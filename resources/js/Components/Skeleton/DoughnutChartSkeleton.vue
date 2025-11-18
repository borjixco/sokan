<template>
    <q-card class="q-pa-md q-mb-md  q-rounded-borders flex flex-center" style="height: 300px;">
        <div class="donut-skeleton">
            <div class="donut-ring"></div>
            <div class="donut-hole"></div>
        </div>
    </q-card>
</template>

<style scoped>
.donut-skeleton {
    position: relative;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
}

.donut-ring {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(
        90deg,
        #e0e0e0 25%,
        #f5f5f5 50%,
        #e0e0e0 75%
    );
    background-size: 400% 100%;
    animation: shimmer 1.5s infinite linear;
}

.donut-hole {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 90px;
    height: 90px;
    background: #fff;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
}

@keyframes shimmer {
    0% {
        background-position: 100% 0;
    }
    100% {
        background-position: -100% 0;
    }
}
</style>
