<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

// Props definidas
defineProps({
    categories: Array, // Lista de categorias recebidas do backend
});

// Formulário para adicionar uma nova categoria
const form = useForm({
    name: '', // Nome da categoria
});

// Função para submeter o formulário
function createCategory() {
    form.post(route('categories.store'), {
        onSuccess: () => form.reset(),
        onError: (errors) => console.error('Erro ao criar categoria:', errors),
    });
}

// Função para deletar uma categoria
function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        form.delete(route('categories.destroy', id));
    }
}
</script>

<template>
    <AppLayout title="Category Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Categories
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Tabela de categorias -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Categories</h3>
                    <table class="table-auto w-full text-gray-700 dark:text-gray-200">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">ID</th>
                                <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Name</th>
                                <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-100 dark:hover:bg-gray-600">
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    {{ category.id }}
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    {{ category.name }}
                                </td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">
                                    <button
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 dark:hover:bg-red-800"
                                        @click="deleteCategory(category.id)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Formulário para criar uma nova categoria -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Create Category</h3>
                    <form @submit.prevent="createCategory" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Category Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Enter category name"
                                required
                            />
                            <span v-if="form.errors.name" class="text-sm text-red-500">
                                {{ form.errors.name }}
                            </span>
                        </div>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring focus:ring-indigo-200 active:bg-indigo-700 disabled:opacity-25 transition"
                        >
                            Save
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
