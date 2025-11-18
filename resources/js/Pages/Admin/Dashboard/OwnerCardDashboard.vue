<template>
    <q-card class="zo-stat" v-if="loaded">
        <div class="zo-content">
            <div class="zo-icon">
                <q-icon name="group"/>
            </div>
            <div class="zo-info">
                <strong class="zo-label">مالکین</strong>
                <div class="zo-counter">
                    <div>
                        <span>جاری:</span>
                        <strong>{{ current }}</strong>
                    </div>
                    <div>
                        <span>آرشیو:</span>
                        <strong>{{ archive }}</strong>
                    </div>
                </div>
            </div>
            <div class="zo-add">
                <Link :href="route('admin.owners')">
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
        const current = ref(0);
        const archive = ref(0);

        fetch(route('admin.dashboard.stats.owners', {}))
        .then(response => response.json())
        .then(function (res) {
            if (res.status === 'success') {
                loaded.value = true;
                current.value = res.data.current;
                archive.value = res.data.archive;
            } else {
                showNotify(res.message, 'negative');
            }
        });

        return {
            loaded,
            current,
            archive
        };
    },
};
</script>
