<template>
    <Head title="واحدهای تجاری - ویرایش"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col-lg-8">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="group"/>
                    </span>
                        </div>
                        <div class="rb-title">
                            <h1>ویرایش واحد تجاری</h1>
                            <p>لطفاً اطلاعات را با دقت تکمیل کرده و قیمت‌ها را به ریال ثبت کنید.</p>
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
                            <FieldNumber
                                outlined
                                v-model="form.unit_number"
                                icon="segment"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>متراژ</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.meterage"
                                @keyup="calculateNowTotalValueMeterage"
                                icon="crop"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>نوع واحد</label>
                            <q-select
                                outlined
                                color="teal-10"
                                dropdown-icon="keyboard_arrow_down"
                                v-model="form.unit_type"
                                :options="unit_type_options"
                                option-label="label"
                                option-value="value"
                                emit-value
                                map-options
                            >
                                <template v-slot:prepend>
                                    <q-icon name="domain"/>
                                </template>
                            </q-select>
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>تلفن واحد تجاری</label>
                            <q-input outlined color="teal-10" v-model="form.tel">
                                <template v-slot:prepend>
                                    <q-icon name="smartphone"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-2 col-6 col-12">
                            <label>کد پستی</label>
                            <q-input outlined color="teal-10" v-model="form.postal_code">
                                <template v-slot:prepend>
                                    <q-icon name="location_on"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>موقعیت</label>
                            <q-select
                                outlined
                                dropdown-icon="keyboard_arrow_down"
                                color="teal-10"
                                v-model="form.position"
                                :options="position_options"
                                option-label="label"
                                option-value="value"
                                emit-value
                                map-options
                            >
                                <template v-slot:prepend>
                                    <q-icon name="my_location"/>
                                </template>
                            </q-select>
                        </div>
                        <div class="col-lg-2 col-12">
                            <label>شماره بدنه کنتور</label>
                            <q-input outlined color="teal-10" v-model="form.meter_serial_number">
                                <template v-slot:prepend>
                                    <q-icon name="dialpad"/>
                                </template>
                            </q-input>
                        </div>
                        <div class="col-lg-2 col-12">
                            <label>طبقه</label>
                            <q-select
                                outlined
                                dropdown-icon="keyboard_arrow_down"
                                color="teal-10"
                                v-model="form.floor"
                                :options="floor_options"
                                option-label="label"
                                option-value="value"
                                emit-value
                                map-options
                            >
                                <template v-slot:prepend>
                                    <q-icon name="filter_list"/>
                                </template>
                            </q-select>
                        </div>
                        <div class="col-lg-3 col-12">
                            <label>وضعیت سقف</label>
                            <q-select
                                outlined
                                dropdown-icon="keyboard_arrow_down"
                                color="teal-10"
                                v-model="form.roof"
                                :options="roof_options"
                                option-label="label"
                                option-value="value"
                                emit-value
                                map-options
                            >
                                <template v-slot:prepend>
                                    <q-icon name="roofing"/>
                                </template>
                            </q-select>
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
                                icon="crop"
                                left-hint="ریال"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>ارزش واحد تجاری فروخته شده</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.total_value"
                                icon="crop"
                                left-hint="ریال"
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
                                :left-hint="page.props.nowDateValue"
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
                                icon="add_card"
                                left-hint="ریال"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>حداکثر اجاره ماهیانه</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.maximum_monthly_rent"
                                icon="add_card"
                                left-hint="ریال"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>اجاره سرقفلی سالانه مالک</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.owner_annual_goodwill_rent"
                                icon="add_card"
                                left-hint="ریال"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>نرخ فروش پیشنهادی مالک</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.sale_price_suggested_owner"
                                icon="add_card"
                                left-hint="ریال"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>رهن پیشنهادی مالک</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.owner_proposed_mortgage"
                                icon="add_card"
                                left-hint="ریال"
                            />
                        </div>
                        <div class="col-lg-3 col-6 col-12">
                            <label>اجاره پیشنهادی مالک</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.rent_proposed_owner"
                                icon="add_card"
                                left-hint="ریال"
                            />
                        </div>
                        <div class="col-lg-6 col-6 col-12">
                            <label>مبلغ شارژ</label>
                            <FieldNumber
                                bottom-slots
                                v-model="form.charge_amount"
                                icon="add_card"
                                left-hint="ریال"
                                :right-hint="unit.formula_charge_amount ? 'مبلغ فرمول:'+numberFormat(unit.formula_charge_amount) : ''"
                            />
                        </div>
                        <div class="col-12">
                            <div class="q-my-md">
                                <div class="rb-add">
                                    <q-btn type="submit" class="rb-action rb-large" label="ویرایش واحد تجاری"
                                           @click="editUnit"
                                           :loading="isLoading"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <!-- اطلاعات مالک -->
            <div class="q-mb-lg" v-if="owners">
                <div class="rb-title-section">
                    <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="contacts"/>
                    </span>
                    </div>
                    <div class="rb-title">
                        <h1>اطلاعات مالک واحد تجاری</h1>
                        <p>لیست کاربران با کلیک بر روی "افزودن مالک" در دسترس است.</p>
                    </div>
                </div>
                <q-card v-for="owner in owners">
                    <q-card-section>
                        <div class="q-owner-section">
                            <div class="row items-center">
                                <div class="col-lg-12 col-12">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>نام و نام خانوادگی</span>
                                                <strong>{{ owner.user.name ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>کد ملی</span>
                                                <strong>{{ owner.user.national_code ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>شغل</span>
                                                <strong>{{
                                                        owner.user.job_types ? Object.values(owner.user.job_types).map(item => item.label).join(', ') : ''
                                                    }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>جنسیت</span>
                                                <strong>{{ owner.user.gender.label ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>تاریخ تولد</span>
                                                <strong>{{ owner.user.birth_date.jalali ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>نام پدر</span>
                                                <strong>{{ owner.user.father_name ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="q-data">
                                                <span>آدرس</span>
                                                <strong>{{ owner.user.address ?? 'نامشخص' }} </strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>شماره همراه</span>
                                                <strong>{{ owner.user.mobile ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>شماره همراه ۲ (اضطراری)</span>
                                                <strong>{{ owner.user.mobile2 ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>تلفن ثابت</span>
                                                <strong>{{ owner.user.tel ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>سهم</span>
                                                <strong>{{ owner.quota ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>نام و نام خانوادگی وکیل</span>
                                                <strong>{{
                                                        owner.lawyer ?? 'نامشخص'
                                                    }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr>
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>تاریخ خرید/انتقال</span>
                                            <strong>{{ owner.doing_at?.jalali ?? '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>معرف</span>
                                            <strong>نامشخص</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>حق مالکانه</span>
                                            <strong>{{
                                                    owner.ownership ? numberFormat(owner.ownership) + ' ریال' : '-'
                                                }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>اجاره سرقفلی واحد</span>
                                            <strong>{{
                                                    owner.goodwill_rental ? numberFormat(owner.goodwill_rental) + ' ریال' : '-'
                                                }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <!-- اطلاعات مستاجر -->
            <div v-if="occupants">
                <div class="rb-title-section">
                    <div class="rb-icon">
                    <span>
                        <q-icon color="teal-10" name="account_box"/>
                    </span>
                    </div>
                    <div class="rb-title">
                        <h1>اطلاعات مستاجر واحد تجاری</h1>
                        <p>
                            لیست کاربران با کلیک بر روی "افزودن مستاجر" در دسترس است.
                        </p>
                    </div>
                </div>
                <q-card class="q-mb-lg" v-for="occupant in occupants">
                    <q-card-section>
                        <div class="q-owner-section">
                            <div class="row items-center">
                                <div class="col-lg-12 col-12">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>نام و نام خانوادگی</span>
                                                <strong>{{ occupant.user.name ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>کد ملی</span>
                                                <strong>{{ occupant.user.name ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>شغل</span>
                                                <strong>{{
                                                        occupant.user.job_types ? Object.values(occupant.user.job_types).map(item => item.label).join(', ') : ''
                                                    }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>جنسیت</span>
                                                <strong>{{ occupant.user.gender.label ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>تاریخ تولد</span>
                                                <strong>{{
                                                        occupant.user.birth_date?.jalali ?? 'نامشخص'
                                                    }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>نام پدر</span>
                                                <strong>{{ occupant.user.father_name ?? 'نامشخص' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="q-data">
                                                <span>آدرس</span>
                                                <strong>{{ occupant.user.address ?? 'نامشخص' }} </strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>شماره همراه</span>
                                                <strong>{{ occupant.user.mobile ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>شماره همراه ۲ (اضطراری)</span>
                                                <strong>{{ occupant.user.mobile2 ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>تلفن ثابت</span>
                                                <strong>{{ occupant.user.tel ?? '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="q-data">
                                                <span>نام و نام خانوادگی وکیل</span>
                                                <strong>{{
                                                        occupant.lawyer ? occupant.lawyer.name : 'نامشخص'
                                                    }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr>
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>تاریخ شروع اجاره</span>
                                            <strong>{{ occupant.rental_start_date?.jalali ?? '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>تاریخ پایان اجاره</span>
                                            <strong>{{ occupant.rental_end_date?.jalali ?? '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>تاریخ تنظیم</span>
                                            <strong>{{ occupant.doing_at?.jalali ?? '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>تنظیم کننده</span>
                                            <strong>نامشخص</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>مبلغ رهن (ریال)</span>
                                            <strong>{{
                                                    occupant.mortgage_amount ? numberFormat(occupant.mortgage_amount) + ' ریال' : '-'
                                                }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="q-data">
                                            <span>مبلغ اجاره (ریال)</span>
                                            <strong>{{
                                                    occupant.rental_amount ? numberFormat(occupant.rental_amount) + ' ریال' : '-'
                                                }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <UploadDocuments
                title="فایل های بارگذاری شده واحد تجاری"
                description="مدارک و فایل های مربوط به واحدهای تجاری را می توانید بارگذاری نمایید"
                :model-type="modelType"
                :id="unit.id"
                :redirect-route="route('admin.units.edit',unit.id)"
            />
        </q-page>
    </AdminLayout>
</template>
<script>
import {onMounted, ref} from 'vue';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage, Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {formHandler, goToRoute, numberFormat, removeCommaNumber, showNotify} from "@/utils/helpers.js";
import FieldNumber from "@/Components/FieldNumber.vue";
import UploadDocuments from "@/Components/UploadDocuments.vue";
import FieldInput from "@/Components/FieldInput.vue";

export default {
    methods: {goToRoute, route, numberFormat},
    components: {
        FieldInput,
        FieldNumber,
        Head,
        AdminLayout,
        UploadDocuments,
        Link
    },
    setup() {

        const page = usePage();
        const perMeterValue = ref(0);
        const unit = ref(page.props.unit.data);
        const meterageValue = ref(unit.value.meterage);
        const now_value_per_meter = ref(numberFormat(page.props.value.value_per_meter));
        const owners = page.props.owners.data;
        const occupants = page.props.occupants.data;
        const modelType = ref(page.props.modelType)
        const {form, submitForm, isLoading} = formHandler({
            unit_number: ref(unit.value.number),
            floor: ref(unit.value.floor.id),
            meterage: ref(unit.value.meterage),
            unit_type: ref(unit.value.type.value),
            tel: ref(unit.value.tel),
            postal_code: ref(unit.value.postal_code),
            position: ref(unit.value.position.id),
            meter_serial_number: ref(unit.value.meter_serial_number),
            roof: ref(unit.value.roof.value),
            computer_password: ref(unit.value.computer_password),
            case: ref(unit.value.case),
            status: ref(unit.value.status.value),
            operation: ref(unit.value.operation.value),
            block: ref(unit.value.block.value),
            value_per_meter: ref(unit.value.value_per_meter),
            total_value: ref(unit.value.total_value),
            total_now_value: ref(unit.value.now_total_value),
            maximum_full_mortgage: ref(unit.value.maximum_full_mortgage),
            maximum_monthly_rent: ref(unit.value.maximum_monthly_rent),
            owner_annual_goodwill_rent: ref(unit.value.owner_annual_goodwill_rent),
            sale_price_suggested_owner: ref(unit.value.sale_price_suggested_owner),
            owner_proposed_mortgage: ref(unit.value.owner_proposed_mortgage),
            rent_proposed_owner: ref(unit.value.rent_proposed_owner),
            charge_amount: ref(unit.value.charge_amount),
        }, 'put');


        const calculateNowTotalValueMeterage = (event) => {
            meterageValue.value = event.target.value == '' ? 0 : parseFloat(removeCommaNumber(event.target.value));
            if (meterageValue.value > 0 && form.total_now_value > 0) {
                now_value_per_meter.value = numberFormat(removeCommaNumber(form.total_now_value) / meterageValue.value);
            } else {
                now_value_per_meter.value = 0;
            }
        }

        const calculateNowTotalValue = (event) => {
            perMeterValue.value = event.target.value == '' ? 0 : parseFloat(removeCommaNumber(event.target.value));
            if (perMeterValue.value > 0 && form.total_now_value > 0) {
                now_value_per_meter.value = numberFormat(removeCommaNumber(form.total_now_value) / meterageValue.value);
            } else {
                now_value_per_meter.value = 0;
            }
        }
        const editUnit = async () => {
            try {
                await submitForm('admin.units.update', unit.value.id);
                console.log(window.formData.data.formula_charge_amount)
                unit.value.formula_charge_amount = window.formData.data.formula_charge_amount;
            }
            catch (error){
                console.log('error:'+error);
            }
        };

        onMounted(() => {
            if(form.status === 'NOT_SOLD'){
                form.total_value = null;
            }
        })
        const statusHandle = () => {
            if(form.status === 'NOT_SOLD'){
                form.total_value = null;
            }
        }

        return {
            page,
            unit,
            owners,
            occupants,
            form,
            submitForm,
            editUnit,
            isLoading,
            unit_type_options: page.props.unitTypes,
            position_options: page.props.positions,
            floor_options: page.props.floor,
            roof_options: page.props.roof,
            calculateNowTotalValueMeterage,
            calculateNowTotalValue,
            now_value_per_meter,
            dialogVisible: ref(false),
            selectedOption: null,
            modelType,
            statusHandle
        }
    }
}
</script>
<style scoped>

.q-field :deep(.q-field__label) {
    width: 100%;
    max-width: 100%;
    font-size: .875rem;
    top: 10px;
    left: 0;
    text-align: right;
    transform: none;
}

.q-field :deep(.q-field__native) {
    position: relative;
    bottom: 2.5px;
}

.q-field :deep(.q-field__prepend) {
    padding: 0 0 0 5px;
}

.q-field :deep(.q-field__append) {
    padding: 0;
}

.q-actions-section {
    display: flex;
    gap: 15px;
    margin: 0 0 25px;
}

.q-actions-section .q-btn {
    font-size: .95rem;
    padding: 10px 30px;
}

.q-actions-section .q-btn :deep(.col) {
    gap: 5px;
}

.q-actions-section .q-btn :deep(.block) {
    position: relative;
    top: .90px;
    line-height: 1;
}

.q-actions-section .q-btn :deep(.q-icon) {
    margin: 0;
}

.q-owner-section .q-data {
    margin: 0 0 15px;
}

.q-owner-section .q-data span {
    display: block;
    color: rgb(120, 125, 135);
}

.q-owner-section .q-data strong {
    color: rgba(15, 20, 30)
}

.q-owner-section hr {
    margin: 15px 0;
    border-color: rgba(215, 215, 225, .75);
    border-style: dashed;
}
</style>
