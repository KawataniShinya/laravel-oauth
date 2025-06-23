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

            <div class="mb-4">
                <p class="text-sm text-gray-500">ロールID:</p>
                <p class="text-lg font-medium text-gray-800">{{ roleId }}</p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500">作成日時:</p>
                <p class="text-lg font-medium text-gray-800">{{ createdAt }}</p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500">更新日時:</p>
                <p class="text-lg font-medium text-gray-800">{{ updatedAt }}</p>
            </div>
        </div>

        <!-- ユーザー詳細取得ボタン -->
        <button
            class="mt-8 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
            @click="goToDetail"
        >
            ユーザー詳細を取得
        </button>

        <!-- ログアウトボタン -->
        <button
            class="mt-8 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
            @click="logout"
        >
            ログアウト
        </button>

        <!-- エラーメッセージ表示 -->
        <div v-if="errorMessage" class="mt-4 text-red-500">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import {computed} from "vue";

const props = defineProps({
    sub: String,
    name: String,
    email: String,
    roleId: String,
    createdAt: String,
    updatedAt: String,
    errorMessage: String,
})

const errorMessage = computed(() => props.errorMessage || '')

const logout = () => {
    router.post('/logout')
}

const goToDetail = () => {
    router.get('/userinfo-detail')
}
</script>
