<template>
    <div :class="'q-payment-section q-status q-' + status">
        <q-card>
            <q-card-section>
                <ul>
                    <li>
                        <div>
                            <q-icon name="chat"/>
                            <span>پیام</span>
                        </div>
                        <strong>{{message}}</strong>
                    </li>
                </ul>
            </q-card-section>
        </q-card>
    </div>
</template>
<script>
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";

export default {
    setup() {
        const page = usePage();
        return {
            status: ref(page.props.status),
            message: ref(page.props.message),
        }
    }
}
</script>
<style>
@import '/resources/css/app.css';

.q-payment-section {
    max-width: 600px;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.q-payment-section .q-card {
    width: 100%;
    padding: 15px;
}

.q-payment-section .q-card ul li {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
    margin: 5px 0;
}

.q-payment-section .q-card ul li div {
    display: flex;
    align-items: center;
    gap: 5px;
}
</style>
