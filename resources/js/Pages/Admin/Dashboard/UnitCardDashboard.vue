<template>
    <q-card class="zo-stat" v-if="loaded">
        <div class="zo-content">
            <div class="zo-icon">
                <q-icon name="domain"/>
            </div>
            <div class="zo-info">
                <strong class="zo-label">واحدهای تجاری</strong>
                <div class="zo-counter">
                    <div>
                        <span>خالی:</span>
                        <strong>{{ empty }}</strong>
                    </div>
                    <div>
                        <span>بهره برداری:</span>
                        <strong>{{ notEmpty }}</strong>
                    </div>
                </div>
            </div>
            <div class="zo-add">
                <Link :href="route('admin.units')">
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
        const empty = ref(0);
        const notEmpty = ref(0);

            fetch(route('admin.dashboard.stats.units', {}))
            .then(response => response.json())
            .then(function (res) {
                if (res.data && res.status === 'success') {
                    loaded.value = true;
                    empty.value = res.data.empty;
                    notEmpty.value = res.data.notEmpty;
                } else {
                    showNotify(res.message, 'negative');
                }
            });


        return {
            loaded,
            empty,
            notEmpty
        };
    },
};
</script>
