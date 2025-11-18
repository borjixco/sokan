<template>
    <div class="zo-stat-section">
        <TitleSection
            v-if="loaded"
            icon="domain_add"
            title="وضعیت پرداختی قبوض ماهیانه"
            :subtitle="'وضعیت پرداختی قبوض در '+thisMonth+' ماه'"
        />
        <TitleSectionSkeleton v-else/>
        <q-card v-if="loaded">
            <q-card-section>
                <div class="zo-progres-section">
                    <div class="zo-progres">
                        <q-linear-progress
                            size="25px"
                            :value="percent.value+'%'"
                            color="primary"
                            title=""
                        >
                            <q-badge class="zo-badge">
                                <strong>{{ total.toLocaleString() }}</strong>
                                <small>ریال</small>
                                /
                                <strong>{{ paid.toLocaleString() }}</strong>
                                <small>ریال</small>
                            </q-badge>
                        </q-linear-progress>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <ProgressSkeleton v-else/>

    </div>
</template>

<script>
import {setCssVar} from 'quasar'
import TitleSectionSkeleton from "@/Components/Skeleton/TitleSectionSkeleton.vue";
import ProgressSkeleton from "@/Components/Skeleton/ProgressSkeleton.vue";
import TitleSection from "@/Components/TitleSection.vue";
import {computed, ref} from "vue";
import {route} from "ziggy-js";
import {showNotify} from "@/utils/helpers.js";
import {usePage} from "@inertiajs/vue3";
setCssVar('primary', 'rgb(0, 75, 65)')


export default {
    components: {TitleSection, ProgressSkeleton, TitleSectionSkeleton},

    setup() {
        const page = usePage();
        const loaded = ref(false);
        const paid = ref();
        const unpaid = ref();
        const total = ref();
        const percent = ref();
        const thisMonth = ref(page.props.thisMonth);
        fetch(route('admin.dashboard.stats.bill', {}))
            .then(response => response.json())
            .then(function (res) {
                if (res.status === 'success') {
                    loaded.value = true;
                    paid.value = res.data.paid;
                    unpaid.value = res.data.unpaid;
                    total.value = res.data.total;
                    percent.value = computed(() => {
                        return total.value === 0 ? 0 : Math.round((paid.value / total.value) * 100)
                    })
                } else {
                    showNotify(res.message, 'negative');
                }
            });

        return {
            loaded,
            paid,
            unpaid,
            total,
            percent,
            thisMonth
        }
    },
};
</script>
