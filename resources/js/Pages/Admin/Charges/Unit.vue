<template>
    <Head title="شارژ واحدها"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                            <div>
                                <q-icon :name="currentSection.icon"/>
                            </div>
                        </div>
                        <div class="rb-title">
                            <h1>{{ currentSection.pageTitle }}</h1>
                            <p>{{ currentSection.pageDescription }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-actions-section">
                        <div class="rb-add-section">
                            <excel-button
                                class="rb-action"
                                label="خروجی پیش فاکتور"
                                :route="route('admin.charges.units.excel')"
                                slug="پیش-فاکتور-شارژ-واحدها"
                                title="فیلتر خروجی پیش فاکتور"
                                btn-title="خروجی"
                                card-class="full-width"
                                :use-dialog="false"
                            />
                            <q-btn class="rb-action" label="پرینت" icon="print" @click="printHandle"/>

                        </div>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-table
                    flat
                    :rows="rows"
                    :columns="columns"
                    :rows-per-page="perPage"
                    :rows-per-page-options="[perPage]"
                    hide-pagination
                    :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                    :loading="isLoading"
                    :key="perPage"
                >
                    <template v-slot:top-right>
                        <div class="rb-filter-section">
                            <div class="row q-col-gutter-md">
                                <div class="col">
                                    <q-input
                                        dense
                                        color="teal-10"
                                        placeholder="شماره واحد"
                                        v-model="search"
                                        @keyup="searchQuery"
                                    >
                                        <template v-slot:append>
                                            <q-icon name="search"/>
                                        </template>
                                    </q-input>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-slot:top-left>
                        <div class="rb-select-section">
                            <FieldSelect
                                v-model="perPage"
                                dense
                                :options="perPages"
                                @change="perPageHandle"
                            />
                        </div>
                    </template>
                    <template v-slot:body="props">
                        <q-tr align="center">
                            <q-td><strong>{{ props.row.number }}</strong></q-td>
                            <q-td><strong>{{ props.row.status.label }}</strong></q-td>
                            <q-td>
                                <strong v-if="props.row.occupant">
                                    {{ props.row.occupant.user.mobile }}
                                    <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]"
                                               v-if="props.row.occupant.user">
                                        مستاجر: {{ props.row.occupant.user.name }}
                                    </q-tooltip>
                                </strong>
                                <strong v-else-if="props.row.owner">
                                    {{ props.row.owner.user.mobile }}
                                    <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]"
                                               v-if="props.row.owner.user">
                                        مالک: {{ props.row.owner.user.name }}
                                    </q-tooltip>
                                </strong>
                            </q-td>
                            <q-td><strong>{{ props.row.meterage }}</strong></q-td>
                            <q-td><strong>{{
                                    numberFormat(props.row.charge_amount ?? props.row.formula_charge_amount)
                                }}</strong></q-td>
                        </q-tr>
                    </template>
                </q-table>
                <q-card-section>
                    <Pagination :pagination="page.props.rows.meta"/>
                </q-card-section>
            </q-card>

        </q-page>
    </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {usePage, Link, Head, router,} from "@inertiajs/vue3";
import {ref} from "vue";
import {formHandler, goToRoute, numberFormat, tableColumns} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {route} from "ziggy-js";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";
import ExcelButton from "@/Components/ExcelButton.vue";

export default {
    methods: {numberFormat,route},
    components: {
        ExcelButton,
        Pagination,
        FieldInput,
        FieldSelect,
        FieldNumber,
        AdminLayout,
        Link,
        Head
    },
    setup() {
        const page = usePage();
        const perPage = ref(page.props.rows.meta.per_page);
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const currentSection = ref(page.props.currentSection || {icon: "", pageTitle: "", pageDescription: ""});
        const rows = ref(page.props.rows.data)
        console.log(rows);
        const isLoading = ref(false);
        const columns = tableColumns(['واحد', 'وضعیت واحد', 'شماره موبایل مالک/مستاجر', 'متراژ', 'مبلغ (ریال)']);

        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.charges.units'), {search: search.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data;
                }
            })
        }, 600);

        const perPageHandle = (value) => {
            perPage.value = value;
            isLoading.value = true
            router.get(route('admin.charges.units'), {search: search.value, perPage: perPage.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data; // داده‌های جدید را بروزرسانی می‌کنیم
                }
            })
        }

        const printHandle = () => {
            window.print()
        }

        return {
            page,
            perPages: page.props.perPages,
            perPage,
            perPageHandle,
            columns,
            rows,
            currentSection,
            isLoading,
            search,
            searchQuery,
            printHandle
        };
    },
};
</script>
<style scoped>
.q-field :deep(.q-field__prepend) {
    padding: 0 0 0 5px;
}

.q-field :deep(.q-field__native) {
    padding: 0;
    font-size: .95rem;
    line-height: 1;
}

.q-field :deep(.q-field__bottom) {
    padding: 5px;
}

.q-row {
    margin: 0 0 15px;
    position: relative;
}

.q-row .q-remove {
    position: absolute;
    top: 0;
    left: 0;
}

.q-table__container :deep(.q-table__bottom--nodata) {
    justify-content: center;
    align-items: center;
    margin: 15px 0;
    font-size: .95rem;
    border: 0;
}

.q-table__container :deep(.q-table__bottom--nodata .q-table__bottom-nodata-icon) {
    margin: 0;
    font-size: 1.5rem;
    color: rgb(0, 75, 65);
}
</style>
