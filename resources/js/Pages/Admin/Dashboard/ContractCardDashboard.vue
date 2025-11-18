<template>
    <q-card class="zo-stat" v-if="loaded">
        <div class="zo-content">
            <div class="zo-icon">
                <q-icon name="handshake"/>
            </div>
            <div class="zo-info">
                <strong class="zo-label">قراردادها</strong>
                <div class="zo-counter">
                    <div>
                        <span>کل:</span>
                        <strong>{{total}}</strong>
                    </div>
                </div>
            </div>
            <div class="zo-add">
                <Link :href="route('admin.contracts')">
                    <q-btn flat type="submit" label="افزودن"/>
                </Link>
            </div>
        </div>
    </q-card>
    <CardSkeleton v-else/>
</template>

<script>
import CardSkeleton from "@/Components/Skeleton/CardSkeleton.vue";
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import { ref} from "vue";
import {showNotify} from "@/utils/helpers.js";

export default {
    methods: {route},
    components: {
        CardSkeleton,
        Link,
    },
    setup() {
        const loaded = ref(false);
        const total = ref(0);

        fetch(route('admin.dashboard.stats.contracts', {}))
            .then(response => response.json())
            .then(function (res) {
                if (res.status === 'success') {
                    loaded.value = true;
                    total.value = res.data.total;
                } else {
                    showNotify(res.message, 'negative');
                }
            });

        return {
            loaded,
            total,
        };
    },
};
</script>
