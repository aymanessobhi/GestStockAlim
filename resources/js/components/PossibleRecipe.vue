<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

let possibleRecipes = ref([]);

onMounted(async () => {
  fetchPossibleRecipes();
});

const fetchPossibleRecipes = async () => {
  try {
    // Make an API call to get possible recipes
    let response = await axios.get("/api/possible-recipes");
    possibleRecipes.value = response.data.possibleRecipes;
  } catch (error) {
    console.error("Error fetching possible recipes:", error);
  }
};

const getProductStatus = (product) => {
  return product.status === 'available' ? 'available' : 'unavailable';
};

const validateRecipe = async (recipeId) => {
  try {
    const response = await axios.post("/api/validate-recipe", { recipeId });
    console.log(response.data);
    router.push('/');
  } catch (error) {
    console.error("Erreur lors de la validation de la recette:", error);
  }
};

const goBack = () => {
  router.go(-1); // Go back to the previous page
};
</script>

<template>
    <div class="container">
      <div class="card__header">
        <div>
          <a class="btn btn-secondary" @click="goBack">
            Back to Stock List
          </a>
        </div>
      </div>
  
      <div class="table card__content">
        <div class="table--heading">
          <p>ID</p>
          <p>Recipe Name</p>
          <p>Products</p>
          <p>Status</p>
        </div>
        <div class="table--items" v-for="item in possibleRecipes" :key="item.id" v-if="possibleRecipes.length > 0">
          <a href="#" class="table--items--transactionId">#{{ item.id }}</a>
          <p>{{ item.name }}</p>
          <ul>
            <li v-for="(product, index) in item.products" :key="index" :class="getProductStatus(product)">
              <span :style="{ color: product.status === 'unavailable' ? 'red' : 'inherit' }">
                {{ product.name }} ||  
              <span v-if="product.quantity_in_recipe > 0"> {{ product.quantity_in_recipe }}</span>
              <span v-else>Unavailable</span>
              </span>
            </li>
          </ul>
          <button @click="validateRecipe(item.id)" class="btn btn-secondary">Valider</button>
        </div>
        <p v-else>No possible recipes available.</p>
      </div>
    </div>
  </template>
  
