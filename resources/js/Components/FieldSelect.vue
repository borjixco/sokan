<template>
    <q-select
        outlined
        color="teal-10"
        dropdown-icon="keyboard_arrow_down"
        v-model="selectedValue"
        :options="options"
        option-label="label"
        option-value="value"
        emit-value
        map-options
        :multiple="multiple"
        @update:model-value="handleChange"
    >
        <template v-slot:prepend>
            <q-icon v-if="icon" :name="icon" />
        </template>
    </q-select>
</template>

<script>
export default {
    name: "FieldSelect",
    props: {
        modelValue: [String, Number, Array, null],
        options: Array,
        icon: {
            type: String,
            default: "",
        },
        multiple: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        selectedValue: {
            get() {
                // اگر multiple فعال باشه و modelValue آرایه نباشه، آرایه خالی برمی‌گردونه
                return this.multiple
                    ? Array.isArray(this.modelValue) ? this.modelValue : []
                    : this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            },
        },
    },
    methods: {
        handleChange(value) {
            this.$emit("update:modelValue", value);
            this.$emit("change", value);
        },
    },
};
</script>

<style>
.q-field :deep(.q-field__append) {
    padding: 0;
}
</style>
