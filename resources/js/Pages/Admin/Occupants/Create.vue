<template>
    <Head title="مستاجرین - ایجاد"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8 col-12">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="account_box"/>
                    </span>
                        </div>
                        <div class="rb-title">
                            <h1>افزودن مستاجر</h1>
                            <p>
                                لطفاً اطلاعات را با دقت تکمیل کرده و قیمت‌ها را به ریال ثبت می کنیم.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-back-section">
                        <q-btn type="submit" class="rb-action" flat icon-right="chevron_left" label="بازگشت"  @click="goToRoute(route('admin.occupants'))"/>
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
                                    <h1>اطلاعات عمومی مستاجر</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>نام و نام خانوادگی مستاجر</label>
                            <q-input outlined color="teal-10" v-model="form.name">
                                <template v-slot:prepend>
                                    <q-icon name="contacts"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
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
                            <label>جنسیت</label>
                            <q-select
                                outlined
                                color="teal-10"
                                dropdown-icon="keyboard_arrow_down"
                                v-model="form.gender"
                                :options="gender_options"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="female"/>
                                </template>
                            </q-select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>تلفن ثابت</label>
                            <q-input outlined color="teal-10" v-model="form.tel">
                                <template v-slot:prepend>
                                    <q-icon name="phone_in_talk"/>
                                </template>
                            </q-input>
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
                                    >
                                        <template v-slot:prepend>
                                            <q-icon name="date_range"/>
                                        </template>
                                        <template v-slot:hint></template>
                                    </q-select>
                                </div>
                                <div class="col-lg-4 co-12">
                                    <q-select
                                        outlined
                                        color="teal-10"
                                        dropdown-icon="keyboard_arrow_down"
                                        v-model="form.birth_year"
                                        :options="year_options"
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
                            <label>شماره موبایل(ضروری)</label>
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
                                    <q-btn type="submit" class="rb-action rb-large" label="افزودن مستاجر"
                                           @click="addOccupant"/>
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
import {ref, computed} from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {usePage, Head} from "@inertiajs/vue3";
import {formHandler, goToRoute} from "@/utils/helpers.js";
import {route} from "ziggy-js";
import FieldSelect from "@/Components/FieldSelect.vue";

export default {
    methods: {route, goToRoute},
    components: {
        FieldSelect,
        AdminLayout,
        Head
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
        const addOccupant = () => {
            submitForm('admin.occupants.store');
        };

        return {
            addOccupant,
            form,
            submitForm,
            isLoading,
            unit_options: page.props.units,
            job_options: page.props.jobTypes,
            gender_options: page.props.gender,
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

<style scoped>

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

.q-field .q-hint {
    width: 100%;
    display: block;
    text-align: right;
}
</style>
