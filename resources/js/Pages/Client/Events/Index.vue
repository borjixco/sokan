<template>
    <Head title="رویدادها"/>
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
            </div>
            <q-card class="q-mb-lg">
                <q-card-section>
                    <q-table
                        flat
                        :rows="rows"
                        :columns="columns"
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
                                <q-td>
                                    <strong>{{props.row.title}}</strong>
                                </q-td>
                                <q-td>{{props.row.location}}</q-td>
                                <q-td dir="ltr">{{props.row.eventDate.label}}</q-td>
                                <q-td>{{props.row.short_description}}</q-td>
                                <q-td>{{props.row.description}}</q-td>
                            </q-tr>
                        </template>
                    </q-table>
                </q-card-section>
            </q-card>
        </q-page>

    </ClientLayout>
</template>
<script>
import FieldInput from "@/Components/FieldInput.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import {Head, router, usePage} from "@inertiajs/vue3";
import {goToRoute, tableColumns} from "@/utils/helpers.js";
import {ref} from "vue";
import {route} from "ziggy-js";
import FieldNumber from "@/Components/FieldNumber.vue";
import {debounce} from "lodash";
import ClientLayout from "@/Layouts/ClientLayout.vue";

export default {
    methods: {
        goToRoute,
        route
    },
    components: {
        ClientLayout,
        FieldNumber,
        Head,
        FieldInput,
        FieldSelect
    },
    setup() {
        const page = usePage();
        let uri = window.location.search.substring(1);
        const search = ref(new URLSearchParams(uri).get('search'));
        const currentSection = page.props.currentSection;
        const columns = tableColumns(['عنوان', 'مکان', 'تاریخ برگزاری', 'توضیحات کوتاه', 'توضیحات']);
        const rows = ref(page.props.rows.data);
        const isLoading = ref(false);
        const searchQuery = debounce(() => {
            isLoading.value = true
            router.get(route('client.events'), {search: search.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
                    rows.value = response.props.rows.data; // داده‌های جدید را بروزرسانی می‌کنیم
                }
            })
        }, 600);


        return {
            currentSection,
            years: page.props.years,
            months: page.props.months,
            days: page.props.days,
            hours: page.props.hours,
            minutes: page.props.minutes,
            isLoading,
            columns,
            rows,
            search,
            searchQuery,
        };
    }

};
</script>
<style scoped>

.q-field :deep(.q-field__prepend) {
    padding: 0 0 0 5px;
}

.q-field :deep(.q-field__append) {
    padding: 0;
}

.q-field :deep(.q-field__native) {
    padding: 0;
    font-size: .95rem;
    line-height: 1;
}

.q-field :deep(.q-field__bottom) {
    padding: 5px;
}

.q-textarea :deep(.q-field__native) {
    padding: 20px 0 0;
    resize: none;
}
</style>
