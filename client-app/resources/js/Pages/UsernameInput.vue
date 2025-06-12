<template>
    <div class="p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">ユーザー名入力</h1>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">ユーザー名</label>
                <input
                    id="username"
                    type="text"
                    v-model="form.username"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                    required
                />
                <div v-if="form.errors.username" class="text-red-600 text-sm mt-1">
                    {{ form.errors.username }}
                </div>
            </div>

            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                :disabled="form.processing"
            >
                送信
            </button>
        </form>

        <!-- トークンクリアボタン -->
        <div class="mt-6 border-t pt-4">
            <button
                @click="clearToken"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 w-full"
            >
                登録済みトークンをクリア
            </button>
        </div>

        <!-- メッセージ表示 -->
        <div v-if="message" class="mt-4 text-blue-500">
            {{ message }}
        </div>
    </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({
    username: '',
})

const props = defineProps({
    message: String,
})

const message = computed(() => props.message || '')

const submit = () => {
    form.post('/fetch-token')
}

const clearToken = () => {
    router.get('/clear-token')
}
</script>
