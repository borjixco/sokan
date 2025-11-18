<template>
    <Head title="مالکین - ویرایش"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8 col-12">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="group"/>
                    </span>
                        </div>
                        <div class="rb-title">
                            <h1>ویرایش مالک</h1>
                            <p>
                                لطفاً اطلاعات را با دقت تکمیل کرده و قیمت‌ها را به ریال ثبت می کنیم.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-back-section">
                        <q-btn type="submit" class="rb-action" flat icon-right="chevron_left" label="بازگشت"  @click="goToRoute(route('admin.owners'))"/>
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
                            <q-input outlined color="teal-10" v-model="form.name">
                                <template v-slot:prepend>
                                    <q-icon name="contacts"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>کد ملی</label>
                            <q-input outlined color="teal-10" v-model="form.national_code">
                                <template v-slot:prepend>
                                    <q-icon name="badge"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <label>نام پدر</label>
                            <q-input outlined color="teal-10" v-model="form.father_name">
                                <template v-slot:prepend>
                                    <q-icon name="supervisor_account"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <label>تلفن ثابت</label>
                            <q-input outlined color="teal-10" v-model="form.tel">
                                <template v-slot:prepend>
                                    <q-icon name="phone_in_talk"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <label>جنسیت</label>
                            <q-select
                                outlined
                                color="teal-10"
                                dropdown-icon="keyboard_arrow_down"
                                v-model="form.gender"
                                :options="gender_options"
                                option-label="label"
                                option-value="value"
                                emit-value
                                map-options
                            >
                                <template v-slot:prepend>
                                    <q-icon name="female"/>
                                </template>
                            </q-select>
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>تاریخ تولد</label>
                            <div class="row q-col-gutter-sm">
                                <div class="col-lg-4 co-12">
                                    <q-select
                                        outlined
                                        color="teal-10"
                                        dropdown-icon="keyboard_arrow_down"
                                        v-model="form.birth_day"
                                        :options="day_options"
                                        option-label="label"
                                        option-value="value"
                                        emit-value
                                        map-options
                                    >
                                        <template v-slot:prepend>
                                            <q-icon name="calendar_today"/>
                                        </template>
                                    </q-select>
                                </div>
                                <div class="col-lg-4 co-12">
                                    <q-select
                                        outlined
                                        color="teal-10"
                                        dropdown-icon="keyboard_arrow_down"
                                        v-model="form.birth_month"
                                        :options="month_options"
                                        option-label="label"
                                        option-value="value"
                                        emit-value
                                        map-options
                                    >
                                        <template v-slot:prepend>
                                            <q-icon name="date_range"/>
                                        </template>
                                    </q-select>
                                </div>
                                <div class="col-lg-4 co-12">
                                    <q-select
                                        outlined
                                        color="teal-10"
                                        dropdown-icon="keyboard_arrow_down"
                                        v-model="form.birth_year"
                                        :options="year_options"
                                        option-label="label"
                                        option-value="value"
                                        emit-value
                                        map-options
                                    >
                                        <template v-slot:prepend>
                                            <q-icon name="event"/>
                                        </template>
                                    </q-select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>آدرس محل سکونت</label>
                            <q-input outlined color="teal-10" v-model="form.address">
                                <template v-slot:prepend>
                                    <q-icon name="location_on"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>شماره موبایل</label>
                            <q-input outlined color="teal-10" v-model="form.mobile">
                                <template v-slot:prepend>
                                    <q-icon name="smartphone"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>شماره همراه(ضروری)</label>
                            <q-input outlined color="teal-10" v-model="form.mobile2">
                                <template v-slot:prepend>
                                    <q-icon name="smartphone"/>
                                </template>
                            </q-input>
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
                                    <q-btn type="submit" class="rb-action rb-large" label="ویرایش مالک"
                                           @click="editOwner" :loading="isLoading"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <Documents
                title="فایل های بارگذاری شده مالکین"
                description="مدارک و فایل های مربوط به مالکین را می توانید بارگذاری نمایید"
                :model-type="modelType"
                :id="user.id"
                :redirect-route="route('admin.owners.edit',user.id)"
            />
        </q-page>
    </AdminLayout>
</template>

<script>
import {ref, computed} from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage} from "@inertiajs/vue3";
import {formHandler, goToRoute} from "@/utils/helpers.js";
import Documents from "@/Components/UploadDocuments.vue";
import {route} from "ziggy-js";
import FieldSelect from "@/Components/FieldSelect.vue";

export default {
    methods: {goToRoute, route},
    components: {
        FieldSelect,
        Documents,
        Head,
        AdminLayout,
    },

    setup() {
        const page = usePage();
        const user = page.props.user.data;
        const {form, submitForm, isLoading} = formHandler({
            name: ref(user.name),
            national_code: ref(user.national_code),
            father_name: ref(user.father_name),
            birth_day: ref(user.birth_date.object.day),
            birth_month: ref(user.birth_date.object.month),
            birth_year: ref(user.birth_date.object.year),
            gender: ref(user.gender.value),
            address: ref(user.address),
            mobile: ref(user.mobile),
            mobile2: ref(user.mobile2),
            tel: ref(user.tel),
            job_type: ref(user.job_type.map((item) => item.value)),
        }, 'put');
        const editOwner = () => {
            submitForm('admin.owners.update', user.id);
        };

        return {
            editOwner,
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
            modelType: ref(page.props.modelType),
            user
        }
    }
}
</script>
