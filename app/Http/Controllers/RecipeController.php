<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\stock;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB; 

/**
 * @OA\Schema(
 *     schema="Recipe",
 *     title="Recipe",
 *     description="Recipe model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Recipe ID"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Recipe name"
 *     )
 * )
 */
class RecipeController extends Controller
{
    /**
     * Retrieve possible recipes based on available stock
     *
     * @OA\Get(
     *     path="/api/possible-recipes",
     *     operationId="getPossibleRecipes",
     *     tags={"Recipes"},
     *     summary="Get possible recipes based on available stock",
     *     description="Returns possible recipes considering available stock quantities for required products.",
     *     @OA\Response(
     *         response=200,
     *         description="List of possible recipes retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="possibleRecipes",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Recipe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
    public function getPossibleRecipes()
    {
        try {
            // Retrieve all recipes with associated products and their quantities from the pivot table
            $possibleRecipes = Recipe::with(['products' => function ($query) {
                $query->withPivot('quantity');
            }])->get();
    
            // Retrieve all stocks with their associated products
            $productsInStock = Stock::with('product')->get();
    
            foreach ($possibleRecipes as $recipe) {
                foreach ($recipe->products as $product) {
                    // Find the stock entry corresponding to the product
                    $stockedProduct = $productsInStock
                        ->where('product_id', $product->id)
                        ->first();
    
                    // If the product is not in stock for the recipe or quantity is 0
                    if (!$stockedProduct || $stockedProduct->quantity <= 0) {
                        $product->status = 'unavailable';
                    } else {
                        $product->status = 'available';
    
                        // Get the quantity from the pivot table
                        $quantityInRecipe = $product->pivot->quantity ?? 1; // Assuming default quantity is 1 if not specified
    
                        // Add the quantity from recipes_products to the product object
                        $product->quantity_in_recipe = $quantityInRecipe;
                    }
                }
            }
    
            return response()->json(['possibleRecipes' => $possibleRecipes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
/**
 * Validate a recipe and update stock accordingly
 *
 * @OA\Post(
 *     path="/api/validate-recipe",
 *     operationId="validateRecipe",
 *     tags={"Recipes"},
 *     summary="Validate a recipe and update stock",
 *     description="Validates a recipe and updates stock quantities for associated products.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"recipeId"},
 *             @OA\Property(
 *                 property="recipeId",
 *                 type="integer",
 *                 description="ID of the recipe to be validated"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Recipe validated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Success message"
 *             ),
 *             @OA\Property(
 *                 property="updatedAssociatedProducts",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="quantity", type="integer")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server Error"
 *     )
 * )
 */
    public function validateRecipe(Request $request)
    {
        try {
            $recipeId = $request->input('recipeId');
            // Retrieve the recipe by ID
            $recipe = Recipe::findOrFail($recipeId);
            // Start a database transaction
            DB::beginTransaction();
            // Get associated products with their quantities from the pivot table
            $products = $recipe->products()->withPivot('quantity')->get();
            foreach ($products as $product) {
                // Find the stock entry corresponding to the product
                $stockEntry = Stock::where('product_id', $product->id)->first();
                if ($stockEntry && $stockEntry->quantity >= $product->pivot->quantity) {
                    // Decrement the stock quantity by the quantity required for the recipe
                    $stockEntry->quantity -= $product->pivot->quantity;
                    $stockEntry->save();
                } else {
                    // Rollback transaction if any product does not have sufficient stock
                    DB::rollBack();
                    return response()->json(['error' => 'Insufficient stock for the recipe'], 400);
                }
            }
            // Commit the transaction if all updates are successful
            DB::commit();
            // Prepare the response with updated product details including stock quantities
            $updatedProductDetails = [];
            foreach ($products as $product) {
                $stockEntry = Stock::where('product_id', $product->id)->first();
                $updatedProductDetails[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity_in_stock' => $stockEntry ? $stockEntry->quantity : 0,
                    // Add any other relevant product information
                ];
            }
            // Prepare the response with updated product details including stock quantities
            $response = [
                'message' => 'Recipe validated successfully',
                'updatedAssociatedProducts' => $updatedProductDetails,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
