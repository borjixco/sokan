<template>
    <Head title="داشبورد"/>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="zo-dashboard-section">
                <div class="zo-stats-section">
                    <div class="row items-center q-col-gutter-md">
                        <div class="col-lg-3 col-md-6 col-12">
                            <UnitCardDashboard/>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <OwnerCardDashboard/>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <OccupantCardDashboard/>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <TransferCardDashboard/>
                        </div>
                    </div>
                    <div class="row q-col-gutter-md">
                        <div class="col-lg-8 col-12">
                            <TransferChartDashboard/>
                        </div>
                        <div class="col-lg-4 col-12">
                            <ChargeStatDashboard/>
                            <BillStatDashboard/>
                        </div>
                        <div class="col-12">
                            <ChargeReportDashboard/>
                        </div>
                    </div>
                </div>
            </div>
        </q-page>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Head, usePage} from "@inertiajs/vue3";
import {setCssVar} from 'quasar'
import UnitCardDashboard from "@/Pages/Admin/Dashboard/UnitCardDashboard.vue";
import OwnerCardDashboard from "@/Pages/Admin/Dashboard/OwnerCardDashboard.vue";
import OccupantCardDashboard from "@/Pages/Admin/Dashboard/OccupantCardDashboard.vue";
import TransferCardDashboard from "@/Pages/Admin/Dashboard/ContractCardDashboard.vue";
import TransferChartDashboard from "@/Pages/Admin/Dashboard/TransferChartDashboard.vue";

setCssVar('primary', 'rgb(0, 75, 65)')

import ChargeStatDashboard from "@/Pages/Admin/Dashboard/ChargeStatDashboard.vue";
import BillStatDashboard from "@/Pages/Admin/Dashboard/BillStatDashboard.vue";
import ChargeReportDashboard from "@/Pages/Admin/Dashboard/ChargeReportDashboard.vue";

export default {
    components: {
        ChargeReportDashboard,
        BillStatDashboard,
        ChargeStatDashboard,
        TransferChartDashboard,
        TransferCardDashboard,
        OccupantCardDashboard,
        OwnerCardDashboard,
        UnitCardDashboard,
        Head,
        AdminLayout,
    },
    setup() {
        const page = usePage();
        const currentSection = page.props.currentSection;

        return {
            currentSection,
        };
    },
};
</script>
<style>

.zo-stats-section .zo-stat .zo-overlay {
    position: absolute;
    top: 15px;
    left: -15px;
    font-size: 6rem;
    color: rgb(0, 75, 65);;
    transform: rotate(35deg);
    opacity: .5
}

.zo-stats-section .zo-stat {
    width: 100%;
    min-height: 100px;
    display: inline-block;
    margin: 0 0 25px;
    padding: 30px 15px;
    position: relative;
    text-align: center;
    box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.05)
}

.zo-stats-section .zo-stat .zo-content {
    display: flex;
    align-items: center;
    gap: 10px
}

.zo-stats-section .zo-stat .zo-content .zo-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: rgb(0, 75, 65);
    color: rgb(255, 255, 255);
    border-radius: 300px;
    box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.05)
}

.zo-stats-section .zo-stat .zo-content .zo-info {
    width: 100%;
    display: inline-block;
    position: relative;
    text-align: right;
}

.zo-stats-section .zo-stat .zo-content .zo-info .zo-label {
    display: block;
    margin: 0 0 15px;
    font-size: 1.15rem;
}

.zo-stats-section .zo-stat .zo-content .zo-info .zo-counter {
    display: flex;
    gap: 15px
}

.zo-stats-section .zo-stat .zo-content .zo-info .zo-counter div {
    display: flex;
    align-items: center;
    gap: 5px
}

.zo-stats-section .zo-stat .zo-content .zo-info .zo-counter div span {
    opacity: .90;
}

.zo-stats-section .zo-stat .zo-content .zo-info .zo-counter div span,
.zo-stats-section .zo-stat .zo-content .zo-info .zo-counter div strong {
    display: block;
    line-height: 1;
}

.zo-stats-section .zo-stat .zo-content .zo-info .zo-total {
    display: block;
    font-size: 1.25rem;
    line-height: 1;
}

.zo-stats-section .zo-stat .zo-add {
    position: absolute;
    top: 30px;
    left: 15px;
}

.zo-stats-section .zo-stat .zo-add button {
    font-size: .70rem;
    background: rgba(0, 75, 65, .05);
    color: rgb(0, 75, 65);
}

.zo-stat-section {
    margin: 0 0 15px;
}

.zo-title-section {
    display: flex;
    align-items: center;
    gap: 15px;
    margin: 0 0 15px;
}

.zo-title-section .zo-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: rgb(255, 255, 255);
    color: rgb(0, 75, 65);
    border-radius: 15px;
    box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.025);
}

.zo-title-section .zo-title h1 {
    margin: 0;
    font-size: 1rem;
}

.zo-title-section .zo-title p {
    margin: 0;
    font-size: .90rem;
    color: rgb(120, 125, 135);
}

.zo-doughnut-section canvas {
    margin: 0 auto
}

.zo-progres-section .zo-title {
    display: flex;
    justify-content: space-between;
    margin: 0 0 10px;
}

.zo-progres-section .zo-title .zo-price {
    display: inline-flex;
    gap: 1.5px;
}

.zo-progres-section .zo-title .zo-price small {
    font-size: .875rem;
}

.zo-progres-section .zo-progres {
    overflow: hidden;
    border-radius: 300px;
}

.zo-progres-section .zo-badge {
    max-width: 100px;
    display: flex;
    gap: 1.5px;
    margin: 0 auto;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    font-size: .90rem;
    background-color: transparent;
}

.zo-table {
    width: 100%;
    text-align: center;
}

.zo-table th {
    font-weight: normal;
    opacity: .90;
}

.zo-table tr td {
    padding: 10px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.zo-table tr:last-child td {
    border-bottom: 0;
}

.zo-table .zo-price {
    display: flex;
    justify-content: center;
    gap: 2.5px
}

.zo-table .zo-PAID {
    width: 100px;
    display: inline-flex;
    justify-content: center;
    padding: 10px 15px;
    background: rgba(0, 75, 65, .05);
    color: rgb(0, 75, 65);
}

.zo-table .zo-OVERDUE, .zo-table .zo-NOT_SENT, .zo-table .zo-CANCELED {
    width: 100px;
    display: inline-flex;
    justify-content: center;
    padding: 10px 15px;
    background: rgba(245, 65, 55, .05);
    color: rgb(245, 65, 55);
}

.zo-table .zo-SENDING{
    width: 100px;
    display: inline-flex;
    justify-content: center;
    padding: 10px 15px;
    background: rgba(26, 106, 171, .05);
    color: rgb(26, 106, 171);
}

.zo-table .zo-UNPAID {
    width: 100px;
    display: inline-flex;
    justify-content: center;
    padding: 10px 15px;
    background: rgba(255, 195, 5, .05);
    color: rgb(255, 195, 5);
}

</style>
