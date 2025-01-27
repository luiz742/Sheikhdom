<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Chart, registerables } from 'chart.js';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Registrar componentes do Chart.js
Chart.register(...registerables);

// Dados recebidos do backend
const { props } = usePage();
const totalExpenses = ref(props.totalExpenses || 0);
const expensesByCategory = ref(props.expensesByCategory || []);
const monthlyExpenses = ref(props.monthlyExpenses || []);

// Atualizar gráfico de categorias
const initCategoryChart = () => {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: expensesByCategory.value.map((item) => item.name || 'Uncategorized'),
            datasets: [
                {
                    data: expensesByCategory.value.map((item) => item.total || 0),
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
            },
        },
    });
};

// Atualizar gráfico de despesas mensais
const initMonthlyChart = () => {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyExpenses.value.map((item) =>
                new Date(2025, item.month - 1).toLocaleString('default', { month: 'short' })
            ),
            datasets: [
                {
                    label: 'Monthly Expenses',
                    data: monthlyExpenses.value.map((item) => item.total || 0),
                    backgroundColor: '#36A2EB',
                    borderColor: '#36A2EB',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => `$${value}`,
                    },
                },
            },
        },
    });
};

// Inicializa gráficos no mount
onMounted(() => {
    initCategoryChart();
    initMonthlyChart();
});
</script>

<template>
    <AppLayout title="Analytics">
        <div class="py-12 space-y-8 max-w-7xl mx-auto">
            <!-- Estatísticas principais -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 text-center rounded-lg shadow bg-oficial text-white">
                    <h3 class="text-lg font-bold">Total Expenses</h3>
                    <p class="mt-2 text-4xl font-extrabold">${{ totalExpenses }}</p>
                </div>
                <div class="p-6 text-center rounded-lg shadow bg-oficial text-white">
                    <h3 class="text-lg font-bold">Categories</h3>
                    <p class="mt-2 text-4xl font-extrabold">{{ expensesByCategory.length }}</p>
                </div>
                <div class="p-6 text-center rounded-lg shadow bg-oficial text-white">
                    <h3 class="text-lg font-bold">Average Monthly Expenses</h3>
                    <p class="mt-2 text-4xl font-extrabold">
                        ${{ (totalExpenses / (monthlyExpenses.length || 1)).toFixed(2) }}
                    </p>
                </div>
            </div>

            <!-- Gráficos lado a lado -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gráfico de Categorias -->
                <div class="rounded-lg shadow p-6 flex items-center justify-center">
                    <div class="w-1/2">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 text-center">
                            Expenses by Category
                        </h3>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Despesas Mensais -->
                <div class="rounded-lg shadow p-6">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 text-center">
                        Monthly Expenses Overview
                    </h3>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
