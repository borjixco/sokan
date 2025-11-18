<template>
    <Head title="پرسنل"/>
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
                            <Link :href="route('admin.employees.create')">
                                <q-btn type="submit" class="rb-action" label="افزودن کاربر" icon="add"/>
                            </Link>
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
                                        <FieldSelect
                                            clearable
                                            dense
                                            :options="role_options"
                                            v-model="role"
                                            icon=""
                                            @change="searchQuery"
                                        />
                                    </div>
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
                                <q-td><strong>{{ props.row.name }}</strong></q-td>
                                <q-td>{{ props.row.national_code }}</q-td>
                                <q-td>{{ props.row.mobile }}</q-td>
                                <q-td>{{ props.row.category.name }}</q-td>
                                <q-td>{{ props.row.role.label }}</q-td>
                                <q-td class="rb-eye">
                                    <Link :href="route('admin.employees.edit',props.row.id)">
                                        <q-btn flat round>
                                            <q-icon name="visibility" color="teal-10" size="1rem"></q-icon>
                                        </q-btn>
                                    </Link>
                                </q-td>
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
import {Head, usePage, Link, router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import Pagination from "@/Components/Pagination.vue";
import {ref} from "vue";
import {getCsrfToken, goToRoute, numberFormat, showNotify, tableColumns} from "@/utils/helpers.js";
import {debounce} from "lodash";
import FieldSelect from "@/Components/FieldSelect.vue";

export default {
    methods: {numberFormat, route},
    components: {
        Pagination,
        Head,
        AdminLayout,
        Link,
        FieldSelect
    },
    setup() {
        const page = usePage();
        const perPage = ref(page.props.rows.meta.per_page);
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        let role = new URLSearchParams(uri).get('role');
        role = ref(role ? Number(role) : '');
        const isLoading = ref(false);
        const currentSection = page.props.currentSection;
        const columns = tableColumns(['نام', 'کدملی', 'شماره همراه', 'گروه کاری', 'نقش', {
            label: 'عملیات',
            align: 'center',
            headerClasses: 'rb-eye'
        }]);
        const rows = ref(page.props.rows.data);
        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.employees'), {search: search.value, role: role.value}, {
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
            router.get(route('admin.employees'), {search: search.value, role: role.value, perPage: perPage.value}, {
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
            columns,
            rows,
            currentSection,
            search,
            searchQuery,
            isLoading,
            role_options: page.props.roles,
            role,
            printHandle
        };
    }
};
</script>
