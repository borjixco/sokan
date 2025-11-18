<template>
    <div class="zo-stat-section">

        <TitleSection
            v-if="loaded"
            icon="domain_add"
            title="وضعیت پرداختی شارژ ماهیانه"
            subtitle="وضعیت پرداختی شارژ ماهیانه در ماه شهریور"
        />
        <TitleSectionSkeleton v-else/>
        <q-card v-if="loaded">
            <q-card-section class="zo-doughnut-section">
                <canvas ref="doughnutRef" width="295" height="295"/>
            </q-card-section>
        </q-card>
        <DoughnutChartSkeleton v-else/>
    </div>
</template>

<script>
import {Head, usePage} from "@inertiajs/vue3";
import {nextTick, onMounted, ref} from "vue";
import {setCssVar} from 'quasar'

setCssVar('primary', 'rgb(0, 75, 65)')

import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    DoughnutController,
    ArcElement
} from "chart.js";
import TitleSectionSkeleton from "@/Components/Skeleton/TitleSectionSkeleton.vue";
import DoughnutChartSkeleton from "@/Components/Skeleton/DoughnutChartSkeleton.vue";
import TitleSection from "@/Components/TitleSection.vue";
import {route} from "ziggy-js";
import {showNotify} from "@/utils/helpers.js";


Chart.register(
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    DoughnutController,
    ArcElement
);

export default {
    components: {TitleSection, DoughnutChartSkeleton, TitleSectionSkeleton},
    setup() {
        const page = usePage();
        const loaded = ref(false);
        const currentSection = page.props.currentSection;

        const chartRef = ref(null);
        const doughnutRef = ref(null);

        let doughnutInstance = null;

        onMounted(() => {

            fetch(route('admin.dashboard.chart.charges', {}))
            .then(response => response.json())
            .then(function (res) {
                if (res.status === 'success') {
                    loaded.value = true;
                    nextTick(() =>{
                        if (doughnutRef.value) {
                            const paid = res.data.paid;
                            const unpaid = res.data.unpaid;
                            const total = paid + unpaid;

                            doughnutInstance = new Chart(doughnutRef.value, {
                                type: 'doughnut',
                                data: {
                                    labels: ['پرداخت شده', 'باقی‌مانده'],
                                    datasets: [
                                        {
                                            data: [paid, unpaid],
                                            backgroundColor: ['rgb(0, 75, 65)', 'rgb(225, 225, 225)'],
                                            borderWidth: 1,
                                        },
                                    ],
                                },
                                options: {
                                    cutout: '65%',
                                    responsive: true,
                                    plugins: {
                                        tooltip: {
                                            rtl: true,
                                            textDirection: 'rtl',
                                            displayColors: false,
                                            callbacks: {
                                                label: function (context) {
                                                    const value = context.raw.toLocaleString("fa-IR");
                                                    return `${context.label}: ${value} ریال`;
                                                },
                                            },
                                            bodyFont: {
                                                family: 'Estedad-Medium',
                                            },
                                            titleFont: {
                                                family: 'Estedad-Medium',
                                                weight: 'normal',
                                            },
                                        },
                                        legend: {
                                            position: 'top',
                                            labels: {
                                                font: {
                                                    family: 'Estedad-Regular',
                                                },
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                            },
                                        },
                                    },
                                },
                            });
                        }
                    })
                } else {
                    showNotify(res.message, 'negative');
                }
            });


        });

        return {
            currentSection,
            chartRef,
            doughnutRef,
            loaded
        };
    },
};
</script>
