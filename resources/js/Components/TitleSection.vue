<template>
    <div class="zo-title-section">
        <div class="zo-icon">
            <q-icon :name="icon" />
        </div>
        <div class="zo-title">
            <h1>{{ title }}</h1>
            <p>{{ subtitle }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    icon: { type: String, default: 'info' },
    title: { type: String, default: 'عنوان پیش‌فرض' },
    subtitle: { type: String, default: 'زیرعنوان پیش‌فرض' },
})
</script>
