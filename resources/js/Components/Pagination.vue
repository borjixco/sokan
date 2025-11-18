<template>
    <nav class="rb-pagination-section q-pagination" v-if="pagination.links.length > 3">
        <template v-for="link in pagination.links" :key="link.label">
            <Link
                :href="buildUrl(link.url)"
                v-html="link.label"
                :class="{ 'q-active': link.active, '': !link.url }"
            />
        </template>
    </nav>
</template>

<script>
import {Link} from "@inertiajs/vue3";

export default {
    components: {
        Link
    },
    props: {
        pagination: Object, // pagination داده‌های صفحه‌بندی
        anchorId: String,   // شناسه‌ای که می‌خواهید به آن هدایت شود
    },
    methods: {
        // این متد query string فعلی را از URL می‌گیرد
        getQueryParams() {
            const queryString = window.location.search; // دریافت کل query string از URL
            return new URLSearchParams(queryString); // تبدیل به URLSearchParams برای راحتی کار با آن
        },

        // این متد لینک جدید را می‌سازد
        buildUrl(url) {
            if (!url) return null;

            const currentParams = this.getQueryParams(); // دریافت پارامترهای فعلی
            currentParams.set('page', this.getPageNumber(url)); // شماره صفحه را تنظیم می‌کنیم

            // ساخت URL با query string جدید
            let newUrl = `${url.split("?")[0]}?${currentParams.toString()}`;

            // اگر anchorId وجود دارد، آن را به URL اضافه می‌کنیم
            if (this.anchorId) {
                newUrl += `#${this.anchorId}`;
            }

            return newUrl;
        },

        // این متد شماره صفحه را از لینک دریافت می‌کند
        getPageNumber(url) {
            const match = url.match(/page=(\d+)/); // جستجو برای شماره صفحه در URL
            return match ? match[1] : 1; // اگر شماره صفحه وجود داشت، آن را برمی‌گرداند
        }
    }
}
</script>
