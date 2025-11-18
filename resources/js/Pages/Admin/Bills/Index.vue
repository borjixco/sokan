<template>
    <Head title="قبض بدهی"/>
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
                            <q-btn type="submit" class="rb-action" label="صدور قبض" icon="add"
                                   @click="showDialogBill = true"/>
                        </div>
                    </div>
                </div>
            </div>
            <q-dialog v-model="showDialogBill">
                <q-card class="q-mb-lg">
                    <q-card-section>
                        <div class="row q-col-gutter-sm">
                            <div class="col-12">
                                <div class="text-h6">صدور قبض بدهی</div>
                            </div>
                            <div class="col-12">
                                <label>موضوع قبض</label>
                                <FieldInput
                                    v-model="form.title"
                                />
                            </div>
                            <div class="col-12">
                                <label>پرداخت کننده</label>
                                <FieldInput
                                    readonly
                                    @click="showDialogUser = true"
                                    @focus="showDialogUser = true"
                                    :label="userName"
                                />
                            </div>
                            <div class="col-12">
                                <label>مبلغ پرداختی (ریال)</label>
                                <FieldNumber
                                    v-model="form.amount"
                                />
                            </div>
                            <div class="col-12">
                                <label>مهلت پرداخت</label>
                                <div class="row q-col-gutter-sm">
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.year"
                                            :options="day_options"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.month"
                                            :options="month_options"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.day"
                                            :options="year_options"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="q-my-md">
                                    <div class="rb-add">
                                        <q-btn
                                            type="submit"
                                            class="rb-action rb-large"
                                            label="افزودن قبض"
                                            @click="addBill"
                                            :loading="isLoading"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>
            <q-dialog v-model="showDialogUser">
                <q-card style="width: 100%; max-width: 750px">
                    <q-card-section>
                        <div class="row items-center">
                            <div class="col-lg-5 col-12">
                                <q-input
                                    dense
                                    input-class="text-left"
                                    color="teal-10"
                                    label="جستجو"
                                    v-model="searchUserQuery"
                                    @update:modelValue="debouncedSearch"
                                />
                            </div>

                        </div>
                    </q-card-section>
                    <q-card-section class="q-pt-none">
                        <q-table
                            flat
                            :rows="users"
                            :columns="columns"
                            :rows-per-page="10"
                            :rows-per-page-options="[10]"
                            :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                            hide-pagination
                        >
                            <template v-slot:body="props">
                                <q-tr>
                                    <q-td>
                                        <q-checkbox
                                            v-model="props.row.selected"
                                            @update:model-value="() => addUserSelection(props.row)"
                                            color="teal-10"/>
                                    </q-td>
                                    <q-td align="center">{{ props.row.name }}</q-td>
                                    <q-td align="center">{{ props.row.phone }}</q-td>
                                    <q-td align="center">{{ props.row.national_code }}</q-td>
                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </q-dialog>
            <q-card class="q-mb-lg">
                <q-card-section class="q-pa-none">
                    <q-table
                        flat
                        :rows="rows"
                        :columns="columnsMain"
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
                                <q-td>{{ props.row.title }}</q-td>
                                <q-td>{{ props.row.user.name }}</q-td>
                                <q-td>{{ props.row.user.mobile }}</q-td>
                                <q-td><strong>{{ props.row.amount ? numberFormat(props.row.amount) : '-' }}</strong>
                                </q-td>
                                <q-td dir="ltr">{{ props.row.createdAt.label }}</q-td>
                                <q-td dir="ltr">{{ props.row.dueDate.label }}</q-td>
                                <q-td>{{ props.row.status.label }}</q-td>
                                <q-td>
                                    <Link flat round :href="props.row.transactionLink">
                                        <q-icon name="visibility" color="teal-10" size="1rem"></q-icon>
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
import {Head, Link, router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {formHandler, numberFormat, tableColumns} from "@/utils/helpers.js";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {route} from "ziggy-js";
import FieldNumber from "@/Components/FieldNumber.vue";
import {debounce} from "lodash";
import Pagination from "@/Components/Pagination.vue";

export default {
    methods: {numberFormat},
    components: {
        Link, Pagination,
        FieldNumber,
        FieldInput, FieldSelect,
        Head,
        AdminLayout,
    },
    setup() {
        const page = usePage();
        let uri = window.location.search.substring(1);
        const perPage = ref(page.props.rows.meta.per_page);
        const search = ref(new URLSearchParams(uri).get('search'));
        const currentSection = page.props.currentSection;
        const showDialogBill = ref(false);
        const showDialogUser = ref(false);
        const users = ref(page.props.users || []);
        const rows = ref(page.props.rows.data)
        const columnsMain = tableColumns(['موضوع', 'نام','شماره همراه', 'مبلغ', 'تاریخ ثبت', 'مهلت', 'وضعیت', 'گزارش تراکنش'])
        const columns = tableColumns(['انتخاب', 'نام', 'شماره همراه', 'کدملی'])
        const userName = ref('');

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

        const perPageHandle = (value) => {
            perPage.value = value;
            isLoading.value = true
            router.get(route('admin.bills'), {search: search.value, perPage: perPage.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data;
                }
            })
        }

        const {form, submitForm, isLoading} = formHandler({
            _token: page.props.csrf_token,
            title: ref(''),
            amount: ref(null),
            userId: ref(0),
            year: ref(''),
            month: ref(''),
            day: ref(''),
        });

        const searchServer = (query) => {
            fetch(route('admin.bills.users.search', {'q': query}))
                .then(response => response.json())
                .then(function (data) {
                    users.value = data
                });
        };

// اعمال debounce با تأخیر 500 میلی‌ثانیه
        const debouncedSearch = debounce((query) => {
            if (query.trim() !== '') {
                searchServer(query);
            }
        }, 500);


        const addUserSelection = (row) => {
            console.log(row)
            userName.value = row.name;
            users.value = [];
            searchUserQuery.value = '';
            showDialogUser.value = false;
            form.userId = row.id
            console.log(form)
        };


        const addBill = () => {
            submitForm('admin.bills.store', null, route('admin.bills'));
        };

        const printHandle = () => {
            window.print()
        }

        return {
            page,
            perPages: page.props.perPages,
            perPage,
            perPageHandle,
            currentSection,
            showDialogBill,
            showDialogUser,
            columns,
            users,
            addBill,
            form,
            isLoading,
            day_options: ref(page.props.days),
            month_options: ref(page.props.months),
            year_options: ref(page.props.years),
            userName,
            searchUserQuery,
            searchQuery,
            debouncedSearch,
            addUserSelection,
            rows,
            columnsMain,
            search,
            printHandle,
        }
    }
};
</script>
