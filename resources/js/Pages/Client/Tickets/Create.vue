<template>
    <Head title="مالکین - ایجاد"/>
    <ClientLayout>
        <q-page class="q-pa-md">
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="mail_outline"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>افزودن پیام</h1>
                    <p>می توانید پیام ثبت کنید و منتظر پاسخ پشتیبانی بمانید</p>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-card-section>
                    <div class="row q-col-gutter-sm">
                        <div class="col-lg-8 col-12">
                            <label>موضوع</label>
                            <FieldInput
                                v-model="form.subject"
                            />
                        </div>
                        <div class="col-lg-4 col-12">
                            <label>دسته</label>
                            <FieldSelect
                                v-model="form.category"
                                :options="categories"
                                clearable
                            />
                        </div>
                        <div class="col-12">
                            <label>پیام شما</label>
                            <FieldInput
                                v-model="form.message"
                                type="textarea"
                            />
                        </div>
                        <div class="col-12">
                            <div class="q-my-md">
                                <div class="rb-add">
                                    <q-btn
                                        type="submit"
                                        class="rb-action rb-large"
                                        label="ثبت پیام"
                                        @click="addTicket"
                                        :loading="isLoading"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-page>
    </ClientLayout>
</template>

<script>
import {ref} from 'vue';
import {Head, usePage} from "@inertiajs/vue3";
import {formHandler} from "@/utils/helpers.js";
import FieldInput from "@/Components/FieldInput.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import ClientLayout from "@/Layouts/ClientLayout.vue";

export default {
    components: {
        ClientLayout,
        Head,
        FieldInput,
        FieldSelect
    },

    setup() {
        const page = usePage();

        const {form, submitForm, isLoading} = formHandler({
            subject: ref(''),
            message: ref(''),
            category: ref(''),
        });
        const addTicket = () => {
            try {
                submitForm('client.tickets.store');
            }
            catch (error){

            }
        };

        return {
            addTicket,
            form,
            submitForm,
            isLoading,
            categories: ref(page.props.categories)
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
