<template>
    <Head title="پرسنل - ویرایش"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="group"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>ویرایش پرسنل</h1>
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
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12" v-if="form.role === 6">
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
                                    <q-btn type="submit" class="rb-action rb-large" label="ویرایش پرسنل"
                                           @click="updateEmployee"
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
        const employee = ref(page.props.employee.data)
        console.log(employee.value)
        const {form, submitForm, isLoading} = formHandler({
            name: ref(employee.value.name),
            national_code: ref(employee.value.national_code),
            father_name: ref(employee.value.father_name),
            birth_day: ref(employee.value.birth_date.object.day),
            birth_month: ref(employee.value.birth_date.object.month),
            birth_year: ref(employee.value.birth_date.object.year),
            gender: ref(employee.value.gender.value),
            address: ref(employee.value.address),
            mobile: ref(employee.value.mobile),
            mobile2: ref(employee.value.mobile2),
            tel: ref(employee.value.tel),
            category: ref(employee.value.category.id),
            role: ref(employee.value.role.id),
            supervisor: ref(employee.value.supervisor.id),
        },'put');
        console.log(form.gender)
        const updateEmployee = () => {
            submitForm('admin.employees.update',employee.value.id);
        };
        console.log(page.props)
        return {
            updateEmployee,
            form,
            submitForm,
            isLoading,
            category_options: page.props.categories,
            role_options: page.props.roles,
            supervisor_options: page.props.supervisors,
            gender_options: ref(page.props.gender),
            day_options: ref(page.props.days),
            month_options: ref(page.props.months),
            year_options: ref(page.props.years),
            reagent_options: [],
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
