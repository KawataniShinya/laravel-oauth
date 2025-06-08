<!-- resources/js/Pages/ProductList.vue -->
<template>
    <div>
        <h1>商品情報</h1>
        <ul>
            <li v-for="product in products" :key="product.id">
                {{ product.name }} - {{ product.price }}円
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        products: Array,
    },
};
</script>
