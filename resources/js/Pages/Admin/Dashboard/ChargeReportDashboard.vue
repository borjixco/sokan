<template>
    <TitleSection
        v-if="loaded"
        icon="domain_add"
        title="وضعیت پرداختی شارژ واحدها"
        :subtitle="'وضعیت پرداختی شارژ واحدها در '+thisMonth+' ماه'"
    />
    <TitleSectionSkeleton v-else/>
    <q-card v-if="loaded">
        <q-card-section>
            <q-table
                flat
                :rows="rows"
                :columns="columns"
                :rows-per-page="perPage"
                :rows-per-page-options="[perPage]"
                hide-pagination
                :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                class="zo-table"
                :key="perPage"
            >
                <template v-slot:body="props">
                    <q-tr align="center">
                        <q-td>{{props.row.id}}</q-td>
                        <q-td>{{props.row.unit.unit_number}}</q-td>
                        <q-td>
                            <strong>{{ props.row.user.mobile }}</strong>
                        </q-td>
                        <q-td>{{ props.row.unit.meterage }}</q-td>
                        <q-td>
                            <div class="zo-price">
                                <strong>{{ props.row.amount ? numberFormat(props.row.amount) : '-' }}</strong>
                                <small>تومان</small>
                            </div>
                        </q-td>
                        <q-td>
                            <q-badge :class="'zo-'+props.row.status.value">{{ props.row.status.label }}</q-badge>
                        </q-td>
                    </q-tr>
                </template>
            </q-table>

        </q-card-section>
    </q-card>
    <TableSkeleton v-else/>
</template>
<script >
import TableSkeleton from "@/Components/Skeleton/TableSkeleton.vue";
import TitleSectionSkeleton from "@/Components/Skeleton/TitleSectionSkeleton.vue";
import TitleSection from "@/Components/TitleSection.vue";
import ProgressSkeleton from "@/Components/Skeleton/ProgressSkeleton.vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import FieldSelect from "@/Components/FieldSelect.vue";
import {route} from "ziggy-js";
import {computed, ref} from "vue";
import {numberFormat, showNotify, tableColumns} from "@/utils/helpers.js";

export default {
    methods: {numberFormat},
    components: {FieldSelect, Link, TitleSection, ProgressSkeleton, TitleSectionSkeleton, TableSkeleton},

    setup() {
        const loaded = ref(false);
        const page = usePage();
        const rows = ref([]);
        const perPage = ref(10);
        const columns = tableColumns(['شناسه', 'شماره واحد', 'شماره تماس', 'متراژ', 'مبلغ (ریال)', 'وضعیت']);
        const thisMonth = page.props.thisMonth;

        fetch(route('admin.dashboard.reports.charges', {}))
            .then(response => response.json())
            .then(function (res) {
                if (res.status === 'success') {
                    loaded.value = true;
                    rows.value = res.data.rows;
                } else {
                    showNotify(res.message, 'negative');
                }
            });
        return {
            loaded,
            thisMonth,
            rows,
            columns,
            perPage
        }
    }
}
</script>
