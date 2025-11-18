<template>
    <Head title="تراکنش ها"/>
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
                                <q-td><strong>{{ props.row.id }}</strong></q-td>
                                <q-td>{{ props.row.payableTitle }}</q-td>
                                <q-td>{{ props.row.user.name }}</q-td>
                                <q-td dir="ltr"><strong>{{
                                        props.row.amount ? numberFormat(props.row.amount) : '-'
                                    }}</strong></q-td>
                                <q-td>{{ props.row.transactionId }}</q-td>
                                <q-td>{{ props.row.referenceId }}</q-td>
                                <q-td>{{ props.row.description }}</q-td>
                                <q-td>{{ props.row.cardNumber }}</q-td>
                                <q-td dir="ltr">{{ props.row.createdAt.label }}</q-td>
                                <q-td>{{ props.row.method.label }}</q-td>
                                <q-td>{{ props.row.status.label }}</q-td>
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
import {usePage, Link, Head, router} from "@inertiajs/vue3";
import {ref} from "vue";
import {numberFormat, tableColumns} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {route} from "ziggy-js";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";

export default {
    methods: {numberFormat},
    components: {
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
        const payable_type = ref(new URLSearchParams(uri).get('payable_type'));
        const payable_id = ref(new URLSearchParams(uri).get('payable_id'));
        const currentSection = ref(page.props.currentSection || {icon: "", pageTitle: "", pageDescription: ""});
        const rows = ref(page.props.rows.data)
        const isLoading = ref(false)
        const columns = tableColumns(['شناسه', 'نوع', 'پرداخت کننده', 'ملبغ (ریال)', 'کدتراکنش', 'کد پیگیری', 'توضیحات', 'شماره کارت', 'تاریخ', 'روش پرداخت', 'وضعیت']);

        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.transactions'), {
                search: search.value,
                payable_type: payable_type.value,
                payable_id: payable_id.value
            }, {
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
            router.get(route('admin.transactions'), {
                search: search.value,
                payable_type: payable_type.value,
                payable_id: payable_id.value,
                perPage: perPage.value
            }, {
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
            searchQuery,
            search,
            printHandle
        };
    },
};
</script>
<style scoped>
.q-row {
    margin: 0 0 15px;
    position: relative;
}

.q-row .q-remove {
    position: absolute;
    top: 0;
    left: 0;
}
</style>
