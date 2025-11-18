<template>
    <SettingDevLayout>
        <q-card-section>
            <div class="row q-col-gutter-sm">
                <div class="col-lg-3 col-12">
                    <label>سرویس پیامک</label>
                    <FieldSelect
                        icon="list"
                        :options="drivers"
                        option-value="driver"
                        option-label="title"
                        v-model="form.driver"
                        @update:model-value="onDriverChange"
                    />
                </div>
                <div class="col-lg-9 col-12">
                    <label>توکن</label>
                    <FieldInput
                        v-model="form.token"
                    />
                </div>

                <div class="col-12">
                    <div class="q-my-md">
                        <div class="rb-add">
                            <q-btn
                                type="submit"
                                class="rb-action rb-large"
                                label="ذخیره"
                                @click="submit"
                                :loading="isLoading"
                            />
                        </div>
                    </div>
                </div>

            </div>
        </q-card-section>
    </SettingDevLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage, Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import SettingDevLayout from "@/Layouts/SettingDevLayout.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {ref} from "vue";
import {formHandler} from "@/utils/helpers.js";

export default {
    methods: {route},
    components: {
        FieldSelect,
        FieldInput,
        SettingDevLayout,
        Head,
        Link,
        AdminLayout,
    },
    setup() {
        const page = usePage();
        const drivers = ref(page.props.drivers);
        const driversData = ref(page.props.driversData);
        const driver = ref();
        const currentSection = page.props.currentSection;
        const {form, submitForm, isLoading} = formHandler({
            _token: page.props.csrf_token,
            driver: page.props.driver,
            token: page.props.token,
        },'put');

        const submit = async () => {
            try {
                await submitForm('admin.settings.dev.sms');
                driversData.value = window.formData;
                console.log(window.formData);
            }
            catch (e) {

            }
        };

        const onDriverChange = (newValue) => {
            console.log(driversData);
            console.log(newValue)
            console.log(driversData.value[newValue])
            form.token = driversData.value[newValue].token;
        }

        return {form,isLoading,currentSection,drivers,driver,submit,onDriverChange}
    }
};
</script>
