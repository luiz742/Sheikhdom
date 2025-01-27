import { defineStore } from 'pinia';

export const useSidebarStore = defineStore('sidebar', {
    state: () => ({
        showingSidebar: false,
    }),
    actions: {
        toggleSidebar() {
            this.showingSidebar = !this.showingSidebar;
        },
    },
});
