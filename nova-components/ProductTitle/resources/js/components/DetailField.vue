<template>
    <div class="flex gap-4">
        <div class="flex-1">
            <div class="flex items-start pb-4 pt-4">
                <div class="overflow-hidden mr-4">
                    <img :src="field.value.primaryImage" alt="Product Image" class="w-80 object-cover" width="80px">
                </div>
                <div>
                    <div class="text-gray-500 mt-4">Код товара: {{ field.value.sku }}</div>
                    <div class="font-bold text-l">{{ field.value.name }}</div>
                    <div class="text-gray-400">Категория: {{ field.value.category }}</div>
                    <div class="text-gray-500 mb-4">Бренд: {{ field.value.brand }}</div>

                    <a :href="field.value.productUrl" class="link-default" target="_blank" @click.stop="openLink">Посмотреть
                        на Kaspi.kz</a>
                </div>
            </div>
        </div>
        <div class="flex-1">
            <div class="flex items-center justify-end pt-4 pb-4">
                <div class="mr-2 flex items-center">
                    <svg v-if="field.value.status === 'В продаже'" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block text-green-500"
                        role="presentation">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-else-if="field.value.status === 'В архиве'" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block text-red-500"
                        role="presentation">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        width="24" height="24" class="inline-block text-blue-500" role="presentation">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex items-center">{{ field.value.status }}</div>
            </div>
            <div class="flex items-center justify-end pt-4 pb-4">
                <div v-for="promo in field.value.promo" :key="promo.code" class="mr-2 flex items-center">
                    <img :src="promo.src" :alt="promo.code" class="h-6 mr-1">
                    <!-- <span>{{ promo.code }} ({{ promo.type }})</span> -->
                </div>
            </div>
        </div>
    </div>


    <div class="bg-gray-100 rounded-lg p-4">
        <div class="flex mb-4">
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="block mr-2">Актуальная цена:</span>
                <span class="block font-bold text-xl">{{ product.priceBase | numberFormat }} ₸</span>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="block mr-2">Маржа:</span>
                <span class="block font-bold text-xl">{{ margin | numberFormat }} ₸</span>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="flex mr-2">Себестоимость:</span>
                <div class="flex">
                    <span class="block font-bold text-xl" v-if="!isEditingPriceCost">{{ product.price_cost | numberFormat }}
                        ₸</span>
                    <input type="text" v-model="editedPriceCost" v-if="isEditingPriceCost"
                        class="border rounded-md px-2 py-1" style="width: 60px;" @click.stop @blur="savePriceCost" />
                    <span class="ml-1" v-if="!isEditingPriceCost" @click.stop="startEditingPriceCost">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px;" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0">
                <span class="block mr-2">Минимальная цена:</span>
                <div class="flex">
                    <span class="block font-bold text-xl" v-if="!isEditing">{{ product.priceMin | numberFormat }} ₸</span>
                    <input type="text" v-model="editedPriceMin" v-if="isEditing" class="border rounded-md px-2 py-1"
                        style="width: 60px;" @click.stop @blur="savePriceMin" />
                    <span class="ml-1" v-if="!isEditing" @click.stop="startEditing">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px;" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="block mr-2">Конкуренты:</span>
                <span class="block font-bold text-xl">{{ this.field.value.rivalcount | numberFormat }} шт.</span>
            </div>
        </div>

        <hr class="h-px my-8 mb-4 bg-gray-200 border-0 dark:bg-gray-700">

        <div class="flex">
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="block mr-2">Кол-во продаж:</span>
                <span class="block font-bold text-xl">{{ Count | numberFormat }} шт.</span>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="block mr-2">Сумма продаж:</span>
                <span class="block font-bold text-xl">{{ SumMoney | numberFormat }} ₸</span>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="block mr-2">Комиссия kaspi:</span>
                <span class="block font-bold text-xl">{{ this.field.value.product.categories.commission | numberFormat }} %</span>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <span class="block mr-2">Чистая прибыль:</span>
                <span class="block font-bold text-xl">{{ NetR | numberFormat }} ₸</span>
            </div>
            <div class="w-full sm:w-auto flex flex-col justify-center items-start sm:mb-0 sm:mr-4">
                <!-- <span class="block mr-2">Чистая прибыль:</span>
                <span class="block font-bold text-xl">{{ this.field.value.rivalcount | numberFormat }} ₸</span> -->
            </div>
        </div>
    </div>


    <div class="flex">
        <div class="p-6 pl-2 flex items-left mr-6">
            <input type="checkbox" id="weeklyUpdate" class="mr-2 checkbox" v-model="weeklyUpdateEnabled"
                @click.stop="saveWeeklyUpdate" />
            <label for="weeklyUpdate">Обновлять каждые 7 дней</label>
        </div>
        <div class="p-6 pl-2 flex items-left mr-6">
            <input type="checkbox" id="autosale" class="mr-2 checkbox" v-model="autosaleEnabled"
                @click.stop="saveAutosale" />
            <label for="autosale">Автоснижение цены</label>
        </div>
    </div>

</template>

<script>
export default {
    props: ['resourceName', 'field'],
    data() {
        if (this.field.value.product.autoreduction == 1) {
            this.field.value.product.autoreduction = true;
        }

        if (this.field.value.product.keep_published == 1) {
            this.field.value.product.keep_published = true;
        }
        return {
            autosaleEnabled: this.field.value.product.autoreduction,
            weeklyUpdateEnabled: this.field.value.product.keep_published,
            isEditing: false,
            editedPriceMin: this.field.value.product.priceMin,
            isEditingPriceCost: false,
            editedPriceCost: this.field.value.product.price_cost,
        };
    },
    computed: {
        product() {
            return this.field.value.product;
        },
        SumMoney() {
            return this.field.value.SumMoney;
        },
        Count() {
            return this.field.value.Count;
        },
        NetR() {
            // Расчет маржи (здесь используйте вашу логику расчета)
            return (this.field.value.SumMoney  - (this.field.value.SumMoney * this.field.value.product.categories.commission / 100)) - (this.field.value.product.price_cost * this.field.value.Count);
        },
        margin() {
            // Расчет маржи (здесь используйте вашу логику расчета)
            return this.field.value.product.priceBase - this.field.value.product.price_cost - (this.field.value.product.priceBase * this.field.value.product.categories.commission / 100);
        },
        truncatedName() {
            return this.field.value.product.name.slice(0, 60) + '...';

        },
    },
    methods: {
        openLink(event) {
            event.preventDefault();
            window.open(this.field.value.product.productUrl, '_blank');
        },
        saveAutosale() {
            const payload = {
                autosaleEnabled: this.autosaleEnabled ? 0 : 1,
            };
            Nova.request().post(`/api/save-autosale/${this.field.value.product.id}`, payload)
                .then(response => {
                    // Обработка успешного сохранения
                    console.log(response.data.message);
                })
                .catch(error => {
                    // Обработка ошибки сохранения
                    console.error('Ошибка при сохранении автоснижения цены:', error);
                });
        },
        saveWeeklyUpdate() {
            const payload = {
                weeklyUpdateEnabled: this.weeklyUpdateEnabled ? 0 : 1,
            };
            Nova.request().post(`/api/save-weekly-update/${this.field.value.product.id}`, payload)
                .then(response => {
                    // Обработка успешного сохранения
                    console.log(response.data.message);
                })
                .catch(error => {
                    // Обработка ошибки сохранения
                    console.error('Ошибка при сохранении обновления каждые 7 дней:', error);
                });
        },

        startEditing() {
            this.isEditing = true;
            this.editedPriceMin = this.priceMin;
        },

        startEditingPriceCost() {
            this.isEditingPriceCost = true;
            this.editedPriceCost = this.priceCost;
        },
        savePriceMin() {
            const payload = {
                priceMin: this.editedPriceMin,
            };
            if (this.editedPriceMin !== this.field.value.product.priceMin) {
                Nova.request().post(`/api/save-price-min/${this.field.value.product.id}`, payload)
                    .then(response => {
                        this.isEditing = false;
                        this.field.value.product.priceMin = this.editedPriceMin;

                        console.log(response.message);
                    })
                    .catch(error => {
                        console.error('Ошибка при сохранении минимальной цены:', error);
                    });
            } else {
                this.isEditing = false;
            }
        },

        savePriceCost() {
            const payload = {
                priceCost: this.editedPriceCost,
            };
            if (this.editedPriceCost !== this.field.value.product.priceCost) {
                Nova.request().post(`/api/save-price-cost/${this.field.value.product.id}`, payload)
                    .then(response => {
                        this.isEditingPriceCost = false;
                        this.field.value.product.priceCost = this.editedPriceCost;

                        console.log(response.message);
                    })
                    .catch(error => {
                        console.error('Ошибка при сохранении минимальной цены:', error);
                    });
            } else {
                this.isEditing = false;
            }
        },

    },
};
</script>

<style scoped>
.w-80 {
    width: 180px;
}
</style>
