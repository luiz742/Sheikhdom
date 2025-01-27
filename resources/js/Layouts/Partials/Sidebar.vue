<script setup>
import { ref, watchEffect } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { useSidebarStore } from '../../Stores/sidebarStore';
import {
    HomeIcon,
    UserGroupIcon,
    UsersIcon,
    CurrencyDollarIcon,
    FolderIcon,
    ChevronDownIcon,
    XMarkIcon,
    Bars3Icon,
} from '@heroicons/vue/24/outline';

const sidebarStore = useSidebarStore();

// Definir o estado inicial do sidebar com base no tamanho da tela
if (window.innerWidth >= 1024) {
    sidebarStore.showingSidebar = true;
}

const page = usePage();
const teams = ref([]);
const currentTeam = ref(null);

watchEffect(() => {
    teams.value = page.props.auth.user.all_teams || [];
    currentTeam.value = page.props.auth.user.current_team || null;
});
</script>

<template>
    <aside
        class="h-min-screen fixed inset-y-0 left-0 z-20 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 shadow-lg transition-all duration-200 ease-in-out lg:relative lg:translate-x-0"
        :class="sidebarStore.showingSidebar ? 'w-64' : 'w-0'">
        <!-- Header do Sidebar -->
        <div class="h-16 flex items-center justify-between bg-white dark:bg-gray-800 border-b border-orange-500 px-4">
            <div v-if="sidebarStore.showingSidebar" class="truncate w-full">
                <ApplicationMark class="align-middle text-center" />
            </div>

            <!-- Botão para abrir/fechar o Sidebar -->
            <button @click="sidebarStore.toggleSidebar"
                class="text-gray-400 hover:text-gray-600 focus:outline-none lg:hidden">
                <Bars3Icon v-if="!sidebarStore.showingSidebar" class="w-6 h-6" />
                <XMarkIcon v-else class="w-6 h-6" />
            </button>
        </div>

        <!-- Menu -->
        <nav v-if="sidebarStore.showingSidebar" class="mt-6 px-4 space-y-2">
            <!-- Seção de Usuários Normais -->
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">User</h3>
            <Link :href="route('dashboard')"
                :class="route().current('dashboard') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                class="flex items-center px-4 py-2 rounded-lg transition-colors">
            <HomeIcon class="w-6 h-6 me-3" />
            Dashboard
            </Link>

            <Link :href="route('profile.show')"
                :class="route().current('profile.show') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                class="flex items-center px-4 py-2 rounded-lg transition-colors">
            <UsersIcon class="w-6 h-6 me-3" />
            Profile
            </Link>

            <div>
                <button @click="sidebarStore.isExpensesDropdownOpen = !sidebarStore.isExpensesDropdownOpen"
                    class="flex items-center w-full px-4 py-2 text-gray-700 dark:text-gray-200 rounded-lg transition-colors hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white">
                    <CurrencyDollarIcon class="w-6 h-6 me-3" />
                    Expenses
                    <ChevronDownIcon class="w-4 h-4 ms-auto transform transition-transform"
                        :class="sidebarStore.isExpensesDropdownOpen ? 'rotate-180' : ''" />
                </button>
                <div v-if="sidebarStore.isExpensesDropdownOpen" class="mt-2 space-y-1 ps-6">
                    <Link :href="route('expenses.index')"
                        :class="route().current('expenses.index') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                        class="block px-4 py-2 rounded-lg">
                    List Expenses
                    </Link>
                    <Link :href="route('expenses.create')"
                        :class="route().current('expenses.create') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                        class="block px-4 py-2 rounded-lg">
                    Create Expense
                    </Link>
                </div>
            </div>

            <!-- Divisória -->
            <hr class="my-6 border-gray-300 dark:border-gray-600" />

            <!-- Seção de Administradores -->
            <div>
                <h3 class="text-sm pt-2 font-semibold text-gray-500 dark:text-gray-400 uppercase">Admin</h3>
                <div v-if="$page.props.jetstream.canCreateTeams">
                    <button @click="sidebarStore.isTeamsDropdownOpen = !sidebarStore.isTeamsDropdownOpen"
                        class="flex items-center w-full px-4 py-2 text-gray-700 dark:text-gray-200 rounded-lg transition-colors hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white">
                        <UserGroupIcon class="w-6 h-6 me-3" />
                        Teams
                        <ChevronDownIcon class="w-4 h-4 ms-auto transform transition-transform"
                            :class="sidebarStore.isTeamsDropdownOpen ? 'rotate-180' : ''" />
                    </button>
                    <div v-if="sidebarStore.isTeamsDropdownOpen" class="mt-2 space-y-1 ps-6">
                        <Link :href="route('teams.show', currentTeam?.id)"
                            :class="route().current('teams.show') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                            class="block px-4 py-2 rounded-lg">
                        Team Settings
                        </Link>
                        <Link :href="route('teams.create')"
                            :class="route().current('teams.create') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                            class="block px-4 py-2 rounded-lg">
                        Create New Team
                        </Link>
                    </div>

                </div>

                <div v-if="$page.props.auth.user.isAdmin">
                    <button
                        @click="sidebarStore.isUserManagementDropdownOpen = !sidebarStore.isUserManagementDropdownOpen"
                        class="flex items-center w-full px-4 py-2 text-gray-700 dark:text-gray-200 rounded-lg transition-colors hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white">
                        <UsersIcon class="w-6 h-6 me-3" />
                        User Management
                        <ChevronDownIcon class="w-4 h-4 ms-auto transform transition-transform"
                            :class="sidebarStore.isUserManagementDropdownOpen ? 'rotate-180' : ''" />
                    </button>
                    <div v-if="sidebarStore.isUserManagementDropdownOpen" class="mt-2 space-y-1 ps-6">
                        <Link :href="route('admin.users.index')"
                            :class="route().current('admin.users.index') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                            class="block px-4 py-2 rounded-lg">
                        All Users
                        </Link>
                        <Link :href="route('admin.users.edit', { user: $page.props.auth.user.id })"
                            :class="route().current('admin.users.edit') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                            class="block px-4 py-2 rounded-lg">
                        Edit User
                        </Link>
                    </div>
                </div>
                <Link :href="route('categories.index')"
                    :class="route().current('categories.index') ? 'bg-oficial text-white' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'"
                    class="flex items-center px-4 py-2 rounded-lg transition-colors">
                <FolderIcon class="w-6 h-6 me-3" />
                Categories
                </Link>
            </div>

            <Link :href="route('logout')" method="post" as="button"
                class="flex items-center px-4 py-2 rounded-lg transition-colors">
            Log Out
            </Link>
        </nav>
    </aside>
</template>
