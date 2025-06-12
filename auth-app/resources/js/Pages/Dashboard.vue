<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// 追加: propsでscopesとpermissionsを受け取る
defineProps({
    role: {
        type: String,
        default: '',
    },
    scopes: {
        type: Array,
        default: () => [],
    },
    permissions: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                    <!-- 追加: scopesとpermissionsの表示 -->
                    <div class="p-6">
                        <div>
                            <strong>Role:</strong><br/>
                            <span v-if="role">{{ role }}</span>
                        </div>
                        <div class="mt-4">
                            <strong>Scopes:</strong>
                            <ul>
                                <li v-for="scope in scopes" :key="scope">{{ scope }}</li>
                                <li v-if="!scopes.length">なし</li>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <strong>Permissions:</strong>
                            <ul>
                                <li v-for="permission in permissions" :key="permission">{{ permission }}</li>
                                <li v-if="!permissions.length">なし</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
