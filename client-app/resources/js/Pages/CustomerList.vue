<!-- resources/js/Pages/CustomerList.vue -->
<template>
    <div class="p-4">
        <h1 class="text-xl mb-4">顧客情報一覧</h1>
        <ul>
            <li v-for="customer in props.customers" :key="customer.id" class="border-b py-2">
                {{ customer.name }} - {{ customer.email }}
            </li>
        </ul>
    </div>
</template>

<script setup>
const props = defineProps({
    customers: Array
})
</script>
