<template>
    <Head title="رویدادها"/>
    <AdminLayout>
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
                            <h1>{{ currentSection.pageTitle }}</h1>
                            <p>{{ currentSection.pageDescription }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <q-card>
                <q-card-section>
                    <div class="row q-col-gutter-sm">
                        <div class="col-lg-6 col-12">
                            <label>عنوان رویداد ({{titleLength}}/{{charMaxLen}})</label>
                            <FieldInput
                                outlined
                                icon="edit"
                                v-model="form.title"
                                hint="فقط ۲۰ کاراکتر قابلیت ارسال در پیامک را دارد"
                                @keyup="titleHandle"
                            />
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>مکان برگزاری ({{locationLength}}/{{charMaxLen}})</label>
                            <FieldInput
                                icon="location_on"
                                v-model="form.location"
                                hint="فقط ۲۰ کاراکتر قابلیت ارسال در پیامک را دارد"
                                @keyup="locationHandle"
                            />
                        </div>
                        <div class="col-12">
                            <label>تاریخ برگزاری</label>
                            <div class="row q-col-gutter-sm">
                                <div class="col-lg-3 co-12">
                                    <FieldSelect
                                        outlined
                                        icon="calendar_today"
                                        v-model="form.year"
                                        :options="years"
                                        hint="سال"
                                    />
                                </div>
                                <div class="col-lg-3 co-12">
                                    <FieldSelect
                                        outlined
                                        icon="date_range"
                                        v-model="form.month"
                                        :options="months"
                                        hint="ماه"
                                    />
                                </div>
                                <div class="col-lg-2 co-12">
                                    <FieldSelect
                                        outlined
                                        icon="event"
                                        v-model="form.day"
                                        :options="days"
                                        hint="روز"
                                    />
                                </div>
                                <div class="col-lg-2 co-12">
                                    <FieldSelect
                                        outlined
                                        icon="history"
                                        v-model="form.hour"
                                        :options="hours"
                                        hint="ساعت"
                                    />
                                </div>
                                <div class="col-lg-2 co-12">
                                    <FieldSelect
                                        outlined
                                        icon="update"
                                        v-model="form.min"
                                        :options="minutes"
                                        hint="دقیقه"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>توضیحات کوتاه ({{shortDescriptionLength}}/{{charMaxLen}})</label>
                            <FieldInput
                                icon="edit_note"
                                type="textarea"
                                v-model="form.short_description"
                                hint="فقط ۲۰ کاراکتر قابلیت ارسال در پیامک را دارد"
                                @keyup="shortDescriptionHandle"
                            />
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>توضیحات تکمیلی</label>
                            <FieldInput
                                icon="edit_note"
                                type="textarea"
                                v-model="form.description"
                            />
                        </div>
                        <div class="col-12">
                            <div class="q-my-md">
                                <div class="rb-add">
                                    <q-btn type="submit" class="rb-action rb-large" label="افزودن رویداد" @click="reviewEvent" :loading="isLoading2"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-page>

        <q-dialog v-model="dialog">
            <q-card class="full-width">
                <q-card-section>
                    <div class="row q-col-gutter-sm">
                        <div class="col-12">
                            <label>متن پیامک</label>
                            <FieldInput
                                readonly
                                type="textarea"
                                v-model="textSms"
                            />
                        </div>
                    </div>
                    <br>
                    <div class="rb-add">
                        <q-btn type="submit" class="rb-action rb-large" label="ارسال" @click="addEvent" :loading="isLoading"/>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FieldInput from "@/Components/FieldInput.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import {Head, usePage} from "@inertiajs/vue3";
import {formHandler, tableColumns} from "@/utils/helpers.js";
import {ref,reactive} from "vue";
import {route} from "ziggy-js";
import FieldNumber from "@/Components/FieldNumber.vue";

export default {
    components: {
        FieldNumber,
        Head,
        AdminLayout,
        FieldInput,
        FieldSelect
    },
    setup() {
        const page = usePage();
        const currentSection = page.props.currentSection;
        const shortDescriptionLength = ref(0);
        const titleLength = ref(0);
        const locationLength = ref(0);
        const dialog = ref(false)
        const textSms = ref();
        const charMaxLen = ref(20);

        const fields = reactive({
            title: '',
            year: '',
            month: '',
            day: '',
            hour: '',
            min: '',
            location: '',
            short_description: '',
            description: '',
        });

        // استفاده از فرم مشترک
        const { form, submitForm, isLoading } = formHandler(fields, 'post');

        const shortDescriptionHandle = (event) => {
            if (event.target.value.length > 20) {
                event.target.value = event.target.value.slice(0, 20); // فقط ۲۰ کاراکتر را نگه می‌دارد
            }
            shortDescriptionLength.value = event.target.value.length;
        };
        const titleHandle = (event) => {
            if (event.target.value.length > 20) {
                event.target.value = event.target.value.slice(0, 20); // فقط ۲۰ کاراکتر را نگه می‌دارد
            }
            titleLength.value = event.target.value.length;
        };
        const locationHandle = (event) => {
            if (event.target.value.length > 20) {
                event.target.value = event.target.value.slice(0, 20); // فقط ۲۰ کاراکتر را نگه می‌دارد
            }
            locationLength.value = event.target.value.length;
        };

        const reviewEvent = async () => {
            try {
                await submitForm('admin.events.review', null);
                dialog.value = true;
                textSms.value = window.formData?.sms;
            } catch (error) {
                console.error('خطا در ارسال فرم:', error);
            }
        };

        const addEvent = () => {
            // ارسال فرم به سمت سرور
            submitForm('admin.events.store', null, route('admin.events'));
        };

        return {
            currentSection,
            form,
            years: page.props.years,
            months: page.props.months,
            days: page.props.days,
            hours: page.props.hours,
            minutes: page.props.minutes,
            isLoading,
            addEvent,
            shortDescriptionLength,
            shortDescriptionHandle,
            titleHandle,
            locationHandle,
            dialog,
            reviewEvent,
            textSms,
            charMaxLen,
            titleLength,
            locationLength,
        };
    }

};
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

.q-textarea :deep(.q-field__native) {
    padding: 20px 0 0;
    resize: none;
}
</style>
