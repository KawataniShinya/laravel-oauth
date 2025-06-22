<template>
    <div class="p-8 bg-white min-h-screen flex flex-col items-center justify-center">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-700">ユーザー情報</h1>

        <div class="bg-gray-100 p-6 rounded-lg shadow-md w-full max-w-md">
            <div class="mb-4">
                <p class="text-sm text-gray-500">Subject (sub):</p>
                <p class="text-lg font-mono text-gray-800">{{ sub }}</p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500">名前:</p>
                <p class="text-lg font-semibold text-gray-800">{{ name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500">メールアドレス:</p>
                <p class="text-lg font-medium text-gray-800">{{ email }}</p>
            </div>
        </div>

        <button
            class="mt-8 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
            @click="logout"
        >
            ログアウト
        </button>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    sub: String,
    name: String,
    email: String,
})

const logout = () => {
    router.post('/logout')
}
</script>
