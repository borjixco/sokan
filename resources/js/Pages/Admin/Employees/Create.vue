<template>
    <Head title="پرسنل - ایجاد"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="group"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>افزودن پرسنل</h1>
                    <p>
                        لطفاً اطلاعات را با دقت تکمیل کنید.
                    </p>
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
                                    <h1>اطلاعات عمومی پرسنل</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>نام و نام خانوادگی</label>
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
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>شماره موبایل</label>
                            <FieldInput
                                v-model="form.mobile"
                                icon="smartphone"
                            />
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <label>شماره موبایل(ضروری)</label>
                            <FieldInput
                                v-model="form.mobile2"
                                icon="smartphone"
                            />
                        </div>
                        <div class="col-lg-2 col-6 col-12">
                            <label>نقش</label>
                            <FieldSelect
                                v-model="form.role"
                                :options="role_options"
                                icon="manage_accounts"
                            />
                        </div>
                        <div class="col-lg-2 col-6 col-12">
                            <label>گروه کاری</label>
                            <FieldSelect
                                clearable
                                v-model="form.category"
                                :options="category_options"
                                icon="widgets"
                                @change="supervisorHandle"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12" v-if="form.role === 6 && form.category">
                            <label>سرپرست</label>
                            <FieldSelect
                                clearable
                                v-model="form.supervisor"
                                :options="supervisor_options"
                                icon="supervisor_account"
                            />
                        </div>
                        <div class="col-12">
                            <div class="q-my-md">
                                <div class="rb-add">
                                    <q-btn type="submit" class="rb-action rb-large" label="افزودن پرسنل"
                                           @click="addEmployee"
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
import {formHandler} from "@/utils/helpers.js";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
export default {
    components: {
        Head,
        AdminLayout,
        FieldSelect,
        FieldInput
    },

    setup() {
        const page = usePage();
        const supervisor_options = ref(null);
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
            category: ref(''),
            role: ref(''),
            supervisor: ref(''),
        });
        const addEmployee = () => {
            submitForm('admin.employees.store');
        };

        const supervisorHandle = (val) => {
            form.supervisor = '';
            const filteredSupervisors = page.props.supervisors.filter(supervisor => supervisor.category === val);

            if (filteredSupervisors.length > 0) {
                supervisor_options.value = filteredSupervisors
            }
        }

        return {
            addEmployee,
            form,
            submitForm,
            isLoading,
            category_options: page.props.categories,
            role_options: page.props.roles,
            supervisor_options,
            gender_options: ref(page.props.gender),
            day_options: ref(page.props.days),
            month_options: ref(page.props.months),
            year_options: ref(page.props.years),
            reagent_options: [],
            supervisorHandle
        }
    }
}
</script>

<style scoped>

.q-field :deep(.q-field__prepend) {
    padding: 0 0 0 5px;
}

.q-field :deep(.q-field__native) {
    padding: 0;
    font-size: .95rem;
    line-height: 1;
}

.q-field :deep(.q-field__bottom) {
    padding: 5px;
}
</style>
