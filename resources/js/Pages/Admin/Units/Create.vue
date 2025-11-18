<template>
    <Head title="واحدهای تجاری - ایجاد"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8 col-12">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="add_business"/>
                    </span>
                        </div>
                        <div class="rb-title">
                            <h1>افزودن واحد تجاری</h1>
                            <p>
                                لطفاً اطلاعات را با دقت تکمیل کرده و قیمت‌ها را به ریال ثبت می کنیم.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-back-section">
                        <q-btn type="submit" class="rb-action" flat icon-right="chevron_left" label="بازگشت" @click="goToRoute(route('admin.units'))"/>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-card-section>
                    <div class="row q-col-gutter-sm">
                        <div class="col-12">
                            <div class="rb-subtitle-section">
                                <div class="rb-icon">
                                    <span>
                                        <q-icon name="store"/>
                                    </span>
                                </div>
                                <div class="rb-subtitle">
                                    <h1>اطلاعات عمومی واحد تجاری</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>شماره واحد</label>
                            <FieldInput
                                v-model="form.unit_number"
                                icon="segment"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>طبقه</label>
                            <FieldSelect
                                v-model="form.floor"
                                :options="floor_options"
                                icon="filter_list"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>متراژ</label>
                            <FieldNumber
                                bottom-slots
                                color="teal-10"
                                v-model="form.meterage"
                                @keyup="calculateNowTotalValueMeterage"
                                hint="متر مربع"
                                icon="crop"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>نوع کاربری</label>
                            <FieldSelect
                                v-model="form.unit_type"
                                :options="unit_type_options"
                                icon="domain"
                            />
                        </div>
                        <div class="col-lg-2 col-6 col-12">
                            <label>تلفن واحد تجاری</label>
                            <FieldInput
                                v-model="form.tel"
                                icon="smartphone"
                            />
                        </div>
                        <div class="col-lg-2 col-6 col-12">
                            <label>کد پستی</label>
                            <FieldInput
                                v-model="form.postal_code"
                                icon="location_on"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>موقعیت</label>
                            <FieldSelect
                                v-model="form.position"
                                :options="position_options"
                                icon="my_location"
                            />
                        </div>
                        <div class="col-lg-2 col-6 col-12">
                            <label>شماره بدنه کنتور</label>
                            <FieldInput
                                v-model="form.meter_serial_number"
                                icon="dialpad"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>وضعیت سقف</label>
                            <FieldSelect
                                v-model="form.roof"
                                :options="roof_options"
                                icon="roofing"
                            />
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>پرونده</label>
                            <FieldInput
                                v-model="form.case"
                                icon="folder_open"
                            />
                        </div>
                        <div class="col-lg-6 col-12">
                            <label>رمز رایانه</label>
                            <FieldInput
                                v-model="form.computer_password"
                                icon="password"
                            />
                        </div>
                        <div class="col-12"></div>
                        <div class="col-lg-4 col-6 col-12">
                            <div class="col-12">
                                <div class="rb-subtitle-section">
                                    <div class="rb-icon">
                                    <span>
                                        <q-icon name="home"/>
                                    </span>
                                    </div>
                                    <div class="rb-subtitle">
                                        <h1>وضعیت واحد تجاری</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <q-radio v-model="form.status" val="SOLD" color="teal-10" label="فروخته شده" @update:model-value="statusHandle"/>
                                <q-radio v-model="form.status" val="NOT_SOLD" color="teal-10" label="فروخته نشده" @update:model-value="statusHandle"/>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6 col-12">
                            <div class="col-12">
                                <div class="rb-subtitle-section">
                                    <div class="rb-icon">
                                    <span>
                                        <q-icon name="home"/>
                                    </span>
                                    </div>
                                    <div class="rb-subtitle">
                                        <h1>وضعیت بهره برداری</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <q-radio v-model="form.operation" val="RENTED" color="teal-10" label="اجاره شده"/>
                                <q-radio v-model="form.operation" val="OWNER_OPERATED" color="teal-10"
                                         label="راه اندازی توسط مالک"/>
                                <q-radio v-model="form.operation" val="EMPTY" color="teal-10" label="خالی"/>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6 col-12">
                            <div class="col-12">
                                <div class="rb-subtitle-section">
                                    <div class="rb-icon">
                                    <span>
                                        <q-icon name="block"/>
                                    </span>
                                    </div>
                                    <div class="rb-subtitle">
                                        <h1>وضعیت پلمپ</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <q-radio v-model="form.block" val="" color="teal-10" label="بدون پلمپ"/>
                                <q-radio v-model="form.block" val="JUDICIAL_BLOCK" color="teal-10" label="پلمپ قضایی"/>
                                <q-radio v-model="form.block" val="COMPLEX_BLOCK" color="teal-10"
                                         label="پلمپ توسط مجتمع"/>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="rb-subtitle-section">
                                <div class="rb-icon">
                                    <span>
                                        <q-icon name="receipt_long"/>
                                    </span>
                                </div>
                                <div class="rb-subtitle">
                                    <h1>اطلاعات مالی واحد تجاری</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>ارزش هر متر از واحد تجاری</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.value_per_meter"
                                hint="ریال"
                                icon="crop"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>ارزش واحد تجاری فروخته شده</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.total_value"
                                hint="ریال"
                                icon="crop"
                                :readonly="form.status == 'NOT_SOLD'"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>قیمت نهایی</label>
                            <FieldNumber
                                outlined
                                bottom-slots
                                color="teal-10"
                                v-model="form.total_now_value"
                                icon="crop"
                                :left-hint="thisMonth"
                                @keyup="calculateNowTotalValue"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>ارزش هر متر به نرخ روز</label>
                            <q-input
                                readonly
                                outlined
                                bottom-slots
                                v-model="now_value_per_meter"
                                inputmode="numeric"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="crop"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>حداکثر رهن کامل</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.maximum_full_mortgage"
                                hint="ریال"
                                icon="add_card"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>حداکثر اجاره ماهیانه</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.maximum_monthly_rent"
                                hint="ریال"
                                icon="add_card"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>اجاره سرقفلی سالانه مالک</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.owner_annual_goodwill_rent"
                                hint="ریال"
                                icon="add_card"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>فروشی</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.sale_price_suggested_owner"
                                right-hint="نرخ فروش پیشنهادی مالک"
                                left-hint="ریال"
                                icon="add_card"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>رهن پیشنهادی مالک</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.owner_proposed_mortgage"
                                placeholder="اجاره‌ای"
                                left-hint="ریال"
                                icon="add_card"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>اجاره پیشنهادی مالک</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.rent_proposed_owner"
                                placeholder="اجاره‌ای"
                                left-hint="ریال"
                                icon="add_card"
                            />
                        </div>
                        <div class="col-lg-6 col-6 col-12">
                            <label>مبلغ شارژ</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.charge_amount"
                                placeholder="اجاره‌ای"
                                left-hint="ریال"
                                icon="add_card"
                            />

                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <add-owner-component @ownersSelected="ownersSelectedHandle" @quotaUpdated="updateQuotaHandler"/>
            <add-occupant-component @occupantsSelected="occupantsSelectedHandle"/>
            <div class="col-12">
                <div class="q-my-md">
                    <div class="rb-add">
                        <q-btn type="submit" class="rb-action rb-large" label="افزودن واحد تجاری"
                               @click="addUnit"
                               :loading="isLoading"
                        />
                    </div>
                </div>
            </div>
        </q-page>
    </AdminLayout>
</template>

<script>
import {ref} from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {formHandler, goToRoute, numberFormat, removeCommaNumber} from "@/utils/helpers.js";
import {Head, usePage} from "@inertiajs/vue3";
import AddOwnerComponent from "@/Components/AddOwnerComponent.vue";
import AddOccupantComponent from "@/Components/AddOccupantComponent.vue";
import FieldNumber from "@/Components/FieldNumber.vue";
import FieldInput from "@/Components/FieldInput.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import {route} from "ziggy-js";

export default {
    methods: {route, goToRoute},
    components: {
        FieldNumber,
        AddOwnerComponent,
        AddOccupantComponent,
        Head,
        AdminLayout,
        FieldInput,
        FieldSelect
    },
    setup() {
        const page = usePage();
        const thisMonth = ref(new Date().toLocaleString('fa-IR', {month: '2-digit', year: 'numeric'}) + '/01');
        const metrageValue = ref(0);
        const perMeterValue = ref(0);
        const now_value_per_meter = ref(0);
        const {form, submitForm, isLoading} = formHandler({
            unit_number: ref(''),
            floor: ref('طبقه'),
            meterage: ref(''),
            unit_type: ref('نوع کاربری'),
            tel: ref(''),
            postal_code: ref(''),
            position: ref('موقعیت'),
            meter_serial_number: ref(''),
            roof: ref('وضعیت سقف'),
            computer_password: ref(),
            case: ref(),
            status: ref(''),
            operation: ref(''),
            block: ref(''),
            value_per_meter: ref(''),
            total_now_value: ref(''),
            total_value: ref(''),
            maximum_full_mortgage: ref(''),
            maximum_monthly_rent: ref(''),
            owner_annual_goodwill_rent: ref(''),
            sale_price_suggested_owner: ref(''),
            owner_proposed_mortgage: ref(''),
            rent_proposed_owner: ref(''),
            charge_amount: ref(''),
            owners: ref([]),
            occupants: ref([]),
            quotas: ref([]),
        });

        const calculateNowTotalValueMeterage = (event) => {
            metrageValue.value = event.target.value == '' ? 0 : parseFloat(removeCommaNumber(event.target.value));
            if (metrageValue.value > 0 && form.total_now_value > 0) {
                now_value_per_meter.value = numberFormat(removeCommaNumber(form.total_now_value) / metrageValue.value);
            } else {
                now_value_per_meter.value = 0;
            }
        }

        const calculateNowTotalValue = (event) => {
            perMeterValue.value = event.target.value == '' ? 0 : parseFloat(removeCommaNumber(event.target.value));
            console.log(perMeterValue.value);
            console.log(form.total_now_value)
            if (perMeterValue.value > 0 && form.total_now_value > 0) {
                now_value_per_meter.value = numberFormat(removeCommaNumber(form.total_now_value) / metrageValue.value);
            } else {
                now_value_per_meter.value = 0;
            }
        }

        const addUnit = () => {
            submitForm('admin.units.store');
        };

        const ownersSelectedHandle = (data) => {
            form.owners = data.value
        };

        const occupantsSelectedHandle = (data) => {
            form.occupants = data.value
        };

        const updateQuotaHandler = (newQuotas) => {
            console.log(newQuotas)
            form.quotas = newQuotas;
        };

        const statusHandle = () => {
            if(form.status === 'NOT_SOLD'){
                form.total_value = null;
            }
        }

        return {
            calculateNowTotalValueMeterage,
            calculateNowTotalValue,
            roof: ref('With_Roof'),
            block: ref('Judicial_block'),
            unit_type_options: page.props.unitTypes,
            position_options: page.props.positions,
            roof_options: page.props.roof,
            floor_options: page.props.floor,
            form,
            isLoading,
            addUnit,
            ownersSelectedHandle,
            occupantsSelectedHandle,
            thisMonth,
            now_value_per_meter,
            updateQuotaHandler,
            statusHandle
        }
    }
}
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
</style>
