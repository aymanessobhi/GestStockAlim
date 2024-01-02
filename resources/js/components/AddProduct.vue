<script setup>
    import { onMounted, ref } from "vue";
    import { useRouter } from "vue-router";

    let AllProducts = ref([]);
    let product_id = ref(null); // Initializing product_id as null
    let quantity = ref(null); // Reference for quantity
    let dateExpiration = ref(null); // Reference for date expiration
    const router = useRouter();

    onMounted(async () => {
        getAllProducts();
    });

    const backToStockList = () => {
        console.log('Navigating to addProduct route...');
        router.push('/');
    };

    const getAllProducts = async () => {
        let response = await axios.get('/api/products');
        AllProducts.value = response.data.products;
    };

    const onSave = async () => {
    if (product_id.value && quantity.value && dateExpiration.value) {
        const data = {
            product_id: product_id.value,
            quantity: quantity.value,
            expiration_date: dateExpiration.value // Update to match your database column name
        };

        try {
            console.log(data);
            await axios.post('/api/addProductInStock', data);
            console.log('Product added successfully!');
            router.push('/');
        } catch (error) {
            console.error('Error adding product:', error);
        }
    } else {
        console.error('Please fill in all the fields');
    }
};

</script>

<template>
    <div class="container">
        <div class="card__header">
            <div>
                <a class="btn btn-secondary" @click="backToStockList">
                    Back to Stock List
                </a>
            </div>
        </div>
        <div class="card__content">
            <div class="card__content--header">
                <div>
                    <p class="my-1">Product</p>
                    <select name="" id="" class="input" v-model="product_id">
                        <option disabled>Select Product</option>
                        <option :value="product.id" v-for="product in AllProducts" :key="product.id">
                            {{ product.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <p class="my-1">Quantity</p> 
                    <input type="number" class="input" v-model="quantity"> 
                </div>
                <div>
                    <p class="my-1">Date expiration</p> 
                    <input id="expiration_date" type="date" class="input" v-model="dateExpiration">
                </div>
                <div>
                    <a class="btn btn-secondary" @click="onSave">
                        Add
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
