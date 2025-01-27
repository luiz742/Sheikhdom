<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

// Props definidas
defineProps({
    users: Array,
    teams: Array, // Todos os times disponíveis
});

// Formulário para adicionar um usuário a um time
const form = useForm({
    email: '',
    team_id: '',
    role: 'editor', // Papel padrão é "member"
});

// Função para submeter o formulário
function addUserToTeam() {
    console.log('Submitting form:', form);

    form.post(route('admin.teams.addUser', form.team_id), {
        onSuccess: () => {
            console.log('Form submitted successfully:', form);
            form.reset();
            // alert('User successfully invited to the team!');
        },
        onError: (errors) => {
            console.error('Erro ao enviar o formulário:', errors);
        },
    });
}


// Função para atualizar o papel do usuário
function updateRole(user) {
    const roleForm = useForm({ role: user.role });
    roleForm.put(route('admin.users.assignRole', user.id), {
        onError: (errors) => {
            console.error('Erro ao atualizar papel:', errors);
        },
    });
}

// Função para deletar um usuário
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        form.delete(route('admin.users.destroy', userId));
    }
}
</script>

<template>
    <AppLayout title="User Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                User Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Tabela de usuários -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Manage Roles</h3>
                    <table class="table-auto w-full text-gray-700 dark:text-gray-200">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Name</th>
                                <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Email</th>
                                <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Role</th>
                                <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-100 dark:hover:bg-gray-600">
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    {{ user.name }}
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    {{ user.email }}
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    <select v-model="user.role" @change="updateRole(user)"
                                        class="border rounded px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <option value="1">Admin</option>
                                        <option value="2">Manager</option>
                                        <option value="3">User</option>
                                    </select>
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    <Link :href="route('admin.users.edit', user.id)"
                                        class="text-blue-500 hover:underline">
                                        Edit
                                    </Link>
                                    <button
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 dark:hover:bg-red-800 ml-2"
                                        @click="deleteUser(user.id)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Formulário para convidar usuários para um time -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Invite User to Team</h3>
                    <form @submit.prevent="addUserToTeam" class="space-y-4">
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                            <input type="email" id="email" v-model="form.email"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Enter user's email" required />
                            <span v-if="form.errors.email" class="text-sm text-red-500">
                                {{ form.errors.email }}
                            </span>
                        </div>
                        <div>
                            <label for="team" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select
                                Team</label>
                            <select id="team" v-model="form.team_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="" disabled>Select a team</option>
                                <option v-for="team in teams" :key="team.id" :value="team.id">
                                    {{ team.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.team_id" class="text-sm text-red-500">
                                {{ form.errors.team_id }}
                            </span>
                        </div>
                        <div>
                            <label for="role"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200">Role</label>
                            <select id="role" v-model="form.role"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                                <option value="editor">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <span v-if="form.errors.role" class="text-sm text-red-500">
                                {{ form.errors.role }}
                            </span>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring focus:ring-indigo-200 active:bg-indigo-700 disabled:opacity-25 transition">
                            Invite User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
