<template xmlns="">
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="forum"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>{{ ticket.subject }}</h1>
                    <p>{{ ticket.user.name }}</p>
                </div>
            </div>
            <div class="row q-col-gutter-md">
                <div class="col-lg-8 col-12">
                    <q-card class="q-mb-lg">
                        <q-card-section>
                            <div v-for="item in ticket.messages"
                                 :class="['row',item.type == 'user' ? 'justify-end' : '']">
                                <div class="col-lg-6 col-12">
                                    <div :class="item.type == 'user' ? 'q-support' : 'q-user'">
                                        <q-card>
                                            <q-card-section class="q-content"> {{ item.message }}</q-card-section>
                                        </q-card>
                                        <small dir="ltr">{{ item.createdAt.label }}
                                            {{ item.type == 'support' ? ' | ' + item.user.name : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                    <q-card class="q-mb-lg">
                        <q-card-section>
                            <q-input outlined class="q-mb-sm" type="textarea" color="teal-10" v-model="form.content">
                                <template v-slot:prepend>
                                    <q-icon name="edit_note"/>
                                </template>
                            </q-input>
                            <div class="text-left">
                                <q-btn type="submit" color="teal-10" label="ثبت پاسخ جدید" @click="response"
                                       :loading="isLoading"/>
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
                <div class="col-lg-4 col-12">
                    <q-card class="q-mb-lg">
                        <q-card-section>
                            <q-list dense>
                                <q-item>
                                    <q-item-section>عنوان</q-item-section>
                                    <q-item-section>
                                        <strong class="text-left">{{ ticket.subject }}</strong>
                                    </q-item-section>
                                </q-item>
                                <q-separator spaced inset/>
                                <q-item>
                                    <q-item-section>دسته</q-item-section>
                                    <q-item-section>
                                        <strong class="text-left">{{ ticket.category.name }}</strong>
                                    </q-item-section>
                                </q-item>
                                <q-separator spaced inset/>
                                <q-item>
                                    <q-item-section>کاربر</q-item-section>
                                    <q-item-section>
                                        <strong class="text-left">{{ ticket.user.name }}</strong>
                                    </q-item-section>
                                </q-item>
                                <q-separator spaced inset/>
                                <q-item>
                                    <q-item-section>شناسه تیکت</q-item-section>
                                    <q-item-section>
                                        <strong class="text-left">{{ ticket.id }}</strong>
                                    </q-item-section>
                                </q-item>
                                <q-separator spaced inset/>
                                <q-item>
                                    <q-item-section>وضعیت</q-item-section>
                                    <q-item-section>
                                        <strong
                                            :class="['text-left',statusColor(ticket.status.value)]">{{
                                                ticket.status.label
                                            }}</strong>
                                    </q-item-section>
                                </q-item>
                                <q-separator spaced inset/>
                                <q-item>
                                    <q-item-section>ایجاد تیکت</q-item-section>
                                    <q-item-section>
                                        <strong class="text-left" dir="ltr">{{ ticket.createdAt.label }}</strong>
                                    </q-item-section>
                                </q-item>
                            </q-list>
                            <br>
                            <div class="q-pa-md">
                                <FieldSelect
                                    dense
                                    v-model="form2.status"
                                    :options="ticketStatus"
                                />
                                <q-btn class="full-width q-mt-sm" color="blue-grey-10" label="تغییر وضعیت"
                                       @click="changeStatus" :loading="isLoading2"/>
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </q-page>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {formHandler} from "@/utils/helpers.js";
import FieldSelect from "@/Components/FieldSelect.vue";
import {route} from "ziggy-js";

export default {
    components: {
        Head,
        AdminLayout,
        FieldSelect
    },
    setup() {
        const page = usePage();
        const ticket = page.props.ticket.data;
        console.log(ticket);
        const thisRoute = route('admin.tickets.edit', ticket.id);
        const currentSection = page.props.currentSection;

        const {form, submitForm, isLoading} = formHandler({
            content: ref(''),
        }, 'put');

        const {form: form2, submitForm: statusSubmitForm, isLoading: isLoading2} = formHandler({
            status: ref(ticket.status.value),
        }, 'put')

        const statusColor = (status) => {
            if (status === 'PENDING') {
                return 'text-orange-10';
            } else if (status === 'IN_PROGRESS') {
                return 'text-teal-10';
            } else if (status === 'CLOSED') {
                return 'text-blue-grey-10';
            } else if (status === 'RESPONDED') {
                return 'text-blue-10';
            }

        }

        const response = () => {
            submitForm('admin.tickets.update', ticket.id, thisRoute);
        };

        const changeStatus = () => {
            statusSubmitForm('admin.tickets.update.status', ticket.id, thisRoute);
        }

        return {
            response,
            changeStatus,
            form,
            form2,
            isLoading,
            isLoading2,
            ticket,
            statusColor,
            currentSection,
            model: ref('درحال پیگیری'),
            ticketStatus: page.props.ticketStatus
        }
    }
};
</script>
<style scoped>
.q-user {
    margin: 0 0 15px;
}

.q-user .q-card {
    background: rgba(0, 75, 65, .05)
}

.q-user small {
    display: block;
    padding: 5px 10px;
    color: rgb(125, 125, 125);
    text-align: left;
}

.q-support {
    margin: 0 0 15px;
}

.q-support .q-card {
    background: rgba(0, 75, 65, .90);
    color: rgb(255, 255, 255);
}

.q-support small {
    display: block;
    padding: 5px 10px;
    color: rgb(125, 125, 125);
    text-align: left;
}

.q-field :deep(.q-field__prepend) {
    padding: 0 0 0 5px;
}

.q-field :deep(.q-field__append) {
    padding: 0;
}

.q-field :deep(.q-field__native) {
    resize: none;
}
</style>
