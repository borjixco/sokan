<template>
    <Head title="مستاجرین"/>
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
                            <excel-button
                                class="rb-action"
                                label="خروجی مستاجرین"
                                :route="route('admin.occupants.excel')"
                                slug="خروجی-مستاجرین"
                                title="فیلتر خروجی مستاجرین"
                                btn-title="خروجی"
                                card-class="full-width"
                                :use-dialog="false"
                            />
                            <q-btn class="rb-action" label="پرینت" icon="print" @click="printHandle"/>
                            <Link :href="route('admin.occupants.create')">
                                <q-btn type="submit" class="rb-action" label="افزودن مستاجر" icon="add"/>
                            </Link>
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
                                <span class="q-unit" v-for="(unitNumber,index) in props.row.occupantUnits" :key="index">
                                    {{ unitNumber }}
                                </span>
                            </q-td>
                            <q-td><strong>{{ props.row.name }}</strong></q-td>
                            <q-td>{{ props.row.national_code }}</q-td>
                            <q-td>{{ props.row.mobile }}</q-td>
                            <q-td>
                                {{ props.row.job_type ? props.row.job_type.label : '' }}
                            </q-td>
                            <q-td class="rb-eye">
                                <Link :href="route('admin.occupants.edit',props.row.id)">
                                    <q-btn flat round type="submit">
                                        <q-icon name="visibility" color="teal-10" size="1rem"></q-icon>
                                    </q-btn>
                                </Link>

                            </q-td>
                        </q-tr>
                    </template>
                </q-table>
                <q-card-section>
                    <Pagination :pagination="page.props.rows.meta"/>
                </q-card-section>
            </q-card>
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="drive_folder_upload"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>آپلود فایل اکسل</h1>
                    <p>
                        با کلیک بر روی آیکون + می توانید فایل اکسل خود را آپلود کنید.
                    </p>
                </div>
            </div>
            <div class="q-uploader-section">
                <q-uploader
                    field-name="file"
                    class="q-mb-lg"
                    color="teal-10"
                    :url="uploadUrl"
                    label="حداکثر سایز فایل 1 مگابایت"
                    max-file-size="1024000"
                    accept=".xlsx, .xls, .csv"
                    max-files="1"
                    :form-fields="[{ name: '_token', value: csrfToken }]"
                    @uploaded="uploadExcel"
                />
            </div>
        </q-page>
    </AdminLayout>
</template>
<script>
import {ref, computed} from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, Link, router, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {getCsrfToken, goToRoute, numberFormat, showNotify, tableColumns} from "@/utils/helpers.js";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";
import FieldSelect from "@/Components/FieldSelect.vue";
import ExcelButton from "@/Components/ExcelButton.vue";

export default {
    methods: {numberFormat, goToRoute, route},
    components: {
        ExcelButton,
        FieldSelect,
        Link,
        Pagination,
        Head,
        AdminLayout,
    },
    setup() {
        const uploadUrl = route('admin.occupants.upload');
        const page = usePage();
        const perPage = ref(page.props.rows.meta.per_page);
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const isLoading = ref(false);
        const currentSection = page.props.currentSection;
        const columns = tableColumns(['واحدها', 'نام', 'کدملی', 'شماره همراه', 'شغل', {
            label: 'عملیات',
            align: 'center',
            headerClasses: 'rb-eye'
        }]);
        const rows = ref(page.props.rows.data);

        const uploadExcel = ({xhr}) => {
            const response = JSON.parse(xhr.response);
            showNotify(response.message, response.status == 'success' ? 'positive' : 'negative')
            return goToRoute(route('admin.occupants'))
        }

        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.occupants'), {search: search.value}, {
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
            router.get(route('admin.occupants'), {search: search.value, perPage: perPage.value}, {
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
            csrfToken: getCsrfToken(),
            uploadExcel,
            uploadUrl,
            columns,
            rows,
            currentSection,
            searchQuery,
            search,
            isLoading,
            printHandle,
        };
    },
};
</script>
<style scoped>
.q-table__container :deep(.q-table__bottom--nodata) {
    justify-content: center;
    align-items: center;
    position: relative;
    top: 15px;
    font-size: .95rem;
    border: 0;
}

.q-table__container :deep(.q-table__bottom--nodata .q-table__bottom-nodata-icon) {
    margin: 0;
    font-size: 1.5rem;
    color: rgb(0, 75, 65);
}

.q-unit {
    display: inline-block;
    margin: 0 1.5px;
    padding: 0 10px;
    background: rgba(225, 225, 225, .5);
    border-radius: 300px;
}
</style>
