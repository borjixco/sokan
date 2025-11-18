<template>
    <Head :title="`${currentSection.pageTitle} - افزودن`"/>
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
                            <p>در این بخش می توانید نقل و انتقال را اضافه کنید</p>
                        </div>
                    </div>
                </div>
            </div>
            <q-card class="q-mb-lg">
                <q-card-section>
                    <div class="rb-search-section">
                        <div class="row q-col-gutter-sm">
                            <div class="col-lg-10 col-md-8 col-12">
                                <FieldInput
                                    dense
                                    color="teal-10"
                                    placeholder="جستجو شماره واحد"
                                    v-model="unit_number"
                                    icon="segment"
                                    autofocus
                                    @keyup.enter="searchUnit"
                                />
                            </div>
                            <div class="col-lg-2 col-md-4 col-12">
                                <q-btn type="submit" class="rb-search" color="teal-10" label="جستجو واحد"
                                       @click="searchUnit" :loading="isLoading"/>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>

            <div v-if="showDetails">
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
                                            <strong>{{ unit.type.label }}</strong>
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
                                            <strong>{{ unit.position?.label }}</strong>
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
                                            <strong>{{ unit.floor.label }}</strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="roofing"/>
                                        <div>
                                            <span>وضعیت سقف</span>
                                            <strong>{{ unit.roof.label }}</strong>
                                        </div>
                                    </li>
                                    <li class="col-12">
                                        <q-icon name="gavel"/>
                                        <div>
                                            <span>وضعیت واحد تجاری</span>
                                            <strong>{{ unit.status.label }}</strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>ارزش هر متر از واحد تجاری</span>
                                            <strong>{{ numberFormat(unit.value_per_meter) }} <small
                                                v-if="unit.value_per_meter">ریال</small></strong>
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
                                            <strong>{{ numberFormat(unit.now_total_value) }} <small
                                                v-if="unit.now_total_value">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>ارزش هر متر به نرخ روز</span>
                                            <strong>{{ numberFormat(unit.now_value_per_meter) }} <small
                                                v-if="unit.now_value_per_meter">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>حداکثر رهن کامل</span>
                                            <strong>{{ numberFormat(unit.maximum_full_mortgage) }} <small
                                                v-if="unit.maximum_full_mortgage">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>حداکثر اجاره ماهیانه</span>
                                            <strong>{{ numberFormat(unit.maximum_monthly_rent) }} <small
                                                v-if="unit.maximum_monthly_rent">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>اجاره سرقفلی سالانه مالک</span>
                                            <strong>{{ numberFormat(unit.owner_annual_goodwill_rent) }} <small
                                                v-if="unit.owner_annual_goodwill_rent">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>نرخ پیشنهادی مالک</span>
                                            <strong>{{ numberFormat(unit.sale_price_suggested_owner) }} <small
                                                v-if="unit.sale_price_suggested_owner">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>رهن پیشنهادی مالک</span>
                                            <strong>{{ numberFormat(unit.owner_proposed_mortgage) }} <small
                                                v-if="unit.owner_proposed_mortgage">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>اجاره پیشنهادی مالک</span>
                                            <strong>{{ numberFormat(unit.rent_proposed_owner) }} <small
                                                v-if="unit.rent_proposed_owner">ریال</small></strong>
                                        </div>
                                    </li>
                                    <li class="col-lg-4 col-md-6 col-12">
                                        <q-icon name="credit_card"/>
                                        <div>
                                            <span>مبلغ شارژ</span>
                                            <strong>{{ numberFormat(unit.charge_amount) }} <small
                                                v-if="unit.charge_amount">ریال</small></strong>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </q-expansion-item>
                    </q-card-section>
                </q-card>
                <!-- مالک -->
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
                                <p>لیست کاربران با کلیک بر روی "جستجو مالک" در دسترس است.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="text-left">
                            <div class="rb-add" v-if="!owners">
                                <q-btn class="rb-action" label="جستجوی مالک" @click="dialogFrom = true"/>
                            </div>
                        </div>
                    </div>
                </div>
                <q-dialog v-model="dialogFrom">
                    <q-card style="width: 100%; max-width: 750px">
                        <q-card-section>
                            <div class="row items-center">
                                <div class="col-lg-9 col-12">
                                    <div class="text-h6">لیست کاربران</div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <q-btn class="rb-action" label="افزودن مالک"
                                           @click="dialogAddOwnerHandle('from'); dialogFrom = false"/>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <q-input
                                        dense
                                        input-class="text-left"
                                        color="teal-10"
                                        label="جستجو کاربر"
                                        v-model="searchQueryFrom"
                                        @keyup="debouncedSearchFrom"
                                    />
                                </div>
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-none">
                            <q-table
                                flat
                                :rows="rowsFrom"
                                :columns="columnsFrom"
                                :rows-per-page="10"
                                :rows-per-page-options="[10]"
                                :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                                hide-pagination
                            >
                                <template v-slot:body="props">
                                    <q-tr>
                                        <q-td>
                                            <q-checkbox
                                                color="teal-10"
                                                v-model="props.row.selected"
                                                @update:model-value="() => updateSelectionFrom(props.row)"
                                            />
                                        </q-td>
                                        <q-td align="center">{{ props.row.name }}</q-td>
                                        <q-td align="center">{{ props.row.phone }}</q-td>
                                        <q-td align="center">{{ props.row.national_code }}</q-td>
                                    </q-tr>
                                </template>
                            </q-table>
                        </q-card-section>
                    </q-card>
                </q-dialog>
                <div v-if="owners">
                    <q-card class="q-mb-md">
                        <q-card-section>
                            <q-list dense>
                                <q-item v-for="owner in owners">
                                    <q-item-section>
                                        {{ owner.user.name }} - {{ owner.user.mobile }}
                                        {{ owner.user.national_code ? '- ' + owner.user.national_code : '' }} -
                                        {{ owner.quota }} سهم
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </q-card-section>
                    </q-card>
                </div>
                <div v-if="selectedFrom.length">
                    <q-card class="q-mb-md">
                        <q-card-section>
                            <q-list dense>
                                <q-item v-for="(item, index) in selectedFrom" :key="index">
                                    <q-item-section>
                                        <div class="rb-user">
                                            <ul>
                                                <li>
                                                    <q-icon name="account_box"/>
                                                    {{ item.name }}
                                                </li>
                                                <li>
                                                    <q-icon name="smartphone"/>
                                                    {{ item.phone }}
                                                </li>
                                            </ul>
                                        </div>
                                        {{ item.national_code ? '- ' + item.national_code : '' }}
                                    </q-item-section>
                                    <q-item-section avatar>
                                        <q-btn
                                            flat
                                            round
                                            icon="cancel"
                                            class="rb-delete"
                                            @click="removeItemFrom(index)"
                                        />
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </q-card-section>
                    </q-card>
                </div>
                <!-- طرف مستاجر -->
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
                                <p>لیست کاربران با کلیک بر روی "جستجو مستاجر" در دسترس است.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="text-left">
                            <div class="rb-add">
                                <q-btn class="rb-action" label="جستجوی مستاجر" @click="dialogTo = true"/>
                            </div>
                        </div>
                    </div>
                </div>
                <q-dialog v-model="dialogTo">
                    <q-card style="width: 100%; max-width: 750px">
                        <q-card-section>
                            <div class="row items-center">
                                <div class="col-lg-9 col-12">
                                    <div class="text-h6">لیست کاربران</div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="rb-add">
                                        <q-btn class="rb-action" label="افزودن مستاجر"
                                               @click="dialogAddOwnerHandle('to'); dialogTo = false"/>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <q-input
                                        dense
                                        input-class="text-left"
                                        color="teal-10"
                                        label="جستجو کاربر"
                                        v-model="searchQueryTo"
                                        @keyup="debouncedSearchTo"
                                    />
                                </div>
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-none">
                            <q-table
                                flat
                                :rows="rowsTo"
                                :columns="columnsTo"
                                :rows-per-page="10"
                                :rows-per-page-options="[10]"
                                :no-data-label="'رکوردی جهت نمایش وجود ندارد.'"
                                hide-pagination
                            >
                                <template v-slot:body="props">
                                    <q-tr>
                                        <q-td>
                                            <q-checkbox
                                                color="teal-10"
                                                v-model="props.row.selected"
                                                @update:model-value="() => updateSelectionTo(props.row)"
                                            />
                                        </q-td>
                                        <q-td align="center">{{ props.row.name }}</q-td>
                                        <q-td align="center">{{ props.row.phone }}</q-td>
                                        <q-td align="center">{{ props.row.national_code }}</q-td>
                                    </q-tr>
                                </template>
                            </q-table>
                        </q-card-section>
                    </q-card>
                </q-dialog>
                <div v-if="selectedTo.length">
                    <q-card class="q-mb-md">
                        <q-card-section>
                            <q-list dense>
                                <q-item v-for="(item, index) in selectedTo" :key="index">
                                    <q-item-section>
                                        <div class="rb-user">
                                            <ul>
                                                <li>
                                                    <q-icon name="account_box"/>
                                                    {{ item.name }}
                                                </li>
                                                <li>
                                                    <q-icon name="smartphone"/>
                                                    {{ item.phone }}
                                                </li>
                                            </ul>
                                        </div>
                                        {{ item.national_code ? '- ' + item.national_code : '' }}
                                    </q-item-section>
                                    <q-item-section avatar>
                                        <q-btn
                                            flat
                                            round
                                            icon="cancel"
                                            class="rb-delete"
                                            @click="removeItemTo(index)"
                                        />
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </q-card-section>
                    </q-card>
                </div>
                <!-- افزودن کاربر -->
                <q-dialog v-model="dialogAddOwner" full-width>
                    <q-card class="q-mb-lg">
                        <q-card-section>
                            <div class="row q-col-gutter-sm">
                                <div class="col-12">
                                    <div class="rb-subtitle-section">
                                        <div class="rb-icon">
                                    <span>
                                        <q-icon name="contacts"/>
                                    </span>
                                        </div>
                                        <div class="rb-subtitle">
                                            <h1>اطلاعات عمومی</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <label>نام و نام خانوادگی</label>
                                    <FieldInput
                                        v-model="form.name"
                                        icon="contacts"
                                    />
                                </div>
                                <div class="col-lg-2 col-md-6 col-12">
                                    <label>کد ملی</label>
                                    <FieldInput
                                        v-model="form.national_code"
                                        icon="badge"
                                    />
                                </div>
                                <div class="col-lg-2 col-md-6 col-12">
                                    <label>نام پدر</label>
                                    <FieldInput
                                        v-model="form.father_name"
                                        icon="supervisor_account"
                                    />
                                </div>
                                <div class="col-lg-2 col-md-6 col-12">
                                    <label>جنسیت</label>
                                    <FieldSelect
                                        v-model="form.gender"
                                        :options="gender_options"
                                        icon="female"
                                    />
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <label>تلفن ثابت</label>
                                    <FieldInput
                                        v-model="form.tel"
                                        icon="phone_in_talk"
                                    />
                                </div>
                                <div class="col-lg-6 col-12">
                                    <label>تاریخ تولد</label>
                                    <div class="row q-col-gutter-sm">
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form.birth_day"
                                                :options="day_options"
                                                icon="calendar_today"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form.birth_month"
                                                :options="month_options"
                                                icon="date_range"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form.birth_year"
                                                :options="year_options"
                                                icon="event"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <label>آدرس محل سکونت</label>
                                    <FieldInput
                                        v-model="form.address"
                                        icon="location_on"
                                    />
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <label>شماره موبایل</label>
                                    <FieldInput
                                        v-model="form.mobile"
                                        icon="smartphone"
                                    />
                                </div>
                                <div class="col-lg-2 col-md-6 col-12">
                                    <label>شماره موبایل(ضروری)</label>
                                    <FieldInput
                                        v-model="form.mobile2"
                                        icon="smartphone"
                                    />
                                </div>
                                <div class="col-lg-3 col-6 col-12">
                                    <label>شغل</label>
                                    <FieldSelect
                                        v-model="form.job_type"
                                        :options="job_options"
                                        icon="event"
                                    />
                                </div>
                                <div class="col-12">
                                    <div class="q-my-md">
                                        <div class="rb-add">
                                            <q-btn
                                                type="submit"
                                                class="rb-action rb-large"
                                                label="افزودن"
                                                @click="addUser(typeOwner)"
                                                :loading="isLoading2"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                </q-dialog>
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
                                                v-model="form2.year"
                                                :options="year_options"
                                                icon="calendar_today"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form2.month"
                                                :options="month_options"
                                                icon="date_range"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form2.day"
                                                :options="day_options"
                                                icon="event"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 col-12">
                                    <label>شماره قرارداد</label>
                                    <q-input outlined color="teal-10" v-model="form2.contract_number">
                                        <template v-slot:prepend>
                                            <q-icon name="dialpad"/>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="col-lg-2 col-md-6 col-12">
                                    <label>مدت اجاره به حروف</label>
                                    <q-input outlined color="teal-10" v-model="form2.duration">
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
                                                v-model="form2.rent_from_year"
                                                :options="year_options"
                                                icon="calendar_today"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form2.rent_from_month"
                                                :options="month_options"
                                                icon="date_range"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form2.rent_from_day"
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
                                                v-model="form2.rent_to_year"
                                                :options="year_options2"
                                                icon="calendar_today"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form2.rent_to_month"
                                                :options="month_options"
                                                icon="date_range"
                                            />
                                        </div>
                                        <div class="col-lg-4 co-12">
                                            <FieldSelect
                                                v-model="form2.rent_to_day"
                                                :options="day_options"
                                                icon="event"
                                            />
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="col-lg-3 col-md-6 col-12">
                                    <label>شاهده اول</label>
                                    <q-input outlined color="teal-10" v-model="form2.first_witness">
                                        <template v-slot:prepend>
                                            <q-icon name="account_circle"/>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <label>شاهده دوم</label>
                                    <q-input outlined color="teal-10" v-model="form2.second_witness">
                                        <template v-slot:prepend>
                                            <q-icon name="account_circle"/>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <label>وکیل</label>
                                    <q-input outlined color="teal-10" v-model="form2.lawyer">
                                        <template v-slot:prepend>
                                            <q-icon name="gavel"/>
                                        </template>
                                    </q-input>
                                </div>-->
                                <div class="col-lg-3 col-md-6 col-12">
                                    <label>تنظیم کننده</label>
                                    <q-input outlined color="teal-10" v-model="form2.regulator">
                                        <template v-slot:prepend>
                                            <q-icon name="account_circle"/>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="col-lg-4 col-12">
                                    <label>مبلغ رهن (ریال)</label>
                                    <FieldNumber
                                        v-model="form2.mortgage_amount"
                                        icon="credit_card"
                                    />
                                </div>
                                <div class="col-lg-4 col-12">
                                    <label>مبلغ اجاره (ریال)</label>
                                    <FieldNumber
                                        v-model="form2.rental_amount"
                                        icon="credit_card"
                                    />
                                </div>
                                <div class="col-lg-4 col-12">
                                    <label>شماره کارت</label>
                                    <FieldInput
                                        v-model="form2.card_number"
                                        icon="credit_card"
                                    />
                                </div>
                                <div class="col-md-3 col-12">
                                    <label>شماره چک</label>
                                    <FieldInput
                                        bottom-slots
                                        v-model="form2.check_number"
                                        icon="credit_card"
                                        right-hint="تضمین"
                                    />
                                </div>
                                <div class="col-md-3 col-12">
                                    <label>بانک</label>
                                    <FieldInput
                                        bottom-slots
                                        v-model="form2.bank"
                                        icon="credit_card"
                                        right-hint="تضمین"
                                    />
                                </div>
                                <div class="col-md-3 col-12">
                                    <label>مبلغ</label>
                                    <FieldNumber
                                        bottom-slots
                                        v-model="form2.warranty_amount"
                                        icon="credit_card"
                                        right-hint="تضمین"
                                        left-hint="ریال"
                                    />
                                </div>
                                <div class="col-md-3 col-12">
                                    <label>حساب جاری</label>
                                    <FieldInput
                                        bottom-slots
                                        v-model="form2.current_account"
                                        icon="credit_card"
                                        right-hint="تضمین"
                                    />
                                </div>

                                <div class="col-md-3 col-12">
                                    <label>شغل</label>
                                    <FieldSelect
                                        v-model="form2.job"
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
                                                label="افزودن قرارداد"
                                                @click="addTransfer"
                                                :loading="isLoading"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </q-expansion-item>
                    </q-card-section>
                </q-card>
            </div>
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
import {formHandler, numberFormat, showNotify, tableColumns} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";

export default {
    methods: {numberFormat, route},
    components: {
        FieldNumber,
        Head,
        AdminLayout,
        FieldInput,
        FieldSelect,
        Link
    },
    setup() {

        const page = usePage();
        const showDetails = ref(false);
        const general = page.props.general;
        const currentSection = page.props.currentSection;
        const unit = ref();
        const unit_number = ref();
        const isLoading = ref(false);
        const unit_expand = ref(false);
        const transfer_expand = ref(false);
        const selectedFrom = ref([]);
        const selectedTo = ref([]);
        const searchQueryFrom = ref('');
        const searchQueryTo = ref('');
        const columnsFrom = tableColumns(['انتخاب', 'نام', 'شماره همراه', 'کدملی']);
        const columnsTo = tableColumns(['انتخاب', 'نام', 'شماره همراه', 'کدملی']);
        const fromIdSelected = ref([])
        const toIdSelected = ref([])
        const rowsFrom = ref([]);
        const rowsTo = ref([]);
        const dialogFrom = ref(false);
        const dialogTo = ref(false);
        const dialogAddOwner = ref(false);
        const typeOwner = ref();
        const owners = ref();


        const {form, submitForm, isLoading: isLoading2} = formHandler({
            name: ref(''),
            national_code: ref(''),
            father_name: ref(''),
            birth_day: ref(''),
            birth_month: ref(''),
            birth_year: ref(''),
            gender: ref(''),
            address: ref(''),
            mobile: ref(''),
            mobile2: ref(''),
            tel: ref(''),
            job_type: ref(''),
            redirect: ref(false),
        });

        const {form: form2, submitForm: submitForm2, isLoading: isLoading3} = formHandler({
            unit_id: ref(),
            from_owner_id: ref(),
            to_user_id: ref(),
            year: ref(),
            month: ref(),
            day: ref(),
            rent_from_year: ref(),
            rent_from_month: ref(),
            rent_from_day: ref(),
            rent_to_year: ref(),
            rent_to_month: ref(),
            rent_to_day: ref(),
            contract_number: ref(),
            first_witness: ref(),
            second_witness: ref(),
            lawyer: ref(),
            regulator: ref(),
            mortgage_amount: ref(),
            rental_amount: ref(),
            duration: ref(),
            check_number: ref(),
            bank: ref(),
            warranty_amount: ref(),
            current_account: ref(),
            card_number: ref(),
            job: ref(),
        });

        const searchUnit = () => {
            isLoading.value = true
            unit_expand.value = false;
            showDetails.value = false;
            transfer_expand.value = false;

            fetch(route('admin.transfers.search.unit', {'unit': unit_number.value}))
                .then(response => response.json())
                .then(function (res) {
                    isLoading.value = false
                    if (res.data.unit) {
                        unit.value = res.data.unit;
                        unit_expand.value = true;
                        transfer_expand.value = true;
                        showDetails.value = true;
                        form2.unit_id = unit.value.id
                        owners.value = res.data.owners;
                        form2.from_owner_id = res.data?.owners ? res.data.owners.map(owner => owner.id) : null;
                        console.log(owners);
                        console.log(form2.from_owner_id);
                    } else {
                        showNotify('واحدی پیدا نشد', 'negative');
                    }
                });
        }

        const searchServer = (query, role) => {
            fetch(route('admin.transfers.search.user', {'q': query, 'role': role}))
                .then(response => response.json())
                .then(function (data) {
                    rowsFrom.value = data
                });
        };

        const debouncedSearchFrom = debounce(() => {
            if (searchQueryFrom.value.trim() !== '') {
                searchServer(searchQueryFrom.value, 'owner');
            }
        }, 500);

        const debouncedSearchTo = debounce(() => {
            if (searchQueryTo.value.trim() !== '') {
                searchServer2(searchQueryTo.value, 'occupant');
            }
        }, 500);

        const searchServer2 = (query, role) => {
            fetch(route('admin.transfers.search.user', {'q': query, 'role': role}))
                .then(response => response.json())
                .then(function (data) {
                    rowsTo.value = data
                });
        };

        const updateSelectionFrom = (row) => {
            if (!general.multipleOwner) {
                // اگر انتخاب چندگانه غیرفعال باشد، همه انتخاب‌ها را ریست کن
                rowsFrom.value.forEach(item => item.selected = false);

                // سپس فقط آیتم فعلی را انتخاب کن
                row.selected = true;
                selectedFrom.value = [{name: row.name, phone: row.phone, national_code: row.national_code}];
                fromIdSelected.value = [row.id];
            } else {
                // اگر انتخاب چندگانه فعال باشد، رفتار قبلی را حفظ کن
                if (row.selected) {
                    selectedFrom.value.push({name: row.name, phone: row.phone, national_code: row.national_code});
                    fromIdSelected.value.push(row.id);
                } else {
                    selectedFrom.value = selectedFrom.value.filter(item => item.phone !== row.phone);
                    fromIdSelected.value = fromIdSelected.value.filter(id => id !== row.id);
                }
            }
            if (!general.multipleOwner) {
                dialogFrom.value = false;
                searchQueryFrom.value = null;
                rowsFrom.value = [];
            }
            form2.from_owner_id = fromIdSelected.value

        };

        const removeItemFrom = (index) => {
            selectedFrom.value.splice(index, 1);
            fromIdSelected.value.splice(index, 1)
        };

        const updateSelectionTo = (row) => {
            if (!general.multipleOwner) {
                // اگر انتخاب چندگانه غیرفعال باشد، همه انتخاب‌ها را ریست کن
                rowsTo.value.forEach(item => item.selected = false);

                // سپس فقط آیتم فعلی را انتخاب کن
                row.selected = true;
                selectedTo.value = [{name: row.name, phone: row.phone, national_code: row.national_code}];
                toIdSelected.value = [row.id];
            } else {
                // اگر انتخاب چندگانه فعال باشد، رفتار قبلی را حفظ کن
                if (row.selected) {
                    selectedTo.value.push({name: row.name, phone: row.phone, national_code: row.national_code});
                    toIdSelected.value.push(row.id);
                } else {
                    selectedTo.value = selectedTo.value.filter(item => item.phone !== row.phone);
                    toIdSelected.value = toIdSelected.value.filter(id => id !== row.id);
                }
            }
            if (!general.multipleOwner) {
                dialogTo.value = false;
                searchQueryTo.value = null;
                rowsTo.value = [];
            }
            form2.to_user_id = toIdSelected.value

        };

        const removeItemTo = (index) => {
            selectedTo.value.splice(index, 1);
            toIdSelected.value.splice(index, 1)
        };


        const resetForm = () => {
            form.name = '';
            form.national_code = '';
            form.father_name = '';
            form.birth_day = '';
            form.birth_month = '';
            form.birth_year = '';
            form.gender = '';
            form.address = '';
            form.mobile = '';
            form.mobile2 = '';
            form.tel = '';
            form.job_type = '';
            form.redirect = false;
        };

        const addUser = async (type) => {
            try {
                if (type === 'from') {
                    await submitForm('admin.owners.store');
                } else {
                    await submitForm('admin.occupants.store');
                }
                console.log(window?.formData)
                dialogAddOwner.value = false;
                const data = {
                    id: window?.formData?.id,
                    name: window?.formData?.name,
                    national_code: window?.formData?.national_code,
                    phone: window?.formData?.mobile
                };
                console.log(data)
                if (type == 'from') {
                    updateSelectionFrom(data);
                } else {
                    updateSelectionTo(data);
                }
                resetForm();
            } catch (error) {
                console.error('خطا در ارسال فرم:', error);
            }
        }

        const dialogAddOwnerHandle = (type) => {
            dialogAddOwner.value = true;
            typeOwner.value = type;
        }


        const addTransfer = () => {
            try {
                submitForm2('admin.transfers.rent.store');
            } catch (error) {

            }
        }

        return {
            currentSection,
            showDetails,
            searchUnit,
            unit_number,
            isLoading,
            isLoading2,
            isLoading3,
            unit_expand,
            transfer_expand,
            unit,
            selectedFrom,
            selectedTo,
            dialogFrom,
            dialogTo,
            searchQueryFrom,
            searchQueryTo,
            debouncedSearchFrom,
            debouncedSearchTo,
            rowsFrom,
            rowsTo,
            columnsFrom,
            columnsTo,
            updateSelectionFrom,
            updateSelectionTo,
            removeItemFrom,
            removeItemTo,
            dialogAddOwner,
            addUser,
            form,
            form2,
            day_options: page.props.days,
            month_options: page.props.months,
            year_options: page.props.years,
            year_options2: page.props.years2,
            job_options: page.props.jobs,
            gender_options: page.props.genders,
            dialogAddOwnerHandle,
            typeOwner,
            addTransfer,
            owners
        }
    }
};
</script>
<style scoped>
.q-table__container :deep(.q-table__bottom--nodata) {
    justify-content: center;
    align-items: center;
    margin: 15px 0 0;
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

.rb-user ul {
    display: flex;
    align-items: center;
    gap: 15px
}

.rb-user ul li {
    display: inline-block;
}

.q-list :deep(.q-item__section--avatar) {
    align-items: end;
    padding: 0;
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
