<template>
    <div>
        <q-btn
            :label="label"
            :icon="icon"
            :loading="mainLoading"
            @click="handleButtonClick"
        />

        <q-dialog v-if="props.useDialog" v-model="dialog">
            <q-card
                :class="cardClass"
                :style="`min-width: ${dialogWidth}`"
            >
                <q-card-section>
                    <div class="text-h6">{{ title }}</div>
                </q-card-section>

                <q-card-section>
                    <slot name="filters" :filters="localFilters" />
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="بستن" color="secondary" v-close-popup />
                    <q-btn
                        flat
                        :label="btnTitle"
                        color="primary"
                        :loading="exportLoading"
                        @click="handleExport"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<script setup>
import {ref, reactive} from 'vue'
import {defineProps} from 'vue'
import axios from 'axios'
import {showNotify} from '@/utils/helpers.js'

const props = defineProps({
    label: {type: String, default: 'خروجی اکسل'},
    icon: {type: String, default: 'description'},
    route: {type: String, required: true},
    slug: {type: String, required: true},
    title: {type: String, default: 'فیلتر خروجی'},
    btnTitle: {type: String, default: 'خروجی'},
    dialogWidth: {type: String, default: '400px'},
    cardClass: {type: String, default: ''},
    useDialog: {type: Boolean, default: true}
})

const dialog = ref(false)
const mainLoading = ref(false)
const exportLoading = ref(false)
const localFilters = reactive({})

function handleButtonClick() {
    if (props.useDialog) {
        dialog.value = true
    } else {
        handleExport()
    }
}

async function handleExport() {
    try {

        if (props.useDialog) {
            exportLoading.value = true
        } else {
            mainLoading.value = true
        }

        const response = await axios.post(props.route, {
            filters: localFilters
        }, {
            responseType: 'blob'
        })



        // قبل از دانلود بررسی کن که آیا پاسخ واقعی اکسل هست یا خطا
        const contentType = response.headers['content-type']

        if (contentType.includes('application/json')) {
            // یعنی سرور خطا فرستاده، ولی چون responseType 'blob' بود، باید دستی JSON کنیم
            const text = await response.data.text()
            const json = JSON.parse(text)
            throw new Error(json.message || 'خطایی رخ داده است.')
        }

        const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = `${props.slug}.xlsx`
        link.click()
        URL.revokeObjectURL(link.href)

        dialog.value = false
    } catch (error) {
        // اینجا دیگه error.message داره
        console.log(error)
        showNotify(error.message || 'خطایی رخ داده است.', 'negative')
    } finally {
        mainLoading.value = false
        exportLoading.value = false
    }
}

</script>
