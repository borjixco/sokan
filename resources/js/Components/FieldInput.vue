<template>
    <q-input
        outlined
        color="teal-10"
        :model-value="modelValue"
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
    name: "BaseInput",
    props: {
        modelValue: {
            type: [String, Number, null],
            default: "",
        },
        icon: {
            type: String,
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
    },
    methods: {
        // این متد برای به‌روزرسانی مقدار ورودی به والد است
        handleInput(value) {
            this.$emit("update:modelValue", value);  // ارسال مقدار جدید به والد
        },
    },
};
</script>
