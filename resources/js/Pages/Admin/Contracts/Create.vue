<template>
    <Head title="قراردادها - ایجاد"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="handshake"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>افزودن قرارداد تجاری</h1>
                    <p>لطفاً اطلاعات را با دقت تکمیل کرده و قیمت‌ها را به ریال ثبت کنید.</p>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-card-section>
                    <div class="row q-col-gutter-sm">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>عنوان</label>
                            <FieldInput
                                v-model="form.title"
                                icon="contacts"
                            />
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>شرکت یا شخص پیمانکار</label>
                            <FieldInput
                                v-model="form.company"
                                icon="account_circle"
                            />
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>مبلغ (ریال)</label>
                            <FieldNumber
                                v-model="form.cost"
                                icon="credit_card"
                            />
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>شرایط پرداخت</label>
                            <FieldInput
                                v-model="form.terms"
                                icon="fact_check"
                            />
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>وضعیت</label>
                            <FieldSelect
                                v-model="form.status"
                                icon="checklist"
                            />
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>ضمانت</label>
                            <FieldInput
                                v-model="form.guarantee"
                                icon="new_releases"
                            />
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>شماره تماس</label>
                            <FieldInput
                                v-model="form.tel"
                                icon="smartphone"
                            />
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>تاریخ شروع</label>
                            <div class="row q-col-gutter-sm">
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.start_year"
                                        icon="calendar_today"
                                        :options="years"
                                    />
                                </div>
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.start_month"
                                        icon="date_range"
                                        :options="months"
                                    />
                                </div>
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.start_day"
                                        icon="event"
                                        :options="days"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>تاریخ پایان</label>
                            <div class="row q-col-gutter-sm">
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.end_year"
                                        icon="calendar_today"
                                        :options="years"
                                    />
                                </div>
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.end_month"
                                        icon="date_range"
                                        :options="months"
                                    />
                                </div>
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.end_day"
                                        icon="event"
                                        :options="days"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label>توضیحات تکمیلی</label>
                            <FieldInput
                                type="textarea"
                                v-model="form.description"
                                icon="edit_note"
                            />
                        </div>
                        <div class="col-12">
                            <div class="q-my-md">
                                <div class="rb-add">
                                    <q-btn
                                        type="submit"
                                        class="rb-action rb-large"
                                        label="افزودن قرارداد"
                                        @click="addContract"
                                        :loading="isLoading"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>

        </q-page>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage} from "@inertiajs/vue3";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {ref} from "vue";
import {formHandler} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";

export default {
    components: {
        FieldNumber,
        Head,
        AdminLayout,
        FieldSelect,
        FieldInput
    },

    setup() {
        const page = usePage();
        const {form, submitForm, isLoading} = formHandler({
            title: ref(''),
            company: ref(''),
            cost: ref(''),
            terms: ref(''),
            status: ref(''),
            guarantee: ref(''),
            tel: ref(''),
            start_year: ref(''),
            start_month: ref(''),
            start_day: ref(''),
            end_year: ref(''),
            end_month: ref(''),
            end_day: ref(''),
            description: ref(''),
        });

        const addContract = () => {
            submitForm('admin.contracts.store');
        };

        return {
            form,
            years: ref(page.props.years),
            months: ref(page.props.months),
            days: ref(page.props.days),
            addContract,
            isLoading

        }
    }
}
</script>
<style scoped>

label {
    display: block;
    margin: 0 0 2.5px;
}

.q-field :deep(.q-field__prepend) {
    padding: 0 0 0 5px;
}

.q-field :deep(.q-field__append) {
    padding: 0;
}

.q-field :deep(.q-field__native) {
    padding: 0;
    font-size: .95rem;
    line-height: 1;
}

.q-field :deep(.q-field__bottom) {
    padding: 5px;
}

.q-textarea :deep(.q-field__native) {
    padding: 20px 0 0;
    resize: none;
}
</style>
