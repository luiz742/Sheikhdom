<script setup>
import { ref, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useSidebarStore } from '../../Stores/sidebarStore';

const sidebarStore = useSidebarStore();

const isTeamsDropdownOpen = ref(false);
const teams = ref([]);
const currentTeam = ref(null);
const isDarkMode = ref(false);

const page = usePage();
teams.value = page.props.auth.user.all_teams || [];
currentTeam.value = page.props.auth.user.current_team || null;

// Alterna o tema (light/dark) e armazena no localStorage
const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    const html = document.documentElement;
    if (isDarkMode.value) {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

// Define o tema inicial com base no localStorage ou preferência do sistema
onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDarkMode.value = false;
        document.documentElement.classList.remove('dark');
    }
});

const switchToTeam = (team) => {
    router.put(
        route('current-team.update'),
        { team_id: team.id },
        {
            preserveState: true, // Mantém o estado atual
            preserveScroll: true, // Mantém o scroll
            onSuccess: () => {
                currentTeam.value = team; // Atualiza o time atual localmente
                isTeamsDropdownOpen.value = false; // Fecha o dropdown
            },
            onError: (errors) => {
                console.error("Erro ao trocar o time:", errors);
            },
        }
    );
};
</script>

<template>
    <header
        class="flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 shadow-lg border-b border-orange-500"
    >
        <!-- Sidebar Toggle and Title -->
        <div class="flex items-center">
            <!-- Exibir o ícone apropriado com base no estado do sidebar -->
            <button
                @click="sidebarStore.toggleSidebar"
                class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
            >
                <Bars3Icon v-if="!sidebarStore.showingSidebar" class="w-10 h-6" />
                <XMarkIcon v-else class="w-6 h-6" />
            </button>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 ms-4">
                <slot name="title" />
            </h1>
        </div>

        <!-- Theme Toggle and Team Dropdown -->
        <div class="flex items-center space-x-4">
            <!-- Theme Toggle -->
            <button
                @click="toggleTheme"
                class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
            >
                <svg v-if="isDarkMode" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"></path>
                </svg>
            </button>

            <!-- Team Dropdown -->
            <div class="relative">
                <button
                    @click="isTeamsDropdownOpen = !isTeamsDropdownOpen"
                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md"
                >
                    {{ currentTeam?.name || 'Select Team' }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ms-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div v-if="isTeamsDropdownOpen"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Manage Team
                    </div>
                    <ul>
                        <Link :href="route('teams.show', $page.props.auth.user.current_team.id)"
                            class="block w-full text-left px-4 py-2 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700">
                        Team Settings
                        </Link>

                        <hr class="my-1 border-gray-200 dark:border-gray-600" />
                        <li v-for="team in teams" :key="team.id">
                            <button
                                @click="switchToTeam(team)"
                                class="block w-full text-left px-4 py-2 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700"
                            >
                                {{ team.name }}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
</template>
