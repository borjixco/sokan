<template>
    <Head title="قبض بدهی" />
    <ClientLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                                <span>
                                    <q-icon :name="currentSection.icon"/>
                                </span>
                        </div>
                        <div class="rb-title">
                            <h1>{{currentSection.pageTitle}}</h1>
                            <p>{{currentSection.pageDescription}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <q-card class="q-mb-lg">
                <q-table
                    flat
                    :rows="rows"
                    :columns="columns"
                    :rows-per-page="page.props.rows.meta.per_page"
                    :rows-per-page-options="[page.props.rows.meta.per_page]"
                    hide-pagination
                    :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                    :loading="isLoading"
                >
                    <template v-slot:top-right>
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
                        </div>
                    </template>
                    <template v-slot:body="props">
                        <q-tr align="center">
                            <q-td>{{props.row.subject}}</q-td>
                            <q-td><strong>{{ props.row.amount ? numberFormat(props.row.amount) : '-' }}</strong></q-td>
                            <q-td dir="ltr">{{ props.row.createdAt.label }}</q-td>
                            <q-td dir="ltr">{{ props.row.dueDate.label }}</q-td>
                            <q-td>{{ props.row.status.label }}</q-td>
                            <q-td>
                                <q-btn flat round @click="payHandle(props.row.id)" v-if="props.row.status.value == 'UNPAID'">
                                    <q-tooltip anchor="top middle" self="bottom middle" :offset="[0,0]">پرداخت</q-tooltip>
                                    <q-icon name="paid" color="teal-10" size="1rem"></q-icon>
                                </q-btn>
                                <Link flat round :href="props.row.transactionLink">
                                    <q-tooltip anchor="top middle" self="bottom middle" :offset="[0,0]">گزارش تراکنش ها</q-tooltip>
                                    <q-icon name="payments" color="teal-10" size="1rem"></q-icon>
                                </Link>
                            </q-td>
                        </q-tr>
                    </template>
                </q-table>
                <q-card-section>
                    <Pagination :pagination="page.props.rows.meta"/>
                </q-card-section>
            </q-card>
        </q-page>
    </ClientLayout>
</template>
<script>
import {Head, Link, router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {formHandler, goToUrl, numberFormat, tableColumns} from "@/utils/helpers.js";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {route} from "ziggy-js";
import FieldNumber from "@/Components/FieldNumber.vue";
import {debounce} from "lodash";
import Pagination from "@/Components/Pagination.vue";
import ClientLayout from "@/Layouts/ClientLayout.vue";

export default {
    methods: {numberFormat},
    components: {
        ClientLayout,
        Link, Pagination,
        FieldNumber,
        FieldInput, FieldSelect,
        Head,
    },
    setup() {
        const page = usePage();
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const currentSection = page.props.currentSection;
        const showDialogBill = ref(false);
        const showDialogUser = ref(false);
        const users = ref(page.props.users || []);
        const rows = ref(page.props.rows.data)
        const columns = tableColumns(['عنوان', 'مبلغ (ریال)', 'تاریخ ایجاد', 'مهلت پرداخت','وضعیت', 'گزارش تراکنش'])
        const userName = ref('');
        console.log(page.props.rows)
        const searchUserQuery = ref('');
        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.bills'), {search: search.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data;
                }
            })
        }, 600);
        const {form, submitForm, isLoading} = formHandler();

        const payHandle = async (billId) => {
            try {
                await submitForm('client.payments.bill',billId)
                goToUrl(window.formData?.url,'_blank');
            } catch (error) {
                console.error('خطا:', error);
            }
        }
        return {
            page,
            currentSection,
            showDialogBill,
            showDialogUser,
            columns,
            users,
            form,
            userName,
            searchUserQuery,
            searchQuery,
            rows,
            search,
            payHandle,
            isLoading
        }
    }
};
</script>
