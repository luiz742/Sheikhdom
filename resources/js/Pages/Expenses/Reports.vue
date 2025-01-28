<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

// Props fornecidas pelo backend via Inertia
const { props } = usePage();
const expenses = ref(props.expenses || []);
const monthlyStats = ref(props.monthlyStats || []);
const summary = ref(props.summary || {});
const currentMonthYear = new Date().toISOString().slice(0, 7);

// Filtros reativos com useForm para integração com Inertia.js
const filters = useForm({
    monthYear: props.query?.monthYear || currentMonthYear,
    search: props.query?.search || '',
});

// Atualizar despesas filtradas
const updateFilters = () => {
    filters.get(route('admin.reports.index'), {
        preserveState: true, // Preserva o estado entre as mudanças
        preserveScroll: true, // Evita rolagem para o topo
        replace: true, // Substitui a URL em vez de adicionar ao histórico
        onSuccess: (page) => {
            // Atualizar dados baseados nos filtros
            expenses.value = page.props.expenses;
            summary.value = page.props.summary;

            // Os gráficos permanecem fixos
            if (!monthlyStats.value.length) {
                monthlyStats.value = page.props.monthlyStats;
            }
        },
    });
};

// Função para resetar os filtros
const resetFilters = () => {
    filters.monthYear = currentMonthYear;
    filters.search = '';
    updateFilters();
};

// Função para formatar valores monetários
const formatCurrency = value => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

// Inicializar gráfico após renderização
onMounted(() => {
    if (monthlyStats.value.length > 0) {
        const ctx = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyStats.value.map(stat => stat.month),
                datasets: [
                    {
                        label: 'Expenses',
                        data: monthlyStats.value.map(stat => stat.total),
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    }
});
</script>




<template>
    <AppLayout title="Expense Reports">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Expense Reports
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Filtro por Mês/Ano -->
    <div>
        <label for="monthYear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Month & Year
        </label>
        <input
            id="monthYear"
            type="month"
            v-model="filters.monthYear"
            @change="updateFilters"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200"
        />
    </div>

    <!-- Campo de Busca -->
    <div>
        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Search by Title
        </label>
        <input
            id="search"
            type="text"
            v-model="filters.search"
            @input="updateFilters"
            placeholder="Search by title"
            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200"
        />
    </div>

    <!-- Botão de Reset -->
    <div class="flex items-end">
        <button
            @click="resetFilters"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300"
        >
            Reset Filters
        </button>
    </div>
</div>


                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200">Total Expenses</h3>
                        <p class="text-3xl font-bold text-indigo-600">
                            {{ formatCurrency(summary.total) }}
                        </p>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200">Paid</h3>
                        <p class="text-3xl font-bold text-green-600">
                            {{ formatCurrency(summary.paid) }}
                        </p>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200">Pending</h3>
                        <p class="text-3xl font-bold text-yellow-600">
                            {{ formatCurrency(summary.pending) }}
                        </p>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200">Overdue</h3>
                        <p class="text-3xl font-bold text-red-600">
                            {{ formatCurrency(summary.overdue) }}
                        </p>
                    </div>
                </div>

                <!-- Gráfico -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-4">Monthly Expenses</h3>
                    <div class="h-64">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
                
                <!-- Tabela -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-4">Detailed Report</h3>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300">
                                    Title
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300">
                                    Amount
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300">
                                    Payment Date
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="expense in expenses" :key="expense.id" class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ expense.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ formatCurrency(expense.amount) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ expense.payment_date }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-800': expense.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': expense.status === 'pending',
                                            'bg-red-100 text-red-800': expense.status === 'overdue',
                                        }"
                                    >
                                        {{ expense.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
