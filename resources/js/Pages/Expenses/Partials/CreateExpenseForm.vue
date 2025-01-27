<script setup>
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    categories: Array,
});

const form = useForm({
    title: '', // The name of the expense.
    description: '', // A short description of the expense.
    amount: '', // The monetary value of the expense.
    category_id: '', // The ID of the category to classify the expense.
    payment_date: '', // The date when the expense was paid.
    frequency: '', // How often the expense occurs (e.g., one-time, monthly, annually).
    type: '', // The type of the expense (e.g., personal or business).
    notes: '', // Additional information or details about the expense.
    attachment: null, // A file attachment related to the expense (e.g., receipt).
});

const submitExpense = () => {
    form.post('/expenses', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <FormSection @submitted="submitExpense">
        <template #title>
            Create Expense
        </template>

        <template #description>
            <p>
                Fill out the form below to add a new expense and track your spending effectively.
            </p>        
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Name" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    placeholder="e.g., Electricity Bill, Office Supplies"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <!-- Category -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="category" value="Category" />
                <select
                    id="category"
                    v-model="form.category_id"
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                >
                    <option value="" disabled>Select a category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <InputError :message="form.errors.category_id" class="mt-2" />
            </div>

            <!-- Description -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="description" value="Description" />
                <TextInput
                    id="description"
                    v-model="form.description"
                    type="text"
                    placeholder="e.g., Monthly internet bill payment"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.description" class="mt-2" />
            </div>

            <!-- Amount -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="amount" value="Amount" />
                <TextInput
                    id="amount"
                    v-model="form.amount"
                    type="number"
                    step="0.01"
                    placeholder="e.g., 123.45"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.amount" class="mt-2" />
            </div>

            <!-- Payment Date -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="payment_date" value="Payment Date" />
                <TextInput
                    id="payment_date"
                    v-model="form.payment_date"
                    type="date"
                    placeholder="Select a date"
                    class="block w-full mt-1"
                />
                <InputError :message="form.errors.payment_date" class="mt-2" />
            </div>

            <!-- Frequency -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="frequency" value="Frequency" />
                <select
                    id="frequency"
                    v-model="form.frequency"
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                >
                    <option value="" disabled>Select frequency</option>
                    <option value="one-time">One-Time</option>
                    <option value="monthly">Monthly</option>
                    <option value="annually">Annually</option>
                </select>
                <InputError :message="form.errors.frequency" class="mt-2" />
            </div>

            <!-- Type -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="type" value="Type" />
                <select
                    id="type"
                    v-model="form.type"
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                >
                    <option value="" disabled>Select a type</option>
                    <option value="reimbursable">Personal</option>
                    <option value="fixed">Business</option>
                </select>
                <InputError :message="form.errors.type" class="mt-2" />
            </div>

            <!-- Notes -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="notes" value="Notes" />
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    placeholder="e.g., Paid via online banking, pending approval"
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                ></textarea>
                <InputError :message="form.errors.notes" class="mt-2" />
            </div>

            <!-- Attachment -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="attachment" value="Attachment" />
                <input
                    id="attachment"
                    type="file"
                    @change="(e) => (form.attachment = e.target.files[0])"
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                />
                <InputError :message="form.errors.attachment" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
