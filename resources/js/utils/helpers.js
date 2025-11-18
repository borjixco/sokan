import { Notify } from 'quasar';
import {route} from "ziggy-js";
import {router, useForm, usePage} from '@inertiajs/vue3';
import {ref, onMounted, onBeforeUnmount, computed, watch} from 'vue';
import {useIdle, useTimestamp} from "@vueuse/core";
export function showNotify(message, type = 'positive', handlerFunc) {
    Notify.create({
        position: 'bottom-left',
        message: message,
        color: type,
        icon: type === 'positive' ? 'check_circle' : 'error',
        actions: [
            { icon: 'close', color: 'white', round: true, handler: handlerFunc }
        ]
    });
}

export function formHandler(initialValues = null, method = 'post') {
    const isLoading = ref(false);
    const page = usePage();
    const form = useForm(initialValues);
    const submitForm = (routeName, params = null, goToUrl = '') => {
        return new Promise((resolve, reject) => {
            isLoading.value = true;
            const requestMethod = method === 'post' ? form.post : form.put;

            requestMethod.call(form, route(routeName, params), {
                preserveScroll: true,
                preserveState: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                onSuccess: (page) => {
                    isLoading.value = false;
                    if (page.props.flash?.success && page.props.flash?.success?.message?.length > 0) {
                        showNotify(page.props.flash.success.message);
                    }
                    window.formData = page.props.flash.success.data;
                    if (goToUrl.length > 0) {
                        goToRoute(goToUrl);
                    }
                    resolve(); // عملیات موفقیت‌آمیز بود
                },
                onError: (errors) => {
                    isLoading.value = false;
                    showNotify(errors.message || 'خطا رخ داد', 'negative');
                    reject(errors); // عملیات با خطا مواجه شد
                },
            });
        });
    };

    return { form, submitForm, isLoading };
}

export const goToRoute = (url) => {
    router.visit(url, {
        preserveScroll: true
    });
};

export const goToUrl = (url,target = '_self') =>{
    window.open(url, target);
}

export const numberFormat = (number = null, decimals = 0, decPoint = '.', thousandsSep = ',') =>{
    if(!number && number !== 0){
        return;
    }
    number = parseFloat(number).toFixed(decimals);

    let parts = number.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

    return parts.join(decPoint);
}

export const tableColumns = (columns) => {
    return columns.map((column) => {
        if(typeof column === 'object'){
            return column;
        }
        else {
            return {
                name: column,
                required: true,
                label: column,
                align: 'center',
                field: column,
                sortable: false,
            };
        }
    })
}

export const getCsrfToken = () =>{
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
}

export const removeCommaNumber = (value) =>{
    let strValue = String(value);

    // تبدیل اعداد فارسی به انگلیسی
    const persianToEnglish = {
        '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
        '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
    };

    strValue = strValue.replace(/[۰-۹]/g, (match) => persianToEnglish[match]);

    // حذف تمامی کاماها
    return strValue.replace(/,/g, '');
}

export default function useInactivityLogout(logoutUrl, timeoutMinutes = 5) { // ۵ دقیقه (300 ثانیه)
    const { idle, lastActive } = useIdle(timeoutMinutes*60*1000)
    const now = useTimestamp({ interval: 1000 })
    const idledFor = computed(() =>
        Math.floor((now.value - lastActive.value) / 1000))
    // نظارت بر تغییرات وضعیت idle
    watch(idle, (newIdleValue) => {
        if (newIdleValue === true) {
           router.post(logoutUrl);
        }
    });
}
