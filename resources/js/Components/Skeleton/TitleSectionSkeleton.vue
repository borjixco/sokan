<template>
    <q-card class="q-pa-md q-mb-md bg-grey-2 q-rounded-borders" flat>
        <div class="row items-center justify-start q-gutter-md">
            <!-- آواتار سمت راست -->
            <q-skeleton type="QAvatar" size="56px" />

            <!-- متن‌ها سمت چپ آواتار و راست‌چین -->
            <div class="column text-right">
                <q-skeleton type="text" width="160px" height="25px" class="q-mb-sm" />
                <q-skeleton type="text" width="220px" height="16px" />
            </div>
        </div>
    </q-card>
</template>
<script setup lang="ts">
</script>
