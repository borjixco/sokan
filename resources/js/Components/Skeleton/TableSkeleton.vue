<template>
    <q-card class="q-pa-md q-mb-md q-rounded-borders">
        <div class="table-skeleton">
            <!-- Header -->
            <div class="row q-col-gutter-md q-mb-sm">
                <div
                    v-for="i in columns"
                    :key="'header-' + i"
                    class="col"
                >
                    <div class="skeleton shimmer" style="height: 30px; width: 100%; border-radius: 4px;"></div>
                </div>
            </div>

            <!-- Rows -->
            <div v-for="row in rows" :key="'row-' + row" class="row q-col-gutter-md q-mb-sm">
                <div
                    v-for="col in columns"
                    :key="'cell-' + row + '-' + col"
                    class="col"
                >
                    <div class="skeleton shimmer" style="height: 36px; width: 100%; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </q-card>
</template>

<script setup>
const columns = 4  // تعداد ستون‌ها
const rows = 10     // تعداد ردیف‌ها
</script>

<style scoped>
.skeleton {
    background: #e0e0e0;
    position: relative;
    overflow: hidden;
}

.shimmer::after {
    content: '';
    position: absolute;
    top: 0;
    right: 100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(to left, transparent, rgba(255,255,255,0.5), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    100% {
        right: -100%;
    }
}
</style>
