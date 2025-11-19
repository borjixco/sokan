<template>
    <Head title="ورود به ناحیه کاربری مالکین، مستاجرین و پرسنل"/>
    <div class="rb-login-section">
        <div class="container">
            <div class="row justify-center">
                <div class="col-lg-6 col-12">
                    <div class="rb-login" v-if="step === 1">
                        <q-form @submit.prevent="submitLogin">
                            <div class="rb-img">
                                <img :src="logo" alt="">
                            </div>
                            <h1>ناحیه کاربری</h1>
                            <p>
                                با ورود و یا ثبت نام در سامانه مجتمع شما شرایط و قوانین و حریم خصوصی استفاده از سرویس
                                های سامانه را می‌پذیرید.
                            </p>
                            <div class="rb-input">
                                <q-input
                                    standout="bg-teal-10"
                                    v-model="form.mobile"
                                    type="text"
                                    placeholder="شماره موبایل"
                                    @keyup="handleMobile"
                                    input-class="mobile-input"
                                    autofocus
                                >
                                    <template v-slot:append>
                                        <q-icon name="smartphone"/>
                                    </template>
                                </q-input>
                            </div>
                            <q-btn
                                type="submit"
                                class="q-action"
                                label="ارسال کد تایید"
                                :loading="isLoading"
                            />
                        </q-form>
                    </div>
                    <div class="rb-verify" v-else-if="step === 2">
                        <div class="rb-phone">
                            <p>کد تایید ارسال شده به شماره تلفن <strong>09303736415</strong> را وارد نمایید.</p>
                        </div>
                        <div class="rb-inputs">
                            <OtpInput
                                v-model="form.code"
                                input-classes="otp-input"
                                separator=""
                                :num-inputs="5"
                                @onComplete="handleComplete"
                                :key="otpKey"
                                autofocus
                            />
                        </div>
                        <strong class="rb-timer" v-if="timeLeft > 0">{{ formattedTime }}</strong>
                        <br v-if="timeLeft === 0">
                        <div class="text-center">
                            <div class="rb-button-section" v-if="timeLeft === 0">
                                <q-btn
                                    class="rb-action rb-outline"
                                    label="ارسال مجدد کد تایید"
                                    @click="restartTimer"
                                />
                            </div>
                            <br v-if="timeLeft === 0">
                            <div class="rb-button-section">
                                <q-btn class="rb-action" label="ویرایش شماره موبایل" @click="step = 1; isLoading = false"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="rb-cover"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import logoImage from '/resources/img/logo.png';
import {ref, computed, onMounted, watch, reactive, nextTick} from 'vue';
import { formHandler } from '@/utils/helpers';
import { Head } from "@inertiajs/vue3";
import OtpInput from "vue3-otp-input";
import {route} from "ziggy-js";
import FieldInput from "@/Components/FieldInput.vue";

export default {
    components: {FieldInput, Head, OtpInput },
    setup() {
        const logo = logoImage;
        const step = ref(1);
        const fields = reactive({
            mobile: '',
            code: ''
        })
        const { form, submitForm, isLoading } = formHandler(fields,'post');

        const counter = ref(60);
        const timeLeft = ref(counter.value);
        let timer = null;
        const otpKey = ref(0);

        const formattedTime = computed(() => {
            const minutes = Math.floor(timeLeft.value / 60);
            const seconds = timeLeft.value % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        });

        const startTimer = () => {
            if (timer) clearInterval(timer);
            timer = setInterval(() => {
                if (timeLeft.value > 0) {
                    timeLeft.value--;
                } else {
                    clearInterval(timer);
                }
            }, 1000);
        };

        const handleSubmit = async (route) => {
            try {
                await submitForm(route);
                if (window.formData?.step === 2) {
                    step.value = 2;
                    timeLeft.value = counter.value;
                    startTimer();
                }
            } catch (error) {
                console.error('خطا در ارسال فرم:', error);
            }
        };

        const restartTimer = async () => {
            await handleSubmit('client.auth.resend');
        };

        const handleMobile = async () => {
            if (form.mobile.length === 11) {
                await handleSubmit('client.auth.login');
            }
        };

        const submitLogin = async () => {
            await handleSubmit('client.auth.login');
        };


        onMounted(() => {
            if (step.value === 2) startTimer();
        });

        watch(step, (newStep) => {
            if (newStep === 2) {
                // صبر کن تا DOM آماده بشه
                nextTick(() => {
                    const firstOtpInput = document.querySelector('.otp-input');
                    if (firstOtpInput) {
                        firstOtpInput.focus();
                    }
                });
            }
        });

        const handleComplete = async (value) => {
            try {
                form.code = value;
                await submitForm('client.auth.verify');
            } catch (error) {
                form.code = null;
                otpKey.value++;
                console.log(form)
            }

        };

        return {
            logo, isLoading, form, submitLogin, step, formattedTime, restartTimer, timeLeft, handleComplete,handleMobile, otpKey
        };
    }
};
</script>

<style>
@import '/resources/css/app.css';
.otp-input-container{
    direction: ltr;
}
.otp-input {
    direction: ltr;
    width: 40px;
    height: 40px;
    padding: 5px;
    margin: 0 10px;
    font-size: 20px;
    border-radius: 4px;
    border: 1px solid rgba(0, 0, 0, 0.3);
    text-align: center;
}
/* Background colour of an input field with value */
.otp-input.is-complete {
    background-color: #e4e4e4;
}
.otp-input::-webkit-inner-spin-button,
.otp-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input::placeholder {
    font-size: 15px;
    text-align: center;
    font-weight: 600;
}
</style>
