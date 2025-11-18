<template>
    <div :class="'q-callback-section q-status q-' + status">
        <q-card>
            <q-card-section>
                <ul>
                    <li v-if="message">
                        <div>
                            <q-icon color="teal-10" size="1.25rem" name="chat"/>
                            <span>پیام</span>
                        </div>
                        <strong>{{ message }}</strong>
                    </li>
                    <li v-if="transactionId">
                        <div>
                            <q-icon color="teal-10" size="1.25rem" name="receipt_long"/>
                            <span>شناسه تراکنش</span>
                        </div>
                        <strong>{{ transactionId }}</strong>
                    </li>
                    <li v-if="refId">
                        <div>
                            <q-icon color="teal-10" size="1.25rem" name="post_add"/>
                            <span>شناسه مرجع بانک</span>
                        </div>
                        <strong>{{ refId }}</strong>
                    </li>
                    <li v-if="amount">
                        <div>
                            <q-icon color="teal-10" size="1.25rem" name="credit_card"/>
                            <span>مبلغ</span>
                        </div>
                        <strong class="q-price">{{ numberFormat(amount) }} ریال</strong>
                    </li>
                    <li v-if="date">
                        <div>
                            <q-icon color="teal-10" size="1.25rem" name="event"/>
                            <span>تاریخ</span>
                        </div>
                        <strong>{{ date }}</strong>
                    </li>
                </ul>
            </q-card-section>
        </q-card>
    </div>
</template>
<script>
import {usePage} from "@inertiajs/vue3";
import {numberFormat} from "../../../utils/helpers.js";

export default {
    methods: {numberFormat},
    setup() {
        const page = usePage();
        return {
            message: page.props.message,
            transactionId: page.props.transactionId,
            refId: page.props.refId,
            status: page.props.status,
            amount: page.props.amount,
            date: page.props.date,
        };
    }
}
</script>
<style>
@import '/resources/css/app.css';

.q-callback-section {
    max-width: 600px;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.q-callback-section .q-card {
    width: 100%;
    padding: 15px;
}

.q-callback-section .q-card ul li {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
    margin: 5px 0;
}

.q-callback-section .q-card ul li div {
    display: flex;
    align-items: center;
    gap: 5px;
}

.q-callback-section .q-card ul li .q-status {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5px 15px;
    border-radius: 300px
}

.q-callback-section .q-card ul li .q-status.q-success {
    background: rgb(0, 75, 65);
    color: rgb(255, 255, 255)
}

.q-callback-section .q-card ul li strong.q-price span {
    font-family: 'Estedad-Medium';
    font-size: .90rem;
}
</style>
