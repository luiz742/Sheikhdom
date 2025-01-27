<script setup>
import { ref, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const style = ref('success');
const message = ref('');

watchEffect(() => {
    if (page.props.jetstream.flash?.banner) {
        style.value = page.props.jetstream.flash?.bannerStyle || 'success';
        message.value = page.props.jetstream.flash?.banner || '';
        show.value = true;

        // Ocultar automaticamente após 5 segundos
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
});
</script>

<template>
    <div>
        <!-- Toast Notification -->
        <div v-if="show && message" :class="{
            'bg-green-500': style === 'success',
            'bg-red-600': style === 'danger',
            'bg-yellow-500': style === 'warning',
            'bg-blue-500': style === 'info'
        }"
            class="fixed top-5 right-5 max-w-sm w-full px-4 py-3 rounded-lg shadow-lg flex items-center space-x-4 animate-fade-in z-50">
            <!-- Icon -->
            <div class="flex items-center justify-center w-8 h-8 rounded-full overflow-hidden" :class="{
                'bg-green-700': style === 'success',
                'bg-red-700': style === 'danger',
                'bg-yellow-600': style === 'warning',
                'bg-blue-600': style === 'info'
            }">
                <svg v-if="style === 'success'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>

                <svg v-if="style === 'danger'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>

                <svg v-if="style === 'warning'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856C19.063 20.5 20 19.328 20 18.016V5.984C20 4.672 19.063 3.5 17.918 3.5H6.082C4.937 3.5 4 4.672 4 5.984v12.032c0 1.312.937 2.484 2.082 2.484z" />
                </svg>

                <svg v-if="style === 'info'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>


            </div>

            <!-- Message -->
            <div class="text-sm font-medium text-white">
                {{ message }}
            </div>

            <!-- Close Button -->
            <button @click.prevent="show = false" class="text-white hover:text-gray-200 focus:outline-none transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>

<style>
/* Ajusta a animação e adiciona overflow para SVGs */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10%);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}

.overflow-hidden {
    overflow: hidden;
}
</style>
