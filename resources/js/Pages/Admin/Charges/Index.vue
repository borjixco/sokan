<template>
    <Head title="شارژ واحدها"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row justify-between items-center">
                <div class="col-lg-6">
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
                <div class="col-lg-6">
                    <div class="rb-actions-section">
                        <div class="rb-add-section">
                            <excel-button
                                class="rb-action"
                                label="خروجی شارژ واحدها"
                                :route="route('admin.charges.excel')"
                                slug="شارژ-واحدها"
                                title="فیلتر خروجی شارژ واحدها"
                                btn-title="خروجی"
                                card-class="full-width"
                                :use-dialog="true"
                            >
                                <template #filters="{ filters }">
                                    <div class="row q-col-gutter-sm">
                                        <div class="col-6">
                                            <label>سال</label>
                                            <FieldSelect v-model="filters.year" :options="year_options2"/>
                                        </div>
                                        <div class="col-6">
                                            <label>ماه</label>
                                            <FieldSelect v-model="filters.month" :options="month_options"/>
                                        </div>
                                    </div>
                                </template>
                            </excel-button>


                            <q-btn class="rb-action" label="پرینت" icon="print" @click="printHandle"/>
                            <q-btn type="submit" class="rb-action" label="فرمول شارژ ماهیانه" icon="add"
                                   @click="dialog = true"/>
                            <q-btn type="submit" class="rb-action rb-outline" label="ارسال پیامک شارژ" icon="sms"
                                   @click="smsDialog = true"/>
                        </div>
                    </div>
                </div>
            </div>
            <q-dialog v-model="dialog">
                <q-card class="full-width">
                    <q-card-section>
                        <div class="rb-add-section">
                            <Link :href="route('admin.charges.units')">
                                <q-btn type="submit" class="rb-action rb-large" label="پیش فاکتور"></q-btn>
                            </Link>
                        </div>
                        <div class="row q-col-gutter-sm">
                            <div class="col-6">
                                <label>سال</label>
                                <FieldSelect v-model="form.year" :options="year_options" @change="searchYear"/>
                            </div>
                            <div class="col-6">
                                <label>ماه</label>
                                <FieldSelect v-model="form.month" :options="month_options" @change="searchMonth"/>
                            </div>
                            <div class="col-12">
                                <label>شارژ واحدهای خالی (ریال)</label>
                                <FieldNumber v-model="form.base_amount"/>
                            </div>
                        </div>
                        <br>
                        <div v-if="isLoading2" class="q-mb-md justify-center">
                            <q-spinner size="30px" color="primary"/>
                        </div>
                        <strong>شارژ براساس متراژ</strong>
                        <div v-for="(field, index) in fields" :key="index" class="q-row">
                            <div class="row q-col-gutter-sm">
                                <div class="col-4">
                                    <label>از متراژ (مترمربع)</label>
                                    <FieldNumber v-model="field.from_area"/>
                                </div>
                                <div class="col-4">
                                    <label>تا متراژ (متر مربع)</label>
                                    <FieldNumber v-model="field.to_area"/>
                                </div>
                                <div class="col-4">
                                    <label>مبلغ ثابت (ریال)</label>
                                    <FieldNumber v-model="field.amount"/>
                                </div>
                            </div>
                            <q-btn flat round size="sm" class="q-remove" color="red" icon="close"
                                   @click="removeField(index)"
                                   v-if="fields.length > 1"/>
                        </div>
                        <q-btn round size="sm" color="teal-10" @click="addField" icon="add"/>
                        <div class="rb-add">
                            <q-btn type="submit" class="rb-action rb-large" label="ذخیره" @click="addChargeUnit"
                                   :loading="isLoading"/>
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>
            <q-dialog v-model="smsDialog">
                <q-card class="full-width">
                    <q-card-section>
                        <strong>زمان ارسال پیامک</strong>
                        <div class="row q-col-gutter-sm">
                            <div class="col-lg-4 col-12">
                                <label>سال</label>
                                <FieldSelect
                                    v-model="chargeForm.year"
                                    :options="year_options"
                                />
                            </div>
                            <div class="col-lg-4 col-12">
                                <label>ماه</label>
                                <FieldSelect
                                    v-model="chargeForm.month"
                                    :options="month_options"
                                />
                            </div>
                            <div class="col-lg-4 col-12">
                                <label>روز</label>
                                <FieldSelect
                                    v-model="chargeForm.day"
                                    :options="day_options"
                                />
                            </div>
                        </div>
                        <br>
                        <div class="rb-add">
                            <q-btn type="submit" class="rb-action rb-large" label="ارسال پیامک" @click="addChargeSms"
                                   :loading="isLoading3"/>
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>

            <q-dialog v-model="dialogStatus">
                <q-card class="full-width">
                    <q-card-section>
                        <strong>تغییر وضعیت</strong>
                        <div class="row q-col-gutter-sm">
                            <div class="col-12">
                                <label>وضعیت</label>
                                <FieldSelect v-model="form2.status" :options="status_options"
                                             @change="changeStatusHandle"/>
                            </div>
                            <div class="col-12" v-if="form2.status == 'PAID'">
                                <label>روش پرداخت</label>
                                <FieldSelect v-model="form2.payment_method" :options="payment_method_options"/>
                            </div>
                            <div class="col-12" v-if="form2.status == 'PAID'">
                                <label>شماره پیگیری</label>
                                <FieldInput v-model="form2.referenceId"/>
                            </div>
                        </div>
                        <br>
                        <div class="rb-add">
                            <q-btn type="submit" class="rb-action rb-large" label="تغییر وضعیت" @click="changeStatus"
                                   :loading="isLoading4"/>
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>
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
                                    <span>نتایج: {{ numberFormat(page.props.rows.meta.total) }}</span>
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
                                <q-td><strong>{{ props.row.unit.unit_number }}</strong></q-td>
                                <q-td><strong>{{ props.row.unit.meterage }}</strong></q-td>
                                <q-td>{{ props.row.user.name }}</q-td>
                                <q-td><strong>{{ props.row.amount ? numberFormat(props.row.amount) : '-' }}</strong>
                                </q-td>
                                <q-td dir="ltr">{{ props.row.createdAt.label }}</q-td>
                                <q-td dir="ltr">{{ props.row.period.label }}</q-td>
                                <q-td dir="ltr">{{ props.row.dueDate.label }}</q-td>
                                <q-td>{{ props.row.status.label }}</q-td>
                                <q-td>{{ props.row.paymentMethod.label }}</q-td>
                                <q-td>
                                    <q-btn
                                        flat
                                        round
                                        @click="
                                   dialogStatus = true; chargeId = props.row.id; thisStatus = props.row.status.value; form2.status = thisStatus"
                                        v-if="props.row.status.value !== 'PAID'"
                                    >
                                        <q-tooltip anchor="top middle" self="bottom middle" :offset="[0, 0]">تغییر وضعیت
                                        </q-tooltip>
                                        <q-icon name="check_circle" color="teal-10" size="1rem"></q-icon>
                                    </q-btn>
                                    <Link flat round :href="props.row.transactionLink">
                                        <q-tooltip anchor="top middle" self="bottom middle" :offset="[0,0]">گزارش تراکنش
                                            ها
                                        </q-tooltip>
                                        <q-icon name="payments" color="teal-10" size="1rem"></q-icon>
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
import {usePage, Link, Head, router, usePoll} from "@inertiajs/vue3";
import {ref} from "vue";
import {formHandler, goToRoute, numberFormat, tableColumns} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {route} from "ziggy-js";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";
import {copyToClipboard} from "quasar";
import ExcelButton from "@/Components/ExcelButton.vue";

export default {
    methods: {copyToClipboard, numberFormat, route},
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
        const dialog = ref(false);
        const dialogStatus = ref(false);
        const smsDialog = ref(false);
        const isLoading2 = ref(false);
        const chargeId = ref(null);
        const thisStatus = ref(null);

        const columns = tableColumns(['شناسه', 'واحد', 'متراژ', 'پرداخت کننده', 'مبلغ (ریال)', 'تاریخ ایجاد', 'دوره پرداخت', 'مهلت پرداخت', 'وضعیت', 'روش پرداخت', 'عملیات']);

        const {form, submitForm, isLoading} = formHandler({
            year: ref(page.props.yearNow),
            month: ref(page.props.monthNow),
            base_amount: ref(page.props.chargeSetting ? page.props.chargeSetting.base_amount : null),
            details: ref(),
        }, 'post');

        const {form: chargeForm, submitForm: chargeSubmitForm, isLoading: isLoading3} = formHandler({
            year: ref(page.props.yearNow),
            month: ref(page.props.monthNow),
            day: ref(''),
        }, 'post')

        const {form: form2, submitForm: submitForm2, isLoading: isLoading4} = formHandler({
            status: thisStatus,
            payment_method: ref(),
            referenceId: ref(),
        }, 'post')
        const fields = ref();
        if (page.props.chargeSetting) {
            fields.value = page.props.chargeSetting.details.map(detail => ({
                from_area: detail.from_area,
                to_area: detail.to_area,
                amount: detail.amount
            }));
        } else {
            fields.value = [{from_area: null, to_area: null, amount: ""}];
        }

        const addField = () => {
            fields.value.push({from_area: "", to_area: "", amount: ""});
        };

        const removeField = (index) => {
            if (fields.value.length > 1) {
                fields.value.splice(index, 1);
            }
        };

        const searchYear = () => {
            isLoading2.value = true;
            fetchQuery()
        };

        const searchMonth = () => {
            isLoading2.value = true;
            fetchQuery()
        }

        const fetchedDataCache = ref({});  // ذخیره داده‌های قبلی بر اساس year و month

        const fetchQuery = () => {
            // چک می‌کنیم که آیا قبلاً برای این year و month داده‌ای ذخیره شده یا نه
            const cacheKey = `${form.year}-${form.month}`;
            fields.value = [{from_area: "", to_area: "", amount: ""}];
            form.base_amount = null;
            if (fetchedDataCache.value[cacheKey]) {
                // اگر داده در کش موجود بود، از آن استفاده می‌کنیم
                const data = fetchedDataCache.value[cacheKey];
                fields.value = data.details.map(detail => ({
                    from_area: detail.from_area,
                    to_area: detail.to_area,
                    amount: detail.amount
                }));
                form.base_amount = data.base_amount;
                //console.log('Data loaded from cache');
                isLoading2.value = false;
            } else {
                // اگر داده‌ای در کش نبود، درخواست جدید به سرور می‌زنیم
                fetch(route('admin.charges.setting.search', {'year': form.year, 'month': form.month}))
                    .then(response => response.json())
                    .then(function (result) {
                        isLoading2.value = false;
                        if (result.status == 'success') {
                            if (result.data.details && Array.isArray(result.data.details) && result.data.details.length > 0) {
                                // ذخیره داده‌ها در کش
                                fetchedDataCache.value[cacheKey] = result.data;

                                // مقداردهی به fields و base_amount
                                fields.value = result.data.details.map(detail => ({
                                    from_area: detail.from_area,
                                    to_area: detail.to_area,
                                    amount: detail.amount
                                }));
                                form.base_amount = result.data.base_amount;
                                //console.log('Data loaded from server');
                            } else {
                                // اگر داده‌ها خالی بود یا مشکلی پیش آمد
                                console.warn('No details found or invalid data structure');
                            }
                        }
                    })
                    .catch(error => {
                        isLoading2.value = false;
                        console.error("Error fetching data:", error);
                    });
            }
        };


        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.charges'), {search: search.value}, {
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
            router.get(route('admin.charges'), {search: search.value, perPage: perPage.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data; // داده‌های جدید را بروزرسانی می‌کنیم
                }
            })
        }

        const addChargeUnit = () => {
            form.details = fields;
            submitForm('admin.charges.setting.store');
        };

        const addChargeSms = () => {
            chargeSubmitForm('admin.charges.store', null, route('admin.charges'));
        };


        const changeStatusHandle = (value) => {
            if (value === 'PAID') {
                form2.referenceId = Date.now()
            }
        }

        const changeStatus = () => {
            submitForm2('admin.charges.update.status', chargeId.value, route('admin.charges'));
        };


        usePoll(60000, {
            onStart() {
            },
            onFinish() {
                rows.value = page.props.rows.data
            }
        })

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
            addChargeUnit,
            addChargeSms,
            form,
            chargeForm,
            isLoading,
            year_options: page.props.years,
            year_options2: page.props.years2,
            month_options: page.props.months,
            day_options: Array.from({length: 31}, (_, i) => ({
                value: (i + 1).toString(),
                label: (i + 1).toString()
            })),
            fields,
            addField,
            removeField,
            dialog,
            smsDialog,
            dialogStatus,
            searchYear,
            searchMonth,
            isLoading2,
            isLoading3,
            isLoading4,
            searchQuery,
            search,
            chargeId,
            form2,
            status_options: page.props.status,
            thisStatus,
            changeStatus,
            changeStatusHandle,
            payment_method_options: [
                {'label': 'کارت به کارت', 'value': 'CARD'},
                {'label': 'کارت خوان', 'value': 'POS'},
                {'label': 'نقدی', 'value': 'CASH'}
            ],
            printHandle,
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
