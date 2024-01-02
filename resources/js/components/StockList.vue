<script setup>
		import {onMounted, ref} from "vue"
        import {useRouter} from "vue-router"

        const router = useRouter()

		let productsInStock = ref([])
	
		onMounted(async ()=>{
			getProductsInStock()
		})
		
		const getProductsInStock = async () => {
		  let response = await axios.get("/api/productsInStock")
		  //console.log('response',response)
		  productsInStock.value = response.data.productsInStock
		}

        const newProductInStock = async () => {
            console.log('Navigating to addProduct route...');
            router.push('/addProduct');
        }

        const possibleRecipe = async () => {
            console.log('Navigating to PossibleRecipe route...');
            router.push('/PossibleRecipe');
        }

	</script>
<template>
    <div class="container">
        
        <div class="card__header">
            <div>
                <h2 class="invoice__title">Stock List</h2>
            </div>
            <div>
                <a class="btn btn-secondary" @click="newProductInStock">
                    Add Product In Stock
                </a>
            </div>
        </div>

        <div class="table card__content">
            <div class="table--filter">
                <span class="table--filter--collapseBtn ">
                    <i class="fas fa-ellipsis-h"></i>
                </span>
                <div class="table--filter--listWrapper">
                    <ul class="table--filter--list">
                        <li>
                            <p class="table--filter--link table--filter--link--active">
                                All
                            </p>
                        </li>
                        <li>
                            <a class="btn btn-secondary" @click="possibleRecipe">
                                Possible Recipe
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="table--heading">
                <p>ID</p>
                <p>Product Name</p>
                <p>Quantity</p>
                <p>Date expiration</p>
            </div>
            <div class="table--items" v-for="item in productsInStock" :key="item.id" v-if="productsInStock.length > 0">
                <a href="#" class="table--items--transactionId">#{{item.id}}</a>
                <p>{{item.product.name}}</p>
                <p>{{item.quantity}}</p>
                <p>{{item.expiration_date}}</p>
            </div>
    </div>
    </div>
</template>


