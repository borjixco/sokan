<template>
    <div>

        <!-- جدول داده‌ها -->
        <q-table
            flat
            :rows="rows"
            :columns="columns"
            hide-pagination
            :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
        >
            <template v-slot:body="props">
                <q-tr>
                    <q-td>{{ props.row.number }}</q-td>
                    <q-td>{{ props.row.floor }}</q-td>
                </q-tr>
            </template>
        </q-table>

        <!-- صفحه‌بندی -->
        <Pagination :pagination="page.props.units.meta"/>
    </div>
</template>

<script>
import Pagination from "@/Components/Pagination.vue";
import {ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";

export default {
    components: {
        Pagination,
    },
    setup() {
        const page = usePage();
        const filters = ref({
            search: "",
            page: 1,
        });

        const columns = [
            {name: "number", label: "شماره", field: "number"},
            {name: "name", label: "نام", field: "name"},
        ];
        const units = ref(page.props.units.data);
        const rows = units;
        console.log(page.props.units)
        const totalPages = ref(page.props.units.meta.last_page);

        const fetchData = async () => {
            const response = await router.get(
                route("admin.test"), {...filters.value}, {preserveState: true, preserveScroll: true}
            );



            console.log(response)
            //rows.value = response.props.units.data;
            //totalPages.value = response.props.units.meta.last_page;
        };

        const onSearch = (searchQuery) => {
            filters.value.search = searchQuery;
            filters.value.page = 1;
            fetchData();
        };

        const onPageChange = (page) => {
            filters.value.page = page;
            fetchData();
        };

        //fetchData();

        return {
            page,
            filters,
            rows,
            columns,
            totalPages,
            onSearch,
            onPageChange,
        };
    },
};
</script>
