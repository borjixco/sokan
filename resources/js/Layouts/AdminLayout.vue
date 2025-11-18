<template>
    <q-layout view="hHr lpR fFr">
        <q-header elevated class="rb-header-section bg-white text-black">
            <q-toolbar>
                <q-btn flat round dense color="dark" @click="drawer = !drawer" icon="menu"/>
                <q-toolbar-title></q-toolbar-title>
                <q-btn flat round dense color="dark" size=".90rem" icon="person_outline">
                    <q-tooltip>پروفایل کاربری</q-tooltip>
                </q-btn>
                <Link :href="route('admin.tickets')">
                    <q-btn flat round dense color="dark" size=".90rem" icon="mail_outline">
                        <q-badge rounded floating color="teal-10" text-color="white" :label="ticket.count"
                                 v-if="ticket.count > 0"/>
                        <q-tooltip>مرکز پیام</q-tooltip>
                    </q-btn>
                </Link>
                <q-btn flat round dense color="dark" size=".90rem" icon="logout" @click="logout" :loading="isLoading">
                    <q-tooltip>خروج</q-tooltip>
                </q-btn>
            </q-toolbar>
        </q-header>
        <AdminSidebar :rightDrawerOpen="drawer" @update:drawer="drawer = $event"/>
        <q-page-container class="rb-container-section">
            <slot/>
        </q-page-container>
    </q-layout>
</template>

<script>
import {ref} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import AdminSidebar from "@/Layouts/AdminSidebar.vue";
import useInactivityLogout, {formHandler} from "@/utils/helpers.js";
import {route} from "ziggy-js";

export default {
    methods: {route},
    components: {AdminSidebar, Link},

    setup() {
        const page = usePage();
        const ticket = page.props.menuItems.find(item => item.name === "tickets");
        const drawer = ref(false);
        const {form, submitForm, isLoading} = formHandler([]);
        const logout = () => {
            submitForm('admin.logout');
        };

        useInactivityLogout(route('admin.logout'),10); // آدرس کامل روت لاگ‌اوت

        return {
            drawer,
            logout,
            isLoading,
            ticket
        };
    },
};
</script>

<style>
@import '/resources/css/app.css';

.rb-sidebar-section .q-drawer {
    border-left: 1px solid rgba(245, 245, 255, 1);
    box-shadow: -5px 0 10px 0 rgba(0, 0, 0, 0.025);
}

.rb-sidebar-section .q-drawer .q-layout__shadow {
    display: none;
}

.rb-sidebar-section .q-drawer .rb-logo {
    margin: 15px 0 0;
    text-align: center;
}

.rb-sidebar-section .q-drawer .rb-logo img {
    width: 175px;
}

.rb-sidebar-section .q-drawer hr {
    max-width: 90%;
    margin: 0 auto;
    border-color: rgba(245, 245, 255, .15);
}

.rb-sidebar-section .q-drawer .q-menu-section {
    margin: 10px 0;
    padding: 0 5px;
}

.rb-sidebar-section .q-drawer .q-menu-section .q-item {
    min-height: 45px;
    padding: 0 5px;
    border-radius: .25rem
}

.rb-sidebar-section .q-drawer .q-menu-section .q-item a {
    width: 100%;
    height: 45px;
    display: flex;
    padding: 0 10px;
    color: rgb(30, 30, 30);
}

.rb-sidebar-section .q-drawer .q-menu-section .q-item a i {
    color: rgb(0, 75, 65);
}

.rb-sidebar-section .q-drawer .q-menu-section .q-item.q-active {
    background: rgb(0, 75, 65);
    color: rgb(255, 255, 255);
    box-shadow: 0 1.5px 5px rgba(0, 0, 0, 0.25), 0 2.5px 2.5px rgba(0, 0, 0, 0.15), 0 5px 0 -2.5px rgba(0, 0, 0, 0.10);
}

.rb-sidebar-section .q-drawer .q-menu-section .q-item.q-active a {
    color: rgb(255, 255, 255);
}

.rb-sidebar-section .q-drawer .q-menu-section .q-item.q-active i {
    color: rgb(255, 255, 255);;
}

.rb-sidebar-section .q-drawer .q-menu-section .q-item .q-item__section {
    padding: 0;
    min-width: 35px;
}

.rb-add-section .q-btn .q-icon {
    margin: 0 0 0 5px;
    font-size: 1.25rem;
}

.q-field .q-field__prepend {
    padding: 0 0 0 5px;
}

.q-field .q-field__append {
    padding: 0;
}

.q-field .q-field__native {
    padding: 0;
    font-size: .95rem;
    line-height: 1;
}

.q-field .q-field__bottom {
    padding: 5px;
}

.q-table__container .q-table__bottom--nodata {
    justify-content: center;
    align-items: center;
    margin: 15px 0;
    font-size: .95rem;
    border: 0;
}

.q-table__container .q-table__bottom--nodata .q-table__bottom-nodata-icon {
    margin: 0;
    font-size: 1.5rem;
    color: rgb(0, 75, 65);
}
</style>
