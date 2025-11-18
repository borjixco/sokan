<template>
    <div class="row items-center">
        <div class="col-lg-8 col-12">
            <div class="rb-title-section">
                <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="account_box"/>
                    </span>
                </div>
                <div class="rb-title">
                    <h1>اطلاعات مستاجر واحد تجاری</h1>
                    <p>لیست کاربران با کلیک بر روی "افزودن مستاجر" در دسترس است.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="text-left">
                <div class="rb-add">
                    <q-btn class="rb-action" label="افزودن مستاجر" @click="add = true"/>
                </div>
            </div>
        </div>
    </div>
    <q-dialog v-model="add">
        <q-card style="width: 100%; max-width: 750px">
            <q-card-section>
                <div class="row items-center">
                    <div class="col-lg-7 col-12">
                        <div class="text-h6">لیست مستاجر</div>
                    </div>
                    <div class="col-lg-5 col-12">
                        <q-input
                            dense
                            input-class="text-left"
                            color="teal-10"
                            label="جستجو مستاجر"
                            v-model="searchQuery"
                            @update:modelValue="debouncedSearch"
                        />
                    </div>
                </div>
            </q-card-section>
            <q-card-section class="q-pt-none">
                <q-table
                    flat
                    :rows="rows"
                    :columns="columns"
                    :rows-per-page="10"
                    :rows-per-page-options="[10]"
                    hide-pagination
                >
                    <template v-slot:header>
                        <q-tr>
                            <q-th></q-th>
                            <q-th v-for="(column, index) in columns" :key="index">{{ column.label }}</q-th>
                        </q-tr>
                    </template>
                    <template v-slot:body="props">
                        <q-tr>
                            <q-td>
                                <q-checkbox
                                    color="teal-10"
                                    v-model="props.row.selected"
                                    @update:model-value="() => updateSelection(props.row)"
                                />
                            </q-td>
                            <q-td align="center">{{ props.row.name }}</q-td>
                            <q-td align="center">{{ props.row.phone }}</q-td>
                        </q-tr>
                    </template>
                </q-table>
            </q-card-section>
        </q-card>
    </q-dialog>
    <div v-if="selected.length">
        <q-card class="q-mb-md">
            <q-card-section>
                <q-list dense>
                    <q-item v-for="(item, index) in selected" :key="index">
                        <q-item-section>{{ item.name }} - {{ item.phone }}</q-item-section>
                        <q-item-section avatar>
                            <q-btn
                                flat
                                round
                                icon="cancel"
                                class="rb-delete"
                                @click="removeItem(index)"
                            />
                        </q-item-section>
                    </q-item>
                </q-list>
            </q-card-section>
        </q-card>
    </div>

</template>
<script>
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {debounce} from "lodash";
import {tableColumns} from "@/utils/helpers.js";

export default {
    setup(props, {emit}) {

        const page = usePage();
        const general = page.props.general;
        const columns = tableColumns(['مستاجر', 'شماره موبایل']);
        const occupantsIdSelected = ref([])
        const rows = ref(page.props.occupants);
        const searchQuery = ref('');

        const debouncedSearch = debounce((query) => {
            if (query.trim() !== '') {
                fetch(route('admin.units.occupants', {'q': query}))
                    .then(response => response.json())
                    .then(function (data) {
                        rows.value = data
                    });
            }
        }, 500);


        const selected = ref([]);

        const updateSelection = (row) => {
            if (!general.multipleOccupant) {
                // وقتی انتخاب چندگانه غیرفعال است، تمام انتخاب‌های قبلی را پاک کن
                rows.value.forEach(item => item.selected = false);

                // سپس فقط آیتم فعلی را انتخاب کن
                row.selected = true;
                selected.value = [{ name: row.name, phone: row.phone }];
            } else {
                if (row.selected) {
                    selected.value.push({ name: row.name, phone: row.phone });
                } else {
                    selected.value = selected.value.filter(item => item.phone !== row.phone);
                }
            }

            emit('occupantsSelected', selected);
        };


        const removeItem = (index) => {
            selected.value.splice(index, 1);
            occupantsIdSelected.value.splice(index, 1)
            emit('occupantsSelected', selected);
        };

        return {
            occupantsIdSelected,
            debouncedSearch,
            searchQuery,
            add: ref(false),
            columns,
            rows,
            selected,
            updateSelection,
            removeItem,
        }
    }
}
</script>
