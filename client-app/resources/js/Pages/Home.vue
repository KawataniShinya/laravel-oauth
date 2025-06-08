<!-- resources/js/Pages/Home.vue -->
<template>
    <div class="p-4">
        <h1 class="text-xl mb-4">商品情報取得</h1>
        <form @submit.prevent="submit">
            <div class="mb-2">
                <label>ユーザー名</label>
                <input v-model="form.username" class="border p-1 w-full" />
            </div>
            <div class="mb-2">
                <label>パスワード</label>
                <input type="password" v-model="form.password" class="border p-1 w-full" />
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2">商品情報</button>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    username: '',
    password: '',
})

const submit = () => {
    form.post('/products')
}
</script>
