<template>
    <q-card class="q-pa-md q-mb-md rounded-borders" flat bordered>
        <div class="row items-center justify-between q-mb-md">
            <q-skeleton type="QAvatar" size="48px" />
            <q-skeleton  type="text" width="60px" height="28px" />
        </div>

        <div class="row justify-between">
            <q-skeleton type="text" width="160px" height="24px" class="q-mb-sm" />
        </div>
    </q-card>
</template>

<script setup>
</script>

<style scoped>
.rounded-borders {
    border-radius: 16px;
}
</style>
