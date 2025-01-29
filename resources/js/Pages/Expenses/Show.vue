<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Propriedades recebidas do servidor
const props = defineProps({
    expense: {
        type: Object,
        required: true,
    },
});

// Configuração do formulário com Inertia.js
const form = useForm({
    _method: 'POST',
    status: props.expense.status || 'pending',
});

// Função para marcar a despesa como paga
const markAsPaid = () => {
    form.post(`/expenses/${props.expense.id}/mark-as-paid`, {
        onSuccess: () => console.log('Expense marked as paid!'),
        onError: (errors) => console.error('Error marking as paid:', errors),
    });
};
</script>

<template>
    <AppLayout :title="`Expense Details - ${expense.title}`">
        <!-- Cabeçalho com botão "Mark as Paid" -->         
    
        <!-- Conteúdo principal -->
        <div class="py-12">
            <div class="flex justify-end items-center max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">                
                <PrimaryButton
                    v-if="expense.status"
                    @click="markAsPaid"
                    :disabled="form.processing"
                >
                    Mark as Paid
                </PrimaryButton>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
                        Expense Details
                    </h3>
                    <!-- Exibição dos detalhes da despesa -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Título -->
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-300">Title</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                {{ expense.title }}
                            </p>
                        </div>
                        <!-- Valor -->
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-300">Amount</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                <!-- ${{ expense.amount.toFixed(2) }} -->
                                 {{ expense.amount }}
                            </p>
                        </div>
                        <!-- Data de Pagamento -->
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-300">Payment Date</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                {{ expense.payment_date
                                    ? new Date(expense.payment_date).toLocaleDateString()
                                    : 'N/A' }}
                            </p>
                        </div>
                        <!-- Status -->
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-300">Status</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100 capitalize">
                                {{ expense.status }}
                            </p>
                        </div>
                        <!-- Notas -->
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Notes</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                {{ expense.notes || 'No additional notes.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
