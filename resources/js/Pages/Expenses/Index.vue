<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';

const { props } = usePage();
const expenses = ref(props.expenses || []);
const currentMonthYear = new Date().toISOString().slice(0, 7);

const filters = useForm({
    monthYear: props.query?.monthYear || currentMonthYear,
    search: '', // Campo de busca por título
});

// Atualizar despesas filtradas
const updateFilters = debounce(() => {
    filters.get('/expenses', {
        preserveState: true,
        onSuccess: (page) => {
            expenses.value = page.props.expenses;
        },
    });
}, 300); // Aguarda 300ms após o último input

const resetFilters = () => {
    filters.monthYear = currentMonthYear;
    filters.search = '';
    updateFilters();
};

// Formatar valores como moeda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

// Monitorar mudanças no campo de busca e aplicar o debounce
watch(() => filters.search, () => {
    updateFilters();
});
</script>

<template>
    <AppLayout title="Expenses">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Estatísticas -->
                <div v-if="props.isAdmin" class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Total Expenses</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ formatCurrency(expenses.reduce((sum, expense) => sum + parseFloat(expense.amount || 0), 0)) }}
                    </p>
                </div>

                <!-- Filtros -->
                <div class="flex items-center space-x-4">
                    <div>
                        <label for="monthYear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Month & Year
                        </label>
                        <input
                            v-model="filters.monthYear"
                            type="month"
                            id="monthYear"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                            @change="updateFilters"
                        />
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Search by Title
                        </label>
                        <input
                            v-model="filters.search"
                            type="text"
                            id="search"
                            placeholder="e.g., Office Supplies"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        />
                    </div>
                    <button
                        @click="resetFilters"
                        class="px-4 py-2 bg-oficial text-white rounded-lg shadow hover:bg-oficial mt-6 disabled:opacity-50"
                    >
                        Reset Filters
                    </button>
                </div>

                <!-- Tabela de despesas -->
                <div v-if="expenses.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mt-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Title
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Payment Date
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Frequency
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="expense in expenses" :key="expense.id">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ expense.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ formatCurrency(expense.amount) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.payment_date }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ expense.frequency }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-800': expense.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': expense.status === 'pending',
                                            'bg-red-100 text-red-800': expense.status === 'overdue'
                                        }"
                                    >
                                        {{ expense.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="`/expenses/${expense.id}/edit`" class="text-indigo-600 hover:text-indigo-900 dark:text-white">
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mensagem caso não existam despesas -->
                <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center">
                    <p class="text-gray-500 dark:text-gray-300">No expenses found for the selected period or search criteria.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
