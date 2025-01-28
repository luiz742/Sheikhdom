<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
const props = defineProps({
    user: Object,
    teams: Array, // Todos os times disponíveis
    userTeams: Array, // Times que o usuário pertence
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    teams: props.userTeams.map((team) => team.id), // IDs dos times pertencentes
});

function submit() {
    form.put(route('admin.users.update', props.user.id));
}
</script>

<template>
    <AppLayout title="Edit User">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit User: {{ user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                        Update User Information
                    </h3>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Name
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                            />
                            <span v-if="form.errors.name" class="text-sm text-red-500">
                                {{ form.errors.name }}
                            </span>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Email
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                            />
                            <span v-if="form.errors.email" class="text-sm text-red-500">
                                {{ form.errors.email }}
                            </span>
                        </div>

                        <!-- Role -->
                        <div>
                            <label
                                for="role"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Role
                            </label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                            >
                                <option value="1">Admin</option>
                                <option value="2">Manager</option>
                                <option value="3">User</option>
                            </select>
                            <span v-if="form.errors.role" class="text-sm text-red-500">
                                {{ form.errors.role }}
                            </span>
                        </div>

                        <!-- Teams -->
                        <div>
                            <label
                                for="teams"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Teams
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                                <div
                                    v-for="team in teams"
                                    :key="team.id"
                                    class="flex items-center"
                                >
                                    <input
                                        type="checkbox"
                                        :value="team.id"
                                        v-model="form.teams"
                                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 focus:ring-indigo-500 dark:focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                        {{ team.name }}
                                    </span>
                                </div>
                            </div>
                            <span v-if="form.errors.teams" class="text-sm text-red-500">
                                {{ form.errors.teams }}
                            </span>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-ofical text-white font-semibold rounded-md shadow bg-oficial focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
