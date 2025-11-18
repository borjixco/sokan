<template>
    <div class="zo-stat-section">
        <TitleSection
            v-if="loaded"
            icon="domain_add"
            title="وضعیت نقل و انتقالات"
            subtitle="نقل و انتقالات و اجاره نامه ها در سال جاری 1404"
        />
        <TitleSectionSkeleton v-else/>

        <q-card v-if="loaded">
            <q-card-section>
                <canvas ref="chartRef" width="425" height="225"></canvas>
            </q-card-section>
        </q-card>
        <ChartSkeleton v-else/>
    </div>
</template>
<script>

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
import {usePage} from "@inertiajs/vue3";
import {nextTick, onMounted, ref} from "vue";
import TitleSectionSkeleton from "@/Components/Skeleton/TitleSectionSkeleton.vue";
import ChartSkeleton from "@/Components/Skeleton/ChartSkeleton.vue";
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
    components: {
        TitleSection,
        ChartSkeleton,
        TitleSectionSkeleton
    },
    setup() {
        const barHeights = ref([])


        const page = usePage();
        const loaded = ref(false);
        const currentSection = page.props.currentSection;

        const chartRef = ref(null);

        let chartInstance = null;
        onMounted(() => {
// ارتفاع‌های تصادفی بین 50 تا 160 پیکسل
            barHeights.value = Array.from({ length: 15 }, () =>
                Math.floor(Math.random() * 110) + 100
            )

            fetch(route('admin.dashboard.chart.transfers', {}))
            .then(response => response.json())
            .then(function (res) {
                if (res.status === 'success') {
                    const labels = res.data.months.map(item => item.label);
                    const rentData = res.data.months.map(item => item.rent_count);
                    const saleData = res.data.months.map(item => item.sale_count);
                    loaded.value = true;
                    nextTick(() => {
                        if (chartRef.value) {
                            chartInstance = new Chart(chartRef.value, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        {
                                            label: 'نقل و انتقال',
                                            data: saleData,
                                            backgroundColor: "rgb(170, 130, 50)",
                                            borderRadius: '2.5',
                                            borderSkipped: 'bottom',
                                        },
                                        {
                                            label: 'اجاره‌نامه',
                                            data: rentData,
                                            backgroundColor: 'rgb(0, 75, 65)',
                                            borderRadius: '2.5',
                                            borderSkipped: 'bottom',
                                        },
                                    ],
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false
                                            },
                                            ticks: {
                                                font: {
                                                    family: 'Estedad-Medium',
                                                    weight: 'normal'
                                                }
                                            }
                                        },
                                        y: {
                                            ticks: {
                                                font: {
                                                    family: 'Estedad-Medium',
                                                    weight: 'normal'
                                                }
                                            }
                                        },
                                    },
                                    plugins: {
                                        title: {
                                            display: false,
                                        },
                                        tooltip: {
                                            rtl: true,
                                            textDirection: 'rtl',
                                            displayColors: false,
                                            bodyFont: {
                                                family: 'Estedad-Medium',
                                            },
                                            titleFont: {
                                                family: 'Estedad-Medium',
                                                weight: 'normal'
                                            },
                                        },
                                        legend: {
                                            labels: {
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                font: {
                                                    family: "Estedad-Regular",
                                                }
                                            },
                                            position: "top",
                                        },
                                    },
                                },
                            });
                        }
                    });


                } else {
                    showNotify(res.data.message, 'negative');
                }
            });
        });


        return {
            currentSection,
            chartRef,
            barHeights,
            loaded
        };
    },
};
</script>
