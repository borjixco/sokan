<template>
    <q-card class="q-pa-md q-mb-md q-rounded-borders shimmer-card" flat bordered>
        <div class="row items-end justify-between" style="height: 350px;">
            <div
                v-for="i in 15"
                :key="i"
                class="shimmer-bar"
                :style="{
          height: barHeights[i - 1] + 'px'
        }"
            />
        </div>
    </q-card>
</template>
<script>

import {onMounted, ref} from "vue";

export default {
    setup() {
        const barHeights = ref([])
        onMounted(() => {
            barHeights.value = Array.from({ length: 15 }, () =>
                Math.floor(Math.random() * 110) + 100
            )
        })

        const chartRef = ref(null);

        return {
            chartRef,
            barHeights
        };
    },
};
</script>

<style scoped>
.q-rounded-borders {
    border-radius: 16px;
}

.shimmer-card {
    overflow: hidden;
    position: relative;
}

.shimmer-bar {
    width: 24px;
    background: #e0e0e0;
    border-radius: 6px;
    position: relative;
    overflow: hidden;
    margin: 0 4px;
}

.shimmer-bar::after {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    height: 100%;
    width: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.5),
        transparent
    );
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}
</style>
