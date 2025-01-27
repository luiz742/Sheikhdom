<script setup>
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateExpenseForm from './Partials/CreateExpenseForm.vue';

defineProps({
    categories: Array, // Lista de categorias recebida do backend
});

const form = useForm({
    title: '',
    description: '',
    amount: '',
});

const submitExpense = () => {
    form.post('/expenses', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                 Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <CreateExpenseForm :categories="categories"/>
            </div>
        </div>
    </AppLayout>
</template>