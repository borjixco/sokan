<template>
    <q-input
        outlined
        color="teal-10"
        :model-value="formattedValue"
        @update:model-value="handleInput"
    >
        <template v-slot:hint>
            <div class="row">
                <span class="col-9">
                    <span class="q-hint" v-if="rightHint">{{rightHint}}</span>
                </span>
                <span class="col-3" v-if="leftHint">{{ leftHint }}</span>
            </div>
        </template>

        <template v-slot:prepend>
            <q-icon v-if="icon" :name="icon"/>
        </template>
    </q-input>
</template>

<script>
export default {
    name: "FieldNumber",
    props: {
        modelValue: {
            type: [String, Number, null],
            default: "",
        },
        rightHint: {
            type: String,
            default: "",
        },
        leftHint: {
            type: String,
            default: "",
        },
        icon: {
            type: String,
            default: "",
        },
    },
    computed: {
        formattedValue() {
            // اطمینان از اینکه مقدار همیشه رشته است
            let value = this.modelValue !== null ? String(this.modelValue) : "";

            // تبدیل اعداد فارسی به انگلیسی
            value = value.replace(/[۰-۹]/g, (d) => "۰۱۲۳۴۵۶۷۸۹".indexOf(d));

            // اضافه کردن کاما به صورت سه‌رقمی
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
    },
    methods: {
        handleInput(value) {
            // حذف کاماها قبل از ذخیره مقدار واقعی
            let cleanValue = value.replace(/,/g, "");

            // تبدیل اعداد فارسی به انگلیسی (در اینجا تبدیل فارسی به انگلیسی انجام می‌دهیم)
            cleanValue = cleanValue.replace(/[۰-۹]/g, (d) => "۰۱۲۳۴۵۶۷۸۹".indexOf(d));

            // ارسال مقدار عددی به والد (اگر مقدار خالی بود `null` ذخیره شود)
            this.$emit("update:modelValue", cleanValue ? Number(cleanValue) : null);
        },
    },
};
</script>
