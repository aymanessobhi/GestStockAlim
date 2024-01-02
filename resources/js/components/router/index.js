import {createRouter, createWebHistory} from "vue-router";
import StockList from '../StockList.vue';
import AddProduct from '../AddProduct.vue';
import PossibleRecipe from '../PossibleRecipe.vue';
import notFound from '../NotFound.vue';
	
	const routes = [
	{	
	   path : '/',
	   component : StockList	
	},
	{	
		path : '/addProduct',
		component : AddProduct	
	 },
	 {	
		path : '/PossibleRecipe',
		component : PossibleRecipe	
	 },
	{
	   path : '/:pathMatch(.*)*',
	   component : notFound
	}
	]
	
 	const router = createRouter({
		history: createWebHistory(),
		routes
	})

export default router