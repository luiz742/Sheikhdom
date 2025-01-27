<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
         <!-- Logo para o modo Dark -->
         <img
            class="w-60 hidden dark:block"
            src="/images/sheikhdom-white.png"
            alt="Sheikhdom Logo Dark"
        />

        <!-- Logo para o modo Light -->
        <img
            class="w-60 block dark:hidden"
            src="/images/sheikhdom.png"
            alt="Sheikhdom Logo Light"
        />
    </Link>
</template>
