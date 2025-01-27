<script setup>
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    expense: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    _method: 'PUT',
    title: props.expense.title || '',
    amount: String(props.expense.amount || ''),
    payment_date: props.expense.payment_date || '',
    frequency: props.expense.frequency || '',
    type: props.expense.type || '',
    notes: props.expense.notes || '',
    attachment: null,
    status: props.expense.status || 'pending', // Novo campo de status
});

const submitExpense = () => {
    console.log('Submitting form with data:', form.data());
    form.put(`/expenses/${props.expense.id}`, {
        onSuccess: () => console.log('Expense updated successfully.'),
        onError: (errors) => console.error('Validation errors:', errors),
    });
};
</script>

<template>
    <form @submit.prevent="submitExpense" class="space-y-6">
        <!-- Section 1: Expense Information -->
        <FormSection>
            <template #title>
                Expense Information
            </template>

            <template #description>
                Basic details about your expense.
            </template>

            <template #form>
                <!-- Title -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="title" value="Title" />
                    <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="block w-full mt-1"
                        autofocus
                    />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>
            </template>
        </FormSection>

        <!-- Section 2: Additional Details -->
        <FormSection>
            <template #title>
                Additional Details
            </template>

            <template #description>
                Specify the expense details, frequency, and type.
            </template>

            <template #form>
                <!-- Amount -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="amount" value="Amount" />
                    <TextInput
                        id="amount"
                        v-model="form.amount"
                        type="number"
                        step="0.01"
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
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="" disabled>Select a type</option>
                        <option value="fixed">Fixed</option>
                        <option value="reimbursable">Reimbursable</option>
                    </select>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>

                <!-- Status -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="" disabled>Select status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>
            </template>
        </FormSection>

        <!-- Section 3: Notes -->
        <FormSection>
            <template #title>
                Notes
            </template>

            <template #description>
                Add any relevant notes about this expense.
            </template>

            <template #form>
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="notes" value="Notes" />
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        rows="3"
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    ></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>
            </template>
        </FormSection>

        <!-- Section 4: Attachment -->
        <FormSection>
            <template #title>
                Upload Attachment
            </template>

            <template #description>
                Attach a receipt or invoice for this expense.
            </template>

            <template #form>
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="attachment" value="Attachment" />
                    <input
                        id="attachment"
                        type="file"
                        @change="(e) => (form.attachment = e.target.files[0])"
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError :message="form.errors.attachment" class="mt-2" />
                </div>
            </template>
        </FormSection>

        <!-- Save Button -->
        <div class="flex justify-end mt-6">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save Changes
            </PrimaryButton>
        </div>
    </form>
</template>
