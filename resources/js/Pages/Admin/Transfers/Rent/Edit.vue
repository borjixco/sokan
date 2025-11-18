<template>
    <Head :title="`${currentSection.pageTitle} - ویرایش`"/>
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
                            <p>در این بخش می توانید نقل و انتقال را مشاهده کنید</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-add-section">
                        <a :href="route('admin.transfers.rent.print',transfer.id)" target="_blank">
                            <q-btn type="submit" class="rb-action" label="پرینت"/>
                        </a>
                    </div>
                </div>
            </div>

            <q-card class="q-mb-lg">
                <q-card-section>
                    <q-expansion-item
                        v-model="unit_expand"
                        icon="domain"
                        label="اطلاعات عمومی واحد"
                        caption=""
                    >
                        <div class="rb-info-section">
                            <ul class="row">
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="segment"/>
                                    <div>
                                        <span>واحد تجاری</span>
                                        <strong>{{ unit.number }}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="crop"/>
                                    <div>
                                        <span>متراژ</span>
                                        <strong>{{ unit.meterage }}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="domain"/>
                                    <div>
                                        <span>نوع واحد تجاری</span>
                                        <strong>{{ unit.type?.label }}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="smartphone"/>
                                    <div>
                                        <span>تلفن واحد تجاری</span>
                                        <strong>{{ unit.tel }}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="location_on"/>
                                    <div>
                                        <span>کد پستی</span>
                                        <strong>{{ unit.postal_code }}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="my_location"/>
                                    <div>
                                        <span>موقعیت</span>
                                        <strong>{{unit.position?.label}}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="dialpad"/>
                                    <div>
                                        <span>شماره بدنه کنتور</span>
                                        <strong>{{ unit.meter_serial_number }}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="filter_list"/>
                                    <div>
                                        <span>طبقه</span>
                                        <strong>{{unit.floor?.label}}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="roofing"/>
                                    <div>
                                        <span>وضعیت سقف</span>
                                        <strong>{{unit.roof?.label}}</strong>
                                    </div>
                                </li>
                                <li class="col-12">
                                    <q-icon name="gavel"/>
                                    <div>
                                        <span>وضعیت واحد تجاری</span>
                                        <strong>{{unit.status.label}}</strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>ارزش هر متر از واحد تجاری</span>
                                        <strong>{{ numberFormat(unit.value_per_meter) }} <small v-if="unit.value_per_meter">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>ارزش واحد های تجاری فروخته شده</span>
                                        <strong>{{ numberFormat(unit.total_value) }} <small v-if="unit.total_value">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>قیمت نهایی</span>
                                        <strong>{{ numberFormat(unit.now_total_value) }} <small v-if="unit.now_total_value">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>ارزش هر متر به نرخ روز</span>
                                        <strong>{{ numberFormat(unit.now_value_per_meter) }} <small v-if="unit.now_value_per_meter">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>حداکثر رهن کامل</span>
                                        <strong>{{ numberFormat(unit.maximum_full_mortgage) }} <small v-if="unit.maximum_full_mortgage">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>حداکثر اجاره ماهیانه</span>
                                        <strong>{{ numberFormat(unit.maximum_monthly_rent) }} <small v-if="unit.maximum_monthly_rent">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>اجاره سرقفلی سالانه مالک</span>
                                        <strong>{{ numberFormat(unit.owner_annual_goodwill_rent) }} <small v-if="unit.owner_annual_goodwill_rent">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>نرخ پیشنهادی مالک</span>
                                        <strong>{{ numberFormat(unit.sale_price_suggested_owner) }} <small v-if="unit.sale_price_suggested_owner">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>رهن پیشنهادی مالک</span>
                                        <strong>{{ numberFormat(unit.owner_proposed_mortgage) }} <small v-if="unit.owner_proposed_mortgage">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>اجاره پیشنهادی مالک</span>
                                        <strong>{{ numberFormat(unit.rent_proposed_owner) }} <small v-if="unit.rent_proposed_owner">ریال</small></strong>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-12">
                                    <q-icon name="credit_card"/>
                                    <div>
                                        <span>مبلغ شارژ</span>
                                        <strong>{{ numberFormat(unit.charge_amount) }} <small v-if="unit.charge_amount">ریال</small></strong>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </q-expansion-item>
                </q-card-section>
            </q-card>

            <!-- طرف اول -->
            <div class="row items-center">
                <div class="col-lg-8 col-12">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="account_box"/>
                    </span>
                        </div>
                        <div class="rb-title">
                            <h1>اطلاعات مالک</h1>
                        </div>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-md">
                <q-card-section>
                    <q-list dense>
                        <q-item v-for="owner in transfer.owners">
                            <q-item-section>
                                {{ owner.user.name }} - {{ owner.user.mobile }} {{ owner.user.national_code ? '- ' + owner.user.national_code : '' }}
                            </q-item-section>
                        </q-item>
                    </q-list>
                </q-card-section>
            </q-card>
            <!-- طرف دوم -->
            <div class="row items-center">
                <div class="col-lg-8 col-12">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                                <span>
                                    <q-icon color="teal-10" name="account_box"/>
                                </span>
                        </div>
                        <div class="rb-title">
                            <h1>اطلاعات مستاجر</h1>
                        </div>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-md">
                <q-card-section>
                    <q-list dense>
                        <q-item v-for="occupant in transfer.occupants">
                            <q-item-section>
                                {{ occupant.user.name }} - {{ occupant.user.mobile }} {{ occupant.user.national_code ? '- ' + occupant.user.national_code : '' }}
                            </q-item-section>
                        </q-item>
                    </q-list>
                </q-card-section>
            </q-card>


            <q-card class="q-mb-lg">
                <q-card-section>
                    <q-expansion-item
                        v-model="transfer_expand"
                        icon="domain"
                        label="اطلاعات نقل و انتقالات واحد"
                        caption=""
                    >
                        <br>
                        <div class="row q-col-gutter-sm">
                            <div class="col-lg-8 col-12">
                                <label>تاریخ قرارداد</label>
                                <div class="row q-col-gutter-sm">
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.year"
                                            :options="year_options"
                                            icon="calendar_today"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.month"
                                            :options="month_options"
                                            icon="date_range"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.day"
                                            :options="day_options"
                                            icon="event"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <label>شماره قرارداد</label>
                                <q-input outlined color="teal-10" v-model="form.contract_number">
                                    <template v-slot:prepend>
                                        <q-icon name="dialpad"/>
                                    </template>
                                </q-input>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <label>مدت اجاره به حروف</label>
                                <q-input outlined color="teal-10" v-model="form.duration">
                                    <template v-slot:prepend>
                                        <q-icon name="dialpad"/>
                                    </template>
                                </q-input>
                            </div>
                            <div class="col-lg-6 col-12">
                                <label>اجاره از</label>
                                <div class="row q-col-gutter-sm">
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.rent_from_year"
                                            :options="year_options"
                                            icon="calendar_today"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.rent_from_month"
                                            :options="month_options"
                                            icon="date_range"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.rent_from_day"
                                            :options="day_options"
                                            icon="event"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <label>اجاره تا</label>
                                <div class="row q-col-gutter-sm">
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.rent_to_year"
                                            :options="year_options2"
                                            icon="calendar_today"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.rent_to_month"
                                            :options="month_options"
                                            icon="date_range"
                                        />
                                    </div>
                                    <div class="col-lg-4 co-12">
                                        <FieldSelect
                                            v-model="form.rent_to_day"
                                            :options="day_options"
                                            icon="event"
                                        />
                                    </div>
                                </div>
                            </div>
<!--                            <div class="col-lg-3 col-md-6 col-12">
                                <label>شاهده اول</label>
                                <q-input outlined color="teal-10" v-model="form.first_witness">
                                    <template v-slot:prepend>
                                        <q-icon name="account_circle"/>
                                    </template>
                                </q-input>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <label>شاهده دوم</label>
                                <q-input outlined color="teal-10" v-model="form.second_witness">
                                    <template v-slot:prepend>
                                        <q-icon name="account_circle"/>
                                    </template>
                                </q-input>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <label>وکیل</label>
                                <q-input outlined color="teal-10" v-model="form.lawyer">
                                    <template v-slot:prepend>
                                        <q-icon name="gavel"/>
                                    </template>
                                </q-input>
                            </div>-->
                            <div class="col-lg-3 col-md-6 col-12">
                                <label>تنظیم کننده</label>
                                <q-input outlined color="teal-10" v-model="form.regulator">
                                    <template v-slot:prepend>
                                        <q-icon name="account_circle"/>
                                    </template>
                                </q-input>
                            </div>
                            <div class="col-lg-4 col-md-6 col-12">
                                <label>مبلغ رهن (ریال)</label>
                                <FieldNumber
                                    v-model="form.mortgage_amount"
                                    icon="credit_card"
                                />
                            </div>
                            <div class="col-lg-4 col-md-6 col-12">
                                <label>مبلغ اجاره (ریال)</label>
                                <FieldNumber
                                    v-model="form.rental_amount"
                                    icon="credit_card"
                                />
                            </div>
                            <div class="col-lg-4 col-md-6 col-12">
                                <label>شماره کارت</label>
                                <FieldInput
                                    v-model="form.card_number"
                                    icon="credit_card"
                                />
                            </div>
                            <div class="col-md-3 col-12">
                                <label>شماره چک</label>
                                <FieldInput
                                    bottom-slots
                                    v-model="form.check_number"
                                    icon="credit_card"
                                    right-hint="تضمین"
                                />
                            </div>
                            <div class="col-md-3 col-12">
                                <label>بانک</label>
                                <FieldInput
                                    bottom-slots
                                    v-model="form.bank"
                                    icon="credit_card"
                                    right-hint="تضمین"
                                />
                            </div>
                            <div class="col-md-3 col-12">
                                <label>مبلغ</label>
                                <FieldNumber
                                    bottom-slots
                                    v-model="form.warranty_amount"
                                    icon="credit_card"
                                    right-hint="تضمین"
                                    left-hint="ریال"
                                />
                            </div>
                            <div class="col-md-3 col-12">
                                <label>حساب جاری</label>
                                <FieldInput
                                    bottom-slots
                                    v-model="form.current_account"
                                    icon="credit_card"
                                    right-hint="تضمین"
                                />
                            </div>
                            <div class="col-md-3 col-12">
                                <label>شغل</label>
                                <FieldSelect
                                    clearable
                                    v-model="form.job"
                                    :options="job_options"
                                    icon="date_range"
                                />
                            </div>
                            <div class="col-12">
                                <div class="q-my-md">
                                    <div class="rb-add">
                                        <q-btn
                                            type="submit"
                                            class="rb-action rb-large"
                                            label="ویرایش قرارداد"
                                            @click="updateTransfer"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-expansion-item>
                </q-card-section>
            </q-card>
            <UploadDocuments
                title="فایل های بارگذاری شده نقل و انتقالات"
                description="مدارک و فایل های مربوط به نقل و انتقال را می توانید بارگذاری نمایید"
                :model-type="modelType"
                :id="transfer.id"
                :redirect-route="route('admin.transfers.rent.edit',transfer.id)"
            />
        </q-page>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage, Link, router} from "@inertiajs/vue3";
import FieldInput from "@/Components/FieldInput.vue";
import FieldSelect from "@/Components/FieldSelect.vue";
import {ref} from "vue";
import {route} from "ziggy-js";
import {debounce} from "lodash";
import {formHandler, goToUrl, numberFormat, showNotify, tableColumns} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";
import UploadDocuments from "@/Components/UploadDocuments.vue";
import Documents from "@/Components/UploadDocuments.vue";

export default {
    methods: {goToUrl, numberFormat, route},
    components: {
        Documents,
        UploadDocuments,
        FieldNumber,
        Head,
        AdminLayout,
        FieldInput,
        FieldSelect,
        Link
    },
    setup() {

        const page = usePage();
        const currentSection = page.props.currentSection;
        const transfer = ref(page.props.transfer.data);
        console.log(transfer)
        const unit = ref(page.props.unit.data);
        const unit_number = ref();
        const unit_expand = ref(true);
        const transfer_expand = ref(true);
        const rowsFrom = ref([]);
        const rowsTo = ref([]);
        const dialogAddOwner = ref(false);
        const typeOwner = ref();
        const owner = ref();
        const {form, submitForm, isLoading} = formHandler({
            year: ref(transfer.value.doing_at.object.year),
            month: ref(transfer.value.doing_at.object.month),
            day: ref(transfer.value.doing_at.object.day),
            rent_from_year: ref(transfer.value.rental_start_at.object.year),
            rent_from_month: ref(transfer.value.rental_start_at.object.month),
            rent_from_day: ref(transfer.value.rental_start_at.object.day),
            rent_to_year: ref(transfer.value.rental_end_at.object.year),
            rent_to_month: ref(transfer.value.rental_end_at.object.month),
            rent_to_day: ref(transfer.value.rental_end_at.object.day),
            contract_number: ref(transfer.value.contract_number),
            first_witness: ref(transfer.value.first_witness),
            second_witness: ref(transfer.value.second_witness),
            lawyer: ref(transfer.value.lawyer),
            regulator: ref(transfer.value.regulator),
            rental_amount: ref(transfer.value.rental_amount),
            mortgage_amount: ref(transfer.value.mortgage_amount),
            duration: ref(transfer.value.duration),
            check_number: ref(transfer.value.check_number),
            bank: ref(transfer.value.bank),
            warranty_amount: ref(transfer.value.warranty_amount),
            current_account: ref(transfer.value.current_account),
            card_number: ref(transfer.value.card_number),
            job: ref(transfer.value.job_type.map((item) => item.value)[0]),
        },'put');
        const updateTransfer = () => {
            try {
                submitForm('admin.transfers.rent.update',transfer.value.id);
            }
            catch (error){

            }
        }

        return {
            currentSection,
            unit_number,
            isLoading,
            unit_expand,
            transfer_expand,
            unit,
            rowsFrom,
            rowsTo,
            dialogAddOwner,
            form,
            day_options: page.props.days,
            month_options: page.props.months,
            year_options: page.props.years,
            year_options2: page.props.years2,
            job_options: page.props.jobs,
            gender_options: page.props.genders,
            typeOwner,
            updateTransfer,
            owner,
            transfer,
            modelType: ref(page.props.modelType)
        }
    }
};
</script>
<style scoped>
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

.rb-add-section .q-btn :deep(.q-icon) {
    margin: 0 0 0 5px;
    font-size: 1.25rem;
}

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

.q-expansion-item :deep(.q-focus-helper) {
    border-radius: .25rem;
}

.q-expansion-item :deep(.cursor-pointer) {
    padding: 0 0 0 5px;
}

.rb-info-section ul {
    padding: 15px 0 0;
}

.rb-info-section ul li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 15px;
}

.rb-info-section ul li i {
    font-size: 1.5rem;
    color: rgb(0, 75, 65);
}

.rb-info-section ul li span {
    display: block;
    font-size: .90rem;
    color: rgb(120, 120, 120);
}

.rb-info-section ul li strong {
    display: block;
    font-size: .95rem;
}

.rb-info-section ul li strong small {
    font-family: 'Estedad-Medium';
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
