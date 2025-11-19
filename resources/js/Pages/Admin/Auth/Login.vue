<template>
    <Head title="ورود به پنل مدیریت" />
    <div class="rb-login-section">
        <div class="container">
            <div class="row justify-center">
                <div class="col-lg-6 col-12">
                    <div class="rb-login">
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
                                    autofocus
                                >
                                    <template v-slot:append>
                                        <q-icon name="smartphone"/>
                                    </template>
                                </q-input>
                            </div>
                            <div class="rb-input">
                                <q-input
                                    standout="bg-teal-10"
                                    v-model="form.password"
                                    :type="isPwd ? 'password' : 'text'"
                                    placeholder="کلمه عبور"
                                >
                                    <template v-slot:append>
                                        <q-icon
                                            :name="isPwd ? 'visibility_off' : 'visibility'"
                                            class="cursor-pointer"
                                            @click="isPwd = !isPwd"
                                        />
                                    </template>
                                </q-input>
                            </div>
                            <q-btn
                                type="submit"
                                class="q-action"
                                label="ورود به حساب کاربری"
                                :loading="isLoading"
                            />
                        </q-form>
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
import {ref} from 'vue'
import {formHandler} from '@/utils/helpers';
import {Head, usePage} from "@inertiajs/vue3";

export default {
    components: {
      Head
    },
    setup() {
        const isPwd = ref(true);
        const logo = logoImage;
        const page = usePage()
        const {form, submitForm, isLoading} = formHandler({
            _token: page.props.csrf_token,
            mobile: '',
            password: '',
        });

        const submitLogin = () => {
            submitForm('admin.login.process');
        };

        return {
            isPwd, logo, isLoading, form, submitLogin
        };
    }
};
</script>
<style>
@import '/resources/css/app.css';
</style>
