<template>
    <div class="p-8 bg-gray-100 min-h-screen">
        <h1 class="text-2xl font-bold mb-6 text-center">トークン取得</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Authorization Code Grant -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">Authorization Code Grant</h2>
                <p class="text-gray-700 mb-4">
                    リダイレクト方式でトークンを取得します。
                </p>
                <button
                    @click="authorize"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full"
                >
                    認可リクエストを送る
                </button>
            </div>

            <!-- Password Grant -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold mb-4 text-green-600">Authorization Password Grant</h2>
                <p class="text-gray-700 mb-4">
                    パスワード方式でトークンを取得します。
                </p>
                <form @submit.prevent="submitPassword">
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-medium text-gray-700">パスワード</label>
                        <input
                            type="password"
                            v-model="form.password"
                            class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring focus:border-blue-300"
                            placeholder="password"
                        />
                    </div>
                    <button
                        type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded w-full"
                    >
                        トークンを取得
                    </button>
                </form>
            </div>
        </div>
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
    password: '',
})

const props = defineProps({
    codeGrantUrl: String,
    clientId: String,
    redirectUri: String,
    errorMessage : String,
})

const errorMessage = computed(() => props.errorMessage || '')

const authorize = () => {
    const params = new URLSearchParams({
        client_id: props.clientId,
        redirect_uri: props.redirectUri,
        response_type: 'code',
        scope: '',
        state: 'stateDummy' // 任意の CSRF 対策用のランダム文字列
    })

    window.location.href = `${props.codeGrantUrl}?${params.toString()}`
}

const submitPassword = () => {
    form.post('/auth/password')
}
</script>
