<template>
    <q-dialog v-model="dialogVisible">
        <div class="q-uploader-section">
            <q-card class="full-width">
                <q-card-section>
                    <label>عنوان</label>
                    <FieldInput
                        v-model="form2.title"
                        bottom-slots
                        icon="edit_note"
                    />
                    <label>دسته بندی</label>
                    <q-select
                        outlined
                        color="teal-10"
                        dropdown-icon="keyboard_arrow_down"
                        v-model="form2.category"
                        :options="categories"
                        option-label="label"
                        option-value="value"
                        emit-value
                        map-options
                    >
                        <template v-slot:prepend>
                            <q-icon name="checklist"/>
                        </template>
                    </q-select>
                    <br>
                    <div class="rb-add">
                        <q-btn
                            type="submit"
                            class="rb-action rb-large"
                            label="ویرایش فایل"
                            @click="updateDocument"
                            :loading="isLoading2"
                        />
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </q-dialog>
    <div class="rb-title-section">
        <div class="rb-icon">
            <span>
                <q-icon color="teal-10" name="folder_open"/>
            </span>
        </div>
        <div class="rb-title">
            <h1>{{ title }}</h1>
            <p>{{ description }}</p>
        </div>
    </div>
    <q-card class="q-mb-lg" id="anchor-documents">
        <q-card-section class="q-pa-none">
            <q-table flat :rows="rows" :columns="columns" row-key="id" hide-pagination
                     :no-data-label="'رکوردی جهت نمایش وجود ندارد.'">
                <template v-slot:body="props">
                    <q-tr align="center">
                        <q-td>{{ props.row.id }}</q-td>
                        <q-td>
                            <strong>{{ props.row.title }}</strong>
                        </q-td>
                        <q-td>{{ props.row.type }}</q-td>
                        <q-td>{{ props.row.category.name ? props.row.category.name : '-' }}</q-td>
                        <q-td><strong>{{ props.row.size }}</strong></q-td>
                        <q-td dir="ltr">{{ props.row.createdAt.label }}</q-td>
                        <q-td>
                            <q-btn flat round type="submit" @click="editDocumentHandle(props.row)">
                                <q-tooltip>ویرایش</q-tooltip>
                                <q-icon name="edit" color="teal-10" size="1rem"></q-icon>
                            </q-btn>
                            <a :href="props.row.url" target="_blank">
                                <q-btn flat round type="submit">
                                    <q-tooltip>دانلود</q-tooltip>
                                    <q-icon name="download" color="teal-10" size="1rem"></q-icon>
                                </q-btn>
                            </a>
                        </q-td>
                    </q-tr>
                </template>
            </q-table>
            <Pagination :pagination="page.props.documents.meta" anchorId="anchor-documents"/>
        </q-card-section>
    </q-card>
    <q-card>
        <q-card-section>
            <div class="row q-col-gutter-sm">
                <div class="col-lg-6 col-12">
                    <label>عنوان</label>
                    <FieldInput
                        v-model="form.title"
                        bottom-slots
                        icon="edit_note"
                    />
                </div>
                <div class="col-lg-6 col-12">
                    <label>دسته بندی</label>
                    <FieldSelect
                        v-model="form.category"
                        :options="categories"
                        icon="checklist"
                    />
                </div>
                <div class="col-12">
                    <div class="q-uploader-section">
                        <q-uploader
                            field-name="file"
                            class="q-mb-lg"
                            color="teal-10"
                            :url="uploadUrl"
                            :label="fileLabel"
                            :max-file-size="maxSizeByte"
                            accept=".xlsx, .xls, .csv, .txt, .jpg, .jpeg, .png, .webp, .docx, .pdf"
                            max-files="1"
                            :form-fields="[{ name: '_token', value: csrfToken }]"
                            auto-upload
                            @uploaded="handleUploaded"
                            @rejected="handleRejected"
                        />
                    </div>
                </div>
                <div class="col-12">
                    <div class="rb-add">
                        <q-btn
                            :disable="isDisabled"
                            type="submit"
                            class="rb-action rb-large"
                            label="افزودن فایل"
                            @click="addDocument"
                            :loading="isLoading"
                        />
                    </div>
                </div>
            </div>
        </q-card-section>
    </q-card>
</template>
<script>
import FieldNumber from "@/Components/FieldNumber.vue";
import {route} from "ziggy-js";
import {usePage} from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {ref} from "vue";
import {formHandler, getCsrfToken, goToRoute, showNotify, tableColumns} from "@/utils/helpers.js";
import Pagination from "@/Components/Pagination.vue";

export default {
    methods: {route},
    components: {
        Pagination,
        AdminLayout,
        FieldNumber,
        FieldInput,
        FieldSelect,
    },
    props: {
        title: String,
        description: String,
        modelType: String,
        id: Number,
        redirectRoute: String
    },
    setup(props) {
        const page = usePage();
        const documentId = ref(null);
        const dialogVisible = ref(false);
        const uploadUrl = route('admin.documents.upload');
        const maxSizeByte = 1024000;
        const maxSizeMb = 1;
        const isDisabled = ref(true);
        const fileLabel = 'سایز نباید بیشتر از ' + maxSizeMb + ' مگابایت باشد.';
        const rows = ref(page.props.documents.data);
        const {form, submitForm, isLoading} = formHandler({
            title: ref(),
            category: ref(),
            media_id: ref(),
            model_type: props.modelType,
            id: props.id
        }, 'post');
        const {form: form2, submitForm: submitForm2, isLoading: isLoading2} = formHandler({
            title: ref(),
            category: ref(),
        }, 'put');


        const handleRejected = (files) => {
            if (files.length > 0 && files[0].failedPropValidation == 'max-file-size') {
                showNotify('حجم فایل نباید بیشتر از ' + maxSizeMb + ' مگابایت باشد', 'negative')
            } else if (files.length > 0 && files[0].failedPropValidation == "accept") {
                showNotify('فقط فرمت های مجاز اپلود شود', 'negative')
            }
        }
        const handleUploaded = ({xhr}) => {
            const response = JSON.parse(xhr.response);
            if (response.status == 'success') {
                isDisabled.value = false;
                form.media_id = response.data.media_id
            }
            showNotify(response.message, response.status == 'success' ? 'positive' : 'negative')
        }

        const addDocument = () => {
            submitForm('admin.documents.store', null, props.redirectRoute);
        }

        const editDocumentHandle = (row) => {
            dialogVisible.value = true;
            form2.title = row.title
            form2.category = row.category.id
            documentId.value = row.id
        }

        const updateDocument = () => {
            submitForm2('admin.document.update', documentId.value, props.redirectRoute);
        }

        return {
            page,
            uploadUrl,
            fileLabel,
            maxSizeByte,
            maxSizeMb,
            handleUploaded,
            isDisabled,
            isLoading,
            isLoading2,
            handleRejected,
            addDocument,
            form,
            form2,
            csrfToken: getCsrfToken(),
            dialogVisible,
            selectedOption: null,
            categories: page.props.categories,
            // مطمئن شوید که rows و columns به درستی مقداردهی شده‌اند
            columns: tableColumns(['شناسه', 'عنوان', 'نوع', 'دسته بندی', 'حجم', 'تاریخ', 'عملیات']),
            rows,
            editDocumentHandle,
            updateDocument
        }
    }
}
</script>
<style scoped>
.q-table__container :deep(.q-table__bottom--nodata) {
    justify-content: center;
    align-items: center;
    margin: 15px 0;
    padding: 0;
    position: relative;
    font-size: .95rem;
    border: 0;
}

.q-table__container :deep(.q-table__bottom--nodata .q-table__bottom-nodata-icon) {
    margin: 0;
    font-size: 1.5rem;
    color: rgb(0, 75, 65);
}
</style>
