<!-- resources/js/Pages/ProductList.vue -->
<template>
    <div class="p-4">
        <h1 class="text-xl mb-4">商品情報一覧</h1>
        <ul>
            <li v-for="product in products" :key="product.id" class="border-b py-2">
                {{ product.name }} - {{ product.price }}円
            </li>
        </ul>
    </div>
</template>

<script setup>
const props = defineProps({
    products: Array
});
</script>
