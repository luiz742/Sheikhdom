<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EditExpenseForm from './Partials/EditExpenseForm.vue';

const props = defineProps({
    expense: Object,
});
</script>

<template>
    <AppLayout title="Edit Expense">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Expense
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Enviando o objeto expense diretamente -->
                <EditExpenseForm :expense="expense" />
            </div>
        </div>
    </AppLayout>
</template>
