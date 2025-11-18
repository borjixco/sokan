<template>
    <Head title="نقل و انتقالات"/>
    <AdminLayout>
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
                    <div class="rb-actions-section">
                        <div class="rb-add-section">
                            <excel-button
                                class="rb-action"
                                label="خروجی نقل و انتقال"
                                :route="route('admin.transfers.sale.excel')"
                                slug="شارژ-نقل-و-انتقال"
                                title="فیلتر خروجی نقل و انتقال"
                                btn-title="خروجی"
                                card-class="full-width"
                                :use-dialog="true"
                            >
                                <template #filters="{ filters }">
                                    <div class="row q-col-gutter-sm">
                                        <div class="col-12">
                                        <label>تاریخ تنظیم</label>
                                        </div>
                                        <div class="col-4">
                                            <label>سال</label>
                                            <FieldSelect v-model="filters.year" :options="years"/>
                                        </div>
                                        <div class="col-4">
                                            <label>ماه</label>
                                            <FieldSelect v-model="filters.month" :options="months"/>
                                        </div>
                                        <div class="col-4">
                                            <label>روز</label>
                                            <FieldSelect v-model="filters.day" :options="days"/>
                                        </div>
                                    </div>
                                </template>
                            </excel-button>
                            <q-btn class="rb-action" label="پرینت" @click="printHandle" icon="print"/>
                            <Link :href="route('admin.transfers.sale.create')">
                                <q-btn type="submit" class="rb-action" label="افزودن فروش" icon="add_business"/>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-card-section class="q-pa-none">
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
                                <div class="row q-col-gutter-md">
                                    <div class="col">
                                        <q-input
                                            dense
                                            color="teal-10"
                                            placeholder="شماره واحد"
                                            v-model="unit"
                                        >
                                            <template v-slot:append>
                                                <q-icon name="segment"/>
                                            </template>
                                        </q-input>
                                    </div>
                                    <div class="col">
                                        <q-input
                                            dense
                                            color="teal-10"
                                            placeholder="شماره قرارداد"
                                            v-model="contract"
                                        >
                                            <template v-slot:append>
                                                <q-icon name="edit_note"/>
                                            </template>
                                        </q-input>
                                    </div>
                                    <span>نتایج: {{numberFormat(page.props.rows.meta.total)}}</span>
                                </div>
                            </div>
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
                                <q-td><strong>{{ props.row.unit.unit_number }}</strong></q-td>
                                <q-td>{{ props.row.contract_number }}</q-td>
                                <q-td>
                                    <span class="rb-owner" v-for="(owner,index) in props.row.old_owners" :key="index">
                                        {{ owner?.user.name }}
                                    </span>
                                </q-td>
                                <q-td>
                                    <span class="rb-owner" v-for="(owner,index) in props.row.current_owners" :key="index">
                                        {{ owner?.user.name }}
                                    </span>
                                </q-td>
                                <q-td>{{ props.row.first_witness }}</q-td>
                                <q-td>{{ props.row.second_witness }}</q-td>
                                <q-td>
                                    <strong>{{ numberFormat(props.row.cost) }}</strong> <small v-if="props.row.cost">ریال</small>
                                </q-td>
                                <q-td>{{ props.row.doing_at.jalali }}</q-td>
                                <q-td class="rb-eye">
                                    <Link :href="route('admin.transfers.sale.edit',props.row.id)">
                                        <q-btn flat round>
                                            <q-icon name="visibility" color="teal-10" size="1rem"></q-icon>
                                        </q-btn>
                                    </Link>
                                </q-td>
                            </q-tr>
                        </template>
                    </q-table>
                    <Pagination :pagination="page.props.rows.meta"/>
                </q-card-section>
            </q-card>
        </q-page>
    </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage, Link, router} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import {route} from "ziggy-js";
import {numberFormat, tableColumns} from "@/utils/helpers.js";
import {debounce} from "lodash";
import Pagination from "@/Components/Pagination.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import ExcelButton from "@/Components/ExcelButton.vue";

export default {
    methods: {numberFormat, route},
    components: {
        ExcelButton,
        FieldSelect,
        Pagination,
        Head,
        AdminLayout,
        Link
    },
    setup() {
        const page = usePage();
        const currentSection = page.props.currentSection;
        const perPage = ref(page.props.rows.meta.per_page);
        let uri = window.location.search.substring(2);
        const unit = ref(new URLSearchParams(uri).get('unit') || "");
        const contract = ref(new URLSearchParams(uri).get('contract') || "");

        const isLoading = ref(false);
        const columns = tableColumns(['شماره واحد', 'شماره قرارداد', 'طرف اول قرارداد', 'طرف دوم قرارداد', 'شاهد اول', 'شاهد دوم', 'مبلغ', 'تاریخ تنظیم', {
            label: 'عملیات',
            align: 'center',
            headerClasses: 'rb-eye'
        }]);
        const rows = ref(page.props?.rows?.data);

        const searchQuery = debounce(() => {
            isLoading.value = true;
            router.get(route('admin.transfers.sale'), {unit: unit.value, contract: contract.value}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false;
                    rows.value = response.props.rows.data;
                }
            });
        }, 600);

        // واکنش به تغییرات در فیلترها
        watch([unit, contract], () => {
            searchQuery();
        });

        const perPageHandle = (value) => {
            perPage.value = value;
            isLoading.value = true
            router.get(route('admin.transfers.sale'), {
                unit: unit.value,
                contract: contract.value,
                perPage: perPage.value
            }, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (response) => {
                    isLoading.value = false
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
            currentSection,
            rows,
            columns,
            page,
            isLoading,
            unit,
            contract,
            searchQuery,
            printHandle,
            years: page.props.years,
            months: page.props.months,
            days: page.props.days
        };
    }
};
</script>
<style scoped>

.q-radio :deep(.q-radio__label) {
    padding: 0 5px 0 0;
}

.rb-search-section {
    padding: 15px;
}

.rb-search-section .rb-search {
    width: 100%;
    height: 40px;
}

.q-expansion-item {
    overflow: hidden;
    border-radius: .25rem;
}

.q-expansion-item :deep(.q-item__section--avatar) {
    min-width: 50px;
    padding: 0 15px 0 0;
}

.q-expansion-item :deep(.cursor-pointer) {
    padding: 0 0 0 5px;
}

.rb-owner {
    display: block;
}
</style>
