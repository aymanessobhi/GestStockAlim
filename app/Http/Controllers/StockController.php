<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stock;
/**
 * @OA\Schema(
 *     schema="Stock",
 *     title="Stock",
 *     description="Stock model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Stock ID"
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         description="Product ID associated with the stock"
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Quantity of the product in stock"
 *     ),
 *     @OA\Property(
 *         property="expiration_date",
 *         type="string",
 *         format="date",
 *         description="Expiration date of the product in stock"
 *     )
 * )
 */
class StockController extends Controller
{
     /**
     * Retrieve products in stock
     *
     * @OA\Get(
     *     path="/api/products-in-stock",
     *     operationId="productsInStock",
     *     tags={"Stock"},
     *     summary="Get products in stock",
     *     description="Returns a list of products in stock.",
     *     @OA\Response(
     *         response=200,
     *         description="List of products in stock retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="productsInStock",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Stock")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
    public function productsInStock()
    {
        $productsInStock = stock::with('product')->orderBy('id','DESC')->get();
        return response()->json([
            'productsInStock' => $productsInStock
        ],200);
    }

    /**
     * Add a product to stock
     *
     * @OA\Post(
     *     path="/api/add-product-to-stock",
     *     operationId="addProductInStock",
     *     tags={"Stock"},
     *     summary="Add a product to stock",
     *     description="Adds a product to the stock with quantity and expiration date.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity", "expiration_date"},
     *             @OA\Property(property="product_id", type="integer", description="ID of the product"),
     *             @OA\Property(property="quantity", type="integer", description="Quantity of the product"),
     *             @OA\Property(property="expiration_date", type="string", format="date", description="Expiration date of the product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product added to stock successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", description="Success message"),
     *             @OA\Property(property="stock", ref="#/components/schemas/Stock")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
    public function addProductInStock(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'product_id' => 'required|numeric',
                'quantity' => 'required|numeric',
                'expiration_date' => 'required|date',
            ]);

            $stock = Stock::create([
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
                'expiration_date' => $validatedData['expiration_date'],
            ]);

            return response()->json(['message' => 'Product added to stock', 'stock' => $stock], 201);
        } catch (\Exception $e) {
            Log::error('Error adding product: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
