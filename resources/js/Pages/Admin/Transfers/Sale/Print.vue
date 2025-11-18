<template>
    <Head title="فرم نقل و انتقال سرقفلی واحد تجاری"/>
    <div class="rb-print-section">
        <div class="text-center">
            <strong>بسمه تعالی</strong>
        </div>
        <div class="rb-info">
            <img :src="logo" alt="">
        </div>
        <div class="text-center">
            <h1>فرم نقل و انتقال سرقفلی واحد تجاری</h1>
        </div>
        <div v-if="transfer.old_owners.length > 0" style="display: inline">
            در تاریخ <strong>{{ transfer.doing_at.jalali }}</strong>
            <template v-for="(old_owner, index) in transfer.old_owners" :key="index">
                <span v-if="index > 0"> و </span>
                آقا / خانم <strong>{{ old_owner.user.name ?? dot.slice(0, -2) }}</strong> فرزند
                <strong>{{ old_owner.user.father_name ?? dot }}</strong> به ش ملی
                <strong>{{ old_owner.user.national_code ?? dot }}</strong> تلفن <strong>{{
                    old_owner.user.mobile ?? (old_owner.user.mobile ? dot.slice(0, -2) + old_owner.user.mobile : dot)
                }}</strong> به آدرس
                <strong>{{ old_owner.user.address ?? dot.repeat(3) }}</strong>
            </template>
            در دفتر مجتمع حضور یافته با ارائه اصل قرارداد صلح و سرقفلی شماره
            <strong>{{ transfer.contract_number }}</strong> اعلام می نماید کلیه حق و
            حقوقات خود از واحد <strong>{{ transfer.unit.unit_number }}</strong> مرکز خرید 17 شهریور( شرکت نگین شاهنامه )
            واقع درطبقه <strong>{{transfer.unit.floor.label}}</strong> را با
            موافقت شرکت به
        </div>
        <div v-if="transfer.current_owners.length > 0" style="display: inline">
            <template v-for="(current_owner, index) in transfer.current_owners" :key="index">
                <span v-if="index > 0"> و </span>
                آقا / خانم <strong>{{ current_owner.user.name }}</strong> فرزند
                <strong>{{ current_owner.user.father_name ?? dot }}</strong> ش ملی
                <strong>{{ current_owner.user.national_code ?? dot }}</strong> تلفن
                <strong>{{
                        current_owner.user.mobile ?? (current_owner.user.mobile ? dot.slice(0, -2) + current_owner.user.mobile : dot)
                    }}</strong>
                به آدرس <strong>{{ current_owner.user.address ?? dot.repeat(3) }}</strong> با
                <strong>{{ current_owner.quota }}</strong> سهم
            </template>
            واگذار
            <div v-if="transfer.old_owners.length > 1" style="display: inline">
                نموده ایم
            </div>
            <div v-else style="display: inline">
                نمودم
            </div>
            و از این تاریخ به بعد هیچگونه ادعایی نسبت به کل سرقفلی این واحد تجاری و سایر حقوق آن
            <div v-if="transfer.old_owners.length > 1" style="display: inline">
                نداشته و
                نداریم
            </div>
            <div v-else style="display: inline">
                نداشته و
                ندارم
            </div>
            و در آینده
            <template v-for="(current_owner, index) in transfer.current_owners" :key="index">
                <span v-if="index > 0"> و </span>
                آقا / خانم <strong>{{ current_owner.user.name }}</strong> ( انتقال گیرنده )
            </template>
            با دریافت اصل قرارداد واگذاری ، اعلام می نماید کلیه شرایط مندرج در قرارداد صلح حقوق شرکت را مورد موافقت قرار
            داده و متعهد و ملزم به رعایت کلیه مفاد آن
            <div v-if="transfer.current_owners.length > 1" style="display: inline">
                خواهیم بود
            </div>
            <div v-else style="display: inline">
                خواهم بود
            </div>
            .
            <div>
                این قرارداد پیوست قرارداد اصلی بوده و هر دو حکم واحد را داشته و ارائه هر کدام بدون دیگری
                فاقد ارزش و اعتبار می باشد .
            </div>
        </div>
        <strong>شرایط مورد موافقت و تأیید طرفین علاوه بر مفاد مندرج در اصل قرارداد به شرح زیر است : </strong>
        <div v-html="transfer.terms"></div>
        <div class="rb-terms">
            <ul>
                <li>
                    1- پرداخت کلیه هزینه‌ها از قبیل هزینه نقل و انتقال دارایی واحد فوق و غیره با توافق و تأیید طرفین به
                    عهده خریدار می‌باشد.
                </li>
                <li>
                    2-کلیه هزینه‌های داخلی واحد مربوطه از جمله: شارژ، اجاره سرقفلی و غیره تا زمان معامله توسط فروشنده
                    تسویه و پس از آن به عهده خریدار می‌باشد.
                </li>
                <li>
                    3- مبلغ مورد معامله به اظهار طرفین {{ numberFormat(transfer.cost) }} ریال می‌باشد‌.
                </li>
            </ul>
        </div>
        <div class="rb-signature">
            <span>انتقال دهنده</span>
            <span>انتقال گیرنده</span>
        </div>
        <div class="rb-signature">
            <span>شاهد اول <strong>{{ transfer.first_witness }}</strong></span>
            <span>شاهد دوم <strong>{{ transfer.second_witness }}</strong></span>
        </div>
        <p>* با انتقال سرقفلی واحد فوق بنام
            <template v-for="(current_owner, index) in transfer.current_owners" :key="index">
                <span v-if="index > 0"> و </span>
                آقا / خانم <strong>{{ current_owner.user.name }}</strong>
            </template>
            موافقت می گردد .
        </p>
        <div class="rb-company">
            <span>شرکت نگین شاهنامه</span>
        </div>
    </div>
</template>
<script>
import logoImage from '/resources/img/logo-sale.png';
import {onMounted, nextTick, ref} from "vue";
import {Head, usePage} from "@inertiajs/vue3";
import {numberFormat} from "../../../../utils/helpers.js";

export default {
    methods: {numberFormat},
    components: {
        Head
    },
    setup() {
        const logo = logoImage;
        const page = usePage();
        const transfer = page.props.transfer.data;
        const dot = ref('..........');

        onMounted(() => {
            nextTick(() => {
                setTimeout(() => {
                    window.print();
                }, 2000);
            });
        });

        return {
            logo,
            transfer,
            dot
        };
    }
};
</script>
<style>
@import '/resources/css/app.css';

.rb-print-section {
    width: 100%;
    max-width: 1190px;
    margin: 0 auto;
    text-align: justify;
}

.rb-print-section .rb-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    font-size: .90rem;
}

.rb-print-section .rb-info img {
    max-width: 100px;
}

.rb-print-section .rb-info ul li {
    width: 100%;
    display: inline-block;
    text-align: left;
}

.rb-print-section p {
    margin: 0;
    line-height: 2.25;
}

.rb-print-section h1 {
    margin: 0 0 15px;
    font-family: 'Estedad-Black';
    font-size: 1rem;
}

.rb-print-section ul {
    margin: 0 0 15px;
}

.rb-print-section ul li {
    width: 100%;
    padding: 1.5px 0;
    display: inline-block;
}

.rb-print-section .rb-signature {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin: 80px 0;
    padding: 0 90px;
}

</style>
