<template>
    <Head title="واحدهای تجاری"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                            <div>
                                <q-icon :name="currentSection.icon"/>
                            </div>
                        </div>
                        <div class="rb-title">
                            <h1>{{ currentSection.pageTitle }}</h1>
                            <p>{{ currentSection.pageDescription }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-actions-section">
                        <div class="rb-add-section">
                            <excel-button
                                class="rb-action"
                                label="خروجی واحدها"
                                :route="route('admin.units.excel')"
                                slug="واحدها"
                                title="فیلتر خروجی واحدها"
                                btn-title="خروجی"
                                card-class="full-width"
                                :use-dialog="false"
                            />
                            <q-btn class="rb-action" label="پرینت" icon="print" @click="printHandle"/>
                            <q-btn type="submit" class="rb-action" label="افزودن واحد" icon="add"
                                   @click="goToRoute(route('admin.units.create'))"/>
                        </div>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-table
                    flat
                    :rows="rows"
                    :columns="columns"
                    :rows-per-page="perPage"
                    :rows-per-page-options="[perPage]"
                    hide-pagination
                    :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                    :loading="isLoading"
                    :key="perPage"
                >
                    <template v-slot:top-right>
                        <div class="rb-filter-section">
                            <q-input
                                dense
                                color="teal-10"
                                placeholder="شماره واحد"
                                v-model="search"
                                @keyup="searchQuery"
                            >
                                <template v-slot:append>
                                    <q-icon name="search"/>
                                </template>
                            </q-input>
                        </div>
                        <span>نتایج: {{numberFormat(page.props.rows.meta.total)}}</span>
                    </template>
                    <template v-slot:top-left>
                        <div class="rb-select-section">
                            <FieldSelect
                                v-model="perPage"
                                dense
                                :options="perPages"
                                @change="perPageHandle"
                            />
                        </div>
                    </template>
                    <template v-slot:body="props">
                        <q-tr align="center">
                            <q-td><strong>{{ props.row.number }}</strong></q-td>
                            <q-td>{{ props.row.floor.label }}</q-td>
                            <q-td>{{ props.row.meterage }}</q-td>
                            <q-td>{{ props.row.position ? props.row.position.label : '-' }}</q-td>
                            <q-td>{{ props.row.owner ? props.row.owner.user.name : '-' }}</q-td>
                            <q-td>{{ props.row.owner ? props.row.owner.user.mobile : '' }}</q-td>
                            <q-td>
                                <span v-if="props.row.status.value" :class="['rb-status', props.row.status.value]">
                                    {{ props.row.status.label }}
                                </span>
                            </q-td>
                            <q-td>
                                <span v-if="props.row.roof.value" :class="['rb-status', props.row.roof.value]">
                                    {{ props.row.roof.label }}
                                </span>
                            </q-td>
                            <q-td>
                                <span v-if="props.row.block.value"
                                      :class="['rb-status', props.row.block.value]">
                                    {{ props.row.block.label }}
                                </span>
                            </q-td>
                            <q-td class="rb-eye">
                                <Link :href="route('admin.units.edit',props.row.id)">
                                    <q-btn type="submit" flat round>
                                        <q-icon name="visibility" color="teal-10" size="1rem"></q-icon>
                                    </q-btn>
                                </Link>
                            </q-td>
                        </q-tr>
                    </template>
                </q-table>
                <q-card-section>
                    <Pagination :pagination="page.props.rows.meta"/>
                </q-card-section>
            </q-card>
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="drive_folder_upload"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>آپلود فایل اکسل</h1>
                    <p>
                        با کلیک بر روی آیکون + می توانید فایل اکسل خود را آپلود کنید.
                    </p>
                </div>
            </div>
            <div class="q-uploader-section">
                <q-uploader
                    field-name="file"
                    class="q-mb-lg"
                    color="teal-10"
                    :url="uploadUrl"
                    label="حداکثر سایز فایل 1 مگابایت"
                    max-file-size="1024000"
                    accept=".xlsx, .xls, .csv"
                    max-files="1"
                    :form-fields="[{ name: '_token', value: csrfToken }]"
                    @uploaded="uploadExcel"
                />
            </div>
        </q-page>
    </AdminLayout>
</template>
<script>
import {ref} from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import Pagination from "@/Components/Pagination.vue";
import {router, usePage, Head, Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {getCsrfToken, goToRoute, numberFormat, showNotify, tableColumns} from "@/utils/helpers.js";
import {debounce} from "lodash";
import ExcelButton from "@/Components/ExcelButton.vue";

export default {
    methods: {numberFormat, goToRoute, route},
    components: {
        ExcelButton,
        AdminLayout,
        Head,
        Pagination,
        Link,
        FieldSelect
    },
    setup() {
        const page = usePage();
        const isLoading = ref(false)
        const perPage = ref(page.props.rows.meta.per_page);
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const currentSection = page.props.currentSection;
        const uploadUrl = route('admin.units.upload');
        const columns = tableColumns(['شماره واحد', 'طبقه', 'متراژ', 'موقعیت', 'نام مالک', 'موبایل مالک', 'وضعیت ملک', 'وضعیت سقف', 'پلمپ', {
            label: 'عملیات',
            align: 'center',
            headerClasses: 'rb-eye'
        }]);
        const rows = ref(page.props.rows.data);
        const uploadExcel = ({xhr}) => {
            const response = JSON.parse(xhr.response);
            showNotify(response.message, response.status == 'success' ? 'positive' : 'negative')
            return goToRoute(route('admin.units'))
        }
        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.units'), {search: search.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data; // داده‌های جدید را بروزرسانی می‌کنیم
                }
            })
        }, 600);

        const perPageHandle = (value) => {
            perPage.value = value;
            isLoading.value = true
            router.get(route('admin.units'), {search: search.value, perPage: perPage.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    console.log(response.props.rows.data);
                    rows.value = response.props.rows.data; // داده‌های جدید را بروزرسانی می‌کنیم
                }
            })
        }

        const printHandle = () => {
            window.print()
        }
        return {
            perPages: page.props.perPages,
            perPage,
            perPageHandle,
            page,
            csrfToken: getCsrfToken(),
            uploadUrl,
            columns,
            rows,
            currentSection,
            uploadExcel,
            searchQuery,
            search,
            isLoading,
            printHandle,
        };
    },
};
</script>
<style scoped>

.rb-status {
    display: inline-block;
    margin: 0 2.5px;
    padding: 10px 25px 10px 15px;
    position: relative;
    font-size: .90rem;
    line-height: 1;
    border-radius: 300px;
}

.rb-status:before {
    content: '';
    width: 7.5px;
    height: 7.5px;
    position: absolute;
    top: 11.75px;
    right: 11.75px;
    background: rgb(30, 30, 30);
    border-radius: 50%;
}

.rb-status.SOLD {
    background: rgb(230, 245, 240);
    color: rgb(15, 20, 30)
}

.rb-status.RENTED {
    background: rgb(230, 245, 240);
    color: rgb(15, 20, 30)
}

.rb-status.RENTED:before {
    background: rgb(25, 85, 60)
}

.rb-status.EMPTY {
    background: rgb(235, 240, 255);
    color: rgb(15, 20, 30)
}

.rb-status.EMPTY:before {
    background: rgba(60, 130, 245)
}

.rb-status.WITH_ROOF {
    background: rgb(255, 245, 230);
    color: rgb(15, 20, 30)
}

.rb-status.WITH_ROOF:before {
    background: rgb(170, 130, 50)
}

.rb-status.WITHOUT_ROOF {
    background: rgb(255, 245, 230);
    color: rgb(15, 20, 30)
}

.rb-status.WITHOUT_ROOF:before {
    content: '';
    background: rgb(170, 130, 50);
}

.rb-status.JUDICIAL_BLOCK {
    background: rgb(255, 230, 230);
    color: rgb(15, 20, 30)
}

.rb-status.JUDICIAL_BLOCK:before {
    background: rgb(240, 15, 15)
}

.rb-status.COMPLEX_BLOCK {
    background: rgba(240, 230, 255);
    color: rgb(15, 20, 30)
}

.rb-status.COMPLEX_BLOCK:before {
    background: rgb(170, 120, 215)
}

.q-table__container :deep(.q-table__bottom--nodata) {
    justify-content: center;
    align-items: center;
    margin: 15px 0 -30px;
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
