<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="container border rounded p-6 bg-white shadow-lg w-full">
            <div class="passport-authorize container border rounded py-10">
                <div class="card-header text-center bg-primary pb-10">
                    <p class="text-4xl font-bold tracking-wide">Authorization Request</p>
                </div>
                <div class="card-body">
                    <!-- Introduction -->
                    <p class="lead text-center mb-4">
                        <strong>{{ client.name }}</strong> is requesting permission to access your account.
                    </p>

                    <!-- Scope List -->
                    <div v-if="scopes.length > 0" class="scopes mb-4">
                        <p class="fw-bold">This application will be able to:</p>
                        <ul class="list-group list-group-flush">
                            <li v-for="scope in scopes" :key="scope.id" class="list-group-item">
                                {{ scope.description }}
                            </li>
                        </ul>
                    </div>

                    <!-- Buttons -->
                    <div class="buttons d-flex justify-content-center gap-3 mt-4">
                        <!-- Approve Form -->
                        <form :action="approveUrl" method="POST">
                            <input type="hidden" name="_token" :value="csrfToken" />
                            <input type="hidden" name="state" :value="request.state" />
                            <input type="hidden" name="client_id" :value="client.id" />
                            <input type="hidden" name="auth_token" :value="authToken" />
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 mx-4 my-2 rounded">
                                Authorize
                            </button>
                        </form>

                        <!-- Deny Form -->
                        <form :action="denyUrl" method="POST">
                            <input type="hidden" name="_token" :value="csrfToken" />
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="state" :value="request.state" />
                            <input type="hidden" name="client_id" :value="client.id" />
                            <input type="hidden" name="auth_token" :value="authToken" />
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 mx-4 my-2 rounded">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Props
const { props } = usePage()

const client = props.client
const user = props.user
const scopes = props.scopes || []
const request = props.request
const authToken = props.authToken

// Laravelから渡されたcsrfTokenをpropsで受け取る
const csrfToken = props.csrfToken || ''

const approveUrl = computed(() => route('passport.authorizations.approve'))
const denyUrl = computed(() => route('passport.authorizations.deny'))
</script>

<style scoped>
.passport-authorize .container {
    margin-top: 30px;
}

.passport-authorize .scopes {
    margin-top: 20px;
}

.passport-authorize .buttons {
    margin-top: 25px;
    text-align: center;
}

.passport-authorize .btn {
    width: 125px;
}

.passport-authorize .btn-approve {
    margin-right: 15px;
}

.passport-authorize form {
    display: inline;
}
</style>
