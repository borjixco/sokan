<template>
    <Head title="قراردادها"/>
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
                            <Link :href="route('admin.contracts.create')">
                                <q-btn type="submit" class="rb-action" label="افزودن قرارداد" icon="add"/>
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
                                <q-td dir="ltr">{{ props.row.startAt.label }}</q-td>
                                <q-td dir="ltr">{{ props.row.endAt.label }}</q-td>
                                <q-td>{{ props.row.company }}</q-td>
                                <q-td>
                                    <strong v-if="props.row.cost">{{ numberFormat(props.row.cost) }} </strong>
                                </q-td>
                                <q-td>
                                    <a v-if="props.row.tel" :href="'tel:'+props.row.tel">
                                        <q-btn flat rounded>
                                            <strong>{{ props.row.tel }} </strong>
                                            <q-icon name="smartphone" size="1rem"></q-icon>
                                        </q-btn>
                                    </a>
                                </q-td>
                                <q-td>-</q-td>
                                <q-td class="rb-eye">
                                    <Link :href="route('admin.contracts.edit',props.row.id)">
                                        <q-btn flat round type="submit">
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
import {numberFormat, tableColumns} from "@/utils/helpers.js";
import {ref} from "vue";
import {debounce} from "lodash";
import Pagination from "@/Components/Pagination.vue";
import FieldSelect from "@/Components/FieldSelect.vue";

export default {
    methods: {numberFormat, route},
    components: {
        FieldSelect,
        Pagination,
        Head,
        AdminLayout,
        Link
    },

    setup() {

        const page = usePage();
        const perPage = ref(page.props.rows.meta.per_page);
        const columns = tableColumns(['عنوان', 'تاریخ شروع', 'تاریخ پایان', 'شخص یا شرکت پیمانکار', 'مبلغ (ریال)', 'شماره تماس', 'وضعیت قرارداد', {
            label: 'عملیات',
            align: 'center',
            headerClasses: 'rb-eye'
        }]);
        const rows = ref(page.props.rows.data);
        const currentSection = page.props.currentSection;
        const isLoading = ref(false);
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));

        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.contracts'), {search: search.value}, {
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
            router.get(route('admin.contracts'), {search: search.value, perPage: perPage.value}, {
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
            currentSection,
            perPages: page.props.perPages,
            perPage,
            perPageHandle,
            rows,
            columns,
            page,
            isLoading,
            search,
            searchQuery,
            printHandle
        }
    }
};
</script>
<style scoped>
a {
    color: rgb(0, 75, 65)
}
</style>
