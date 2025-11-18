<template>
    <Head title="تنظیمات فنی"/>
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
            </div>

            <q-card flat bordered>

                <!-- تب‌های لینک‌دار بالا -->
                <q-tabs
                    class="text-primary"
                    align="left"
                    indicator-color="primary"
                    active-color="primary"
                >
                    <Link :href="route('admin.settings.dev')" class="q-active"><q-route-tab label="عمومی" /></Link>
                    <Link :href="route('admin.settings.dev.sms')"><q-route-tab  label="سرویس پیامک" /></Link>
                </q-tabs>

            </q-card>

            <!-- محتوای زیر تب‌ها -->
            <q-card class="q-mt-md q-pa-md">
                <slot/>
            </q-card>
        </q-page>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage, Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";

export default {
    methods: {route},
    components: {
        Head,
        Link,
        AdminLayout,
    },
    setup() {
        const page = usePage();
        const currentSection = page.props.currentSection;

        return {currentSection}
    }
};
</script>
