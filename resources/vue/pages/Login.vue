<script type="ts" setup>
import { computed } from 'vue';
const hello = computed(() => 'Hello World!!! And this is a computed property.');
</script>

<template>
<p class="text-gray-700">{{ hello }}</p>
</template>
