<template>
    <Head title=""/>
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
                            <h1>{{ currentSection.pageTitle }}</h1>
                            <p>{{ currentSection.pageDescription }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-add-section">

                        <Link :href="route('client.tickets.create')">
                            <q-btn type="submit" class="rb-action" label="افزودن"/>
                        </Link>
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
                            <q-td>{{ props.row.subject }}</q-td>
                            <q-td>{{ props.row.category.name }}</q-td>
                            <q-td>{{ props.row.status.label }}</q-td>
                            <q-td>
                                <Link :href="route('client.tickets.edit',props.row.id)">
                                    <q-btn flat round type="submit">
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
        </q-page>
    </ClientLayout>
</template>
<script>
import ClientLayout from "@/Layouts/ClientLayout.vue";
import {Head, usePage, Link, router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import Pagination from "@/Components/Pagination.vue";
import {goToRoute, tableColumns} from "@/utils/helpers.js";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {ref} from "vue";
import {debounce} from "lodash";


export default {
    methods: {goToRoute, route},
    components: {
        Pagination,
        Head,
        Link,
        ClientLayout
    },
    setup() {
        const page = usePage();
        console.log(page.props.rows)
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const isLoading = ref(false);
        const currentSection = page.props.currentSection;
        const columns = tableColumns(['شناسه', 'عنوان', 'دسته', 'وضعیت', 'عملیات']);
        const rows = ref(page.props.rows.data);

        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('client.tickets'), {search: search.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data; // داده‌های جدید را بروزرسانی می‌کنیم
                }
            })
        }, 600);


        return {
            page,
            columns,
            rows,
            currentSection,
            search,
            searchQuery,
            isLoading
        };
    },
};
</script>
