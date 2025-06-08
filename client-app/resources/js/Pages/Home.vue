<template>
    <div class="p-4">
        <h1 class="text-xl mb-4">情報取得</h1>
        <form @submit.prevent="submitProduct">
            <div class="mb-2">
                <label>ユーザー名</label>
                <input v-model="form.username" class="border p-1 w-full" />
            </div>
            <div class="mb-2">
                <label>パスワード</label>
                <input type="password" v-model="form.password" class="border p-1 w-full" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2">商品情報</button>
                <button @click.prevent="submitCustomer" class="bg-green-500 text-white px-4 py-2">顧客情報</button>
            </div>
        </form>

        <!-- エラーメッセージ表示 -->
        <div v-if="errorMessage" class="mt-4 text-red-500">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({
    username: '',
    password: '',
})

const props = defineProps({
    'errorMessage' : String,
})

const errorMessage = computed(() => props.errorMessage || '')

const submitProduct = () => {
    form.post('/products')
}

const submitCustomer = () => {
    form.post('/customers')
}
</script>
