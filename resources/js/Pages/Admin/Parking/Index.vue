<template>
    <Head title="پارکینگ"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row justify-between items-center">
                <div class="col-lg-8">
                    <div class="rb-title-section">
                        <div class="rb-icon">
                            <div>
                                <q-icon :name="currentSection.icon"/>
                            </div>
                        </div>
                        <div class="rb-title">
                            <h1>{{ currentSection.pageTitle }}</h1>
                            <p>{{ currentSection.pageDescription }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="rb-add-section">
                        <q-btn class="rb-action" label="پرینت" @click="printHandle" icon="print"/>
                        <q-btn type="submit" class="rb-action" label="افزودن"/>
                    </div>
                </div>
            </div>
        </q-page>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage} from "@inertiajs/vue3";

export default {
    components: {
        Head,
        AdminLayout,
    },
    setup() {
        const page = usePage();
        const currentSection = page.props.currentSection;

        const printHandle = () => {
            window.print()
        }

        return {
            currentSection,
            printHandle
        }
    }
};
</script>
