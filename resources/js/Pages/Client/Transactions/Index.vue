<template>
    <Head title="تراکنش ها"/>
    <ClientLayout>
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
                            <h1>{{currentSection.pageTitle}}</h1>
                            <p>{{currentSection.pageDescription}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <q-card class="q-mb-lg">
                <q-table
                    flat
                    :rows="rows"
                    :columns="columns"
                    :rows-per-page="page.props.rows.meta.per_page"
                    :rows-per-page-options="[page.props.rows.meta.per_page]"
                    hide-pagination
                    :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                    :loading="isLoading"
                >
                    <template v-slot:top-right>
                        <div class="row q-col-gutter-md">
                            <div class="col">
                                <q-input
                                    dense
                                    color="teal-10"
                                    placeholder="جستجو"
                                    v-model="search"
                                    @keyup="searchQuery"
                                >
                                    <template v-slot:append>
                                        <q-icon name="search"/>
                                    </template>
                                </q-input>
                            </div>
                        </div>
                    </template>
                    <template v-slot:body="props">
                        <q-tr align="center">
                            <q-td><strong>{{ props.row.id }}</strong></q-td>
                            <q-td>{{ props.row.payableTitle }}</q-td>
                            <q-td dir="ltr"><strong>{{ props.row.amount ? numberFormat(props.row.amount) : '-' }}</strong></q-td>
                            <q-td>{{ props.row.transactionId }}</q-td>
                            <q-td>{{ props.row.referenceId }}</q-td>
                            <q-td>{{ props.row.description }}</q-td>
                            <q-td dir="ltr">{{ props.row.createdAt.label }}</q-td>
                            <q-td>{{ props.row.method.label }}</q-td>
                            <q-td>{{ props.row.status.label }}</q-td>
                        </q-tr>
                    </template>
                </q-table>
                <q-card-section>
                    <Pagination :pagination="page.props.rows.meta"/>
                </q-card-section>
            </q-card>
        </q-page>
    </ClientLayout>
</template>

<script>
import {usePage, Link, Head, router} from "@inertiajs/vue3";
import {ref} from "vue";
import {numberFormat, tableColumns} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import FieldInput from "@/Components/FieldInput.vue";
import {route} from "ziggy-js";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";
import ClientLayout from "@/Layouts/ClientLayout.vue";

export default {
    methods: {numberFormat},
    components: {
        ClientLayout,
        Pagination,
        FieldInput,
        FieldSelect,
        FieldNumber,
        Link,
        Head
    },
    setup() {

        const page = usePage();
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const payable_type = ref(new URLSearchParams(uri).get('payable_type'));
        const payable_id = ref(new URLSearchParams(uri).get('payable_id'));
        const currentSection = ref(page.props.currentSection || {icon: "", pageTitle: "", pageDescription: ""});
        const rows = ref(page.props.rows.data)
        const isLoading = ref(false)
        const columns = tableColumns(['شناسه', 'نوع', 'ملبغ (ریال)', 'کدتراکنش', 'کد پیگیری', 'توضیحات', 'تاریخ', 'روش پرداخت', 'وضعیت']);

        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('admin.transactions'), {search: search.value,payable_type: payable_type.value,payable_id: payable_id.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data;
                }
            })
        }, 600);

        return {
            page,
            columns,
            rows,
            currentSection,
            isLoading,
            searchQuery,
            search
        };
    },
};
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

.q-row {
    margin: 0 0 15px;
    position: relative;
}

.q-row .q-remove {
    position: absolute;
    top: 0;
    left: 0;
}

.q-table__container :deep(.q-table__bottom--nodata) {
    justify-content: center;
    align-items: center;
    position: relative;
    top: 15px;
    font-size: .95rem;
    border: 0;
}

.q-table__container :deep(.q-table__bottom--nodata .q-table__bottom-nodata-icon) {
    margin: 0;
    font-size: 1.5rem;
    color: rgb(0, 75, 65);
}
</style>
