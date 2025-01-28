<script setup>
import Sidebar from './Partials/Sidebar.vue';
import Banner from '@/Components/Banner.vue';
import Header from './Partials/Header.vue';
import { Head } from '@inertiajs/vue3';
defineProps({ title: String });
</script>

<template>
    <Head :title="title" />
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
        <Sidebar />
        <div class="flex-1 flex flex-col">
            <Banner />
            <Header>
                <template #title>{{ title }}</template>
            </Header>
            <main class="flex-1 p-6 bg-gray-100 dark:bg-gray-900">
                <slot />
            </main>
        </div>
    </div>
</template>
