<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Props definidas
const props = defineProps({
    users: Array, // Lista de usuários
});

// Campo de busca
const searchQuery = ref('');

// Computed para filtrar os usuários pelo nome
const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return props.users;
    }
    return props.users.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

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
        const deleteForm = useForm();
        deleteForm.delete(route('admin.users.destroy', userId));
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
                <!-- Filtro por nome -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-4">
                    <label for="searchQuery" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Search by Name
                    </label>
                    <input
                        id="searchQuery"
                        type="text"
                        v-model="searchQuery"
                        placeholder="Type a name to search"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200"
                    />
                </div>

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
                            <tr
                                v-for="user in filteredUsers"
                                :key="user.id"
                                class="hover:bg-gray-100 dark:hover:bg-gray-600"
                            >
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    {{ user.name }}
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    {{ user.email }}
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    <select
                                        v-model="user.role"
                                        @change="updateRole(user)"
                                        class="border rounded px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                                    >
                                        <option value="1">Admin</option>
                                        <option value="2">Manager</option>
                                        <option value="3">User</option>
                                    </select>
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    <Link
                                        :href="route('admin.users.edit', user.id)"
                                        class="text-blue-500 hover:underline"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 dark:hover:bg-red-800 ml-2"
                                        @click="deleteUser(user.id)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Mensagem caso não existam usuários -->
                    <div v-if="filteredUsers.length === 0" class="text-center mt-4">
                        <p class="text-gray-500 dark:text-gray-300">No users found matching your criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
