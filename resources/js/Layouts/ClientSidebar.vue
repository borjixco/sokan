<template>
    <div class="rb-sidebar-section">
        <q-drawer show-if-above elevated side="right" :width="250" :breakpoint="1200" v-model="rightDrawerOpen">
            <div class="rb-logo">
                <img :src="logo" alt=""/>
            </div>
            <hr>
            <div class="q-menu-section">
                <q-list>
                    <template v-for="(menu, index) in menuItems" :key="index">
                        <q-item clickable v-ripple v-if="menu.show" >
                            <Link :href="menu.url">
                                <q-item-section avatar>
                                    <q-icon color="teal-10" :name="menu.icon"/>
                                </q-item-section>
                                <q-item-section>{{ menu.title }}</q-item-section>
                                <q-item-section side>
                                    <q-avatar v-if="menu.count > 0" size="sm" color="teal-10" text-color="white">
                                        {{ menu.count }}
                                    </q-avatar>
                                </q-item-section>
                            </Link>
                        </q-item>
                    </template>
                </q-list>
            </div>
        </q-drawer>
    </div>
</template>

<script>
import logoImage from '/resources/img/logo.png';
import {usePage, router, Link} from '@inertiajs/vue3';
import {route} from "ziggy-js";
import {ref, watch} from "vue"; // No need for 'watch' here, just the 'ref'

export default {
    components:{
        Link
    },
    props: {
        rightDrawerOpen: Boolean, // Accept the drawer state as a prop
    },
    emits: ['update:drawer'], // Emit event to update drawer state
    methods: {route},
    setup(props, {emit}) {
        const page = usePage();
        const logo = logoImage;
        const menuItems = page.props.menuItems;

        const goToRoute = (url) => {
            router.visit(url, {
                method: 'get',
                preserveState: true,
                preserveScroll: true,
            });
        };

        // We don't need 'watch' here if you directly bind 'v-model' to the drawer state in the parent
        const rightDrawerOpen = ref(props.rightDrawerOpen);

        // Watch prop changes to emit updates for the drawer (just in case)
        watch(() => props.rightDrawerOpen, (newVal) => {
            rightDrawerOpen.value = newVal;
        });

        // Emit updated drawer state back to parent when it's changed
        watch(rightDrawerOpen, (newValue) => {
            emit('update:drawer', newValue);
        });

        return {
            logo,
            menuItems,
            goToRoute,
            rightDrawerOpen,
        };
    }
};
</script>
