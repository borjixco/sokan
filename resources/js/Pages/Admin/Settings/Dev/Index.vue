<template>
    <SettingDevLayout>
        عمومی
    </SettingDevLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage, Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import SettingDevLayout from "@/Layouts/SettingDevLayout.vue";

export default {
    methods: {route},
    components: {
        SettingDevLayout,
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
