<template>
    <Head title="رویدادها"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row justify-between items-center">
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
                            <q-btn class="rb-action" label="پرینت" icon="print" @click="printHandle"/>
                            <q-btn type="submit" class="rb-action" label="افزودن رویداد" icon="add"
                                   @click="goToRoute(route('admin.events.create'))"/>
                        </div>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-card-section class="q-pa-none">
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
                                            placeholder="جستجو"
                                            v-model="search"
                                            @keyup="searchQuery"
                                        >
                                            <template v-slot:append>
                                                <q-icon name="search"/>
                                            </template>
                                        </q-input>
                                    </div>

                                    <span>نتایج: {{numberFormat(page.props.rows.meta.total)}}</span>
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
                                <q-td>
                                    <strong>{{ props.row.title }}</strong>
                                </q-td>
                                <q-td>{{ props.row.location }}</q-td>
                                <q-td dir="ltr">{{ props.row.eventDate.label }}</q-td>
                                <q-td>{{ props.row.short_description }}</q-td>
                                <q-td>{{ props.row.description }}</q-td>
                            </q-tr>
                        </template>
                    </q-table>
                    <Pagination :pagination="page.props.rows.meta"/>
                </q-card-section>
            </q-card>
        </q-page>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FieldInput from "@/Components/FieldInput.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import {Head, router, usePage} from "@inertiajs/vue3";
import {formHandler, goToRoute, numberFormat, tableColumns} from "@/utils/helpers.js";
import {ref, reactive} from "vue";
import {route} from "ziggy-js";
import FieldNumber from "@/Components/FieldNumber.vue";
import {debounce} from "lodash";
import Pagination from "@/Components/Pagination.vue";

export default {
    methods: {
        numberFormat,
        goToRoute,
        route
    },
    components: {
        Pagination,
        FieldNumber,
        Head,
        AdminLayout,
        FieldInput,
        FieldSelect
    },
    setup() {
        const page = usePage();
        const perPage = ref(page.props.rows.meta.per_page);
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const currentSection = page.props.currentSection;
        const columns = tableColumns(['عنوان', 'مکان', 'تاریخ برگزاری', 'توضیحات کوتاه', 'توضیحات']);
        const rows = ref(page.props.rows.data);
        const isLoading = ref(false);
        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.events'), {search: search.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data; // داده‌های جدید را بروزرسانی می‌کنیم
                }
            })
        }, 600);
        const perPageHandle = (value) => {
            perPage.value = value;
            isLoading.value = true
            router.get(route('admin.events'), {search: search.value, perPage: perPage.value}, {
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
            currentSection,
            years: page.props.years,
            months: page.props.months,
            days: page.props.days,
            hours: page.props.hours,
            minutes: page.props.minutes,
            isLoading,
            columns,
            rows,
            search,
            searchQuery,
            printHandle
        };
    }

};
</script>
<style scoped>
.q-table__container :deep(.q-table__bottom--nodata) {
    justify-content: center;
    align-items: center;
    margin: 15px 0;
    padding: 0;
    position: relative;
    font-size: .95rem;
    border: 0;
}

.q-table__container :deep(.q-table__bottom--nodata .q-table__bottom-nodata-icon) {
    margin: 0;
    font-size: 1.5rem;
    color: rgb(0, 75, 65);
}
</style>
