<template>
    <Head title="مالکین - ایجاد"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="group"/>
                    </span>
                        </div>
                        <div class="rb-title">
                            <h1>افزودن مالک</h1>
                            <p>لطفاً اطلاعات را با دقت تکمیل کرده و قیمت‌ها را به ریال ثبت می کنیم.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-back-section">
                        <q-btn type="submit" class="rb-action" flat icon-right="chevron_left" label="بازگشت" @click="goToRoute(route('admin.owners'))"/>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-card-section>
                    <div class="row q-col-gutter-sm">
                        <div class="col-12">
                            <div class="rb-subtitle-section">
                                <div class="rb-icon">
                                    <span>
                                        <q-icon name="contacts"/>
                                    </span>
                                </div>
                                <div class="rb-subtitle">
                                    <h1>اطلاعات عمومی مالک</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>نام و نام خانوادگی مالک</label>
                            <FieldInput
                                v-model="form.name"
                                icon="contacts"
                            />
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <label>کد ملی</label>
                            <FieldInput
                                v-model="form.national_code"
                                icon="badge"
                            />
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <label>نام پدر</label>
                            <FieldInput
                                v-model="form.father_name"
                                icon="supervisor_account"
                            />
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <label>جنسیت</label>
                            <FieldSelect
                                v-model="form.gender"
                                :options="gender_options"
                                icon="female"
                            />
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>تلفن ثابت</label>
                            <FieldInput
                                v-model="form.tel"
                                icon="phone_in_talk"
                            />
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>تاریخ تولد</label>
                            <div class="row q-col-gutter-sm">
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.birth_day"
                                        :options="day_options"
                                        icon="calendar_today"
                                    />
                                </div>
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.birth_month"
                                        :options="month_options"
                                        icon="date_range"
                                    />
                                </div>
                                <div class="col-lg-4 co-12">
                                    <FieldSelect
                                        v-model="form.birth_year"
                                        :options="year_options"
                                        icon="event"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>آدرس محل سکونت</label>
                            <FieldInput
                                v-model="form.address"
                                icon="location_on"
                            />
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>شماره موبایل</label>
                            <FieldInput
                                v-model="form.mobile"
                                icon="smartphone"
                            />
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>شماره موبایل(ضروری)</label>
                            <FieldInput
                                v-model="form.mobile2"
                                icon="smartphone"
                            />
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>شغل</label>
                            <FieldSelect
                                multiple
                                clearable
                                v-model="form.job_type"
                                :options="job_options"
                                icon="widgets"
                            />
                        </div>
                        <div class="col-12">
                            <div class="q-my-md">
                                <div class="rb-add">
                                    <q-btn
                                        type="submit"
                                        class="rb-action rb-large"
                                        label="افزودن مالک"
                                        @click="addOwner"
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
import {ref} from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage} from "@inertiajs/vue3";
import {formHandler, goToRoute} from "@/utils/helpers.js";
import FieldInput from "@/Components/FieldInput.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import {route} from "ziggy-js";

export default {
    methods: {route, goToRoute},
    components: {
        Head,
        AdminLayout,
        FieldInput,
        FieldSelect
    },

    setup() {
        const page = usePage();

        const {form, submitForm, isLoading} = formHandler({
            name: ref(''),
            national_code: ref(''),
            father_name: ref(''),
            birth_day: ref(''),
            birth_month: ref(''),
            birth_year: ref(''),
            gender: ref(''),
            address: ref(''),
            mobile: ref(''),
            mobile2: ref(''),
            tel: ref(''),
            job_type: ref(''),
        });
        const addOwner = () => {
            submitForm('admin.owners.store');
        };

        return {
            addOwner,
            form,
            submitForm,
            isLoading,
            unit_options: page.props.units,
            job_options: page.props.jobTypes,
            gender_options: page.props.gender,
            model_gender_options: ref(null),
            lawyer_options: page.props.lawyers,
            model_lawyer_options: ref(null),
            day_options: Array.from({length: 31}, (_, i) => ({
                value: (i + 1).toString(),
                label: (i + 1).toString()
            })),
            month_options: Array.from({length: 12}, (_, i) => ({
                value: (i + 1).toString(),
                label: (i + 1).toString()
            })),
            year_options: page.props.years,
            reagent_options: [],
            model_reagent_options: ref(null),
        }
    }
}
</script>
