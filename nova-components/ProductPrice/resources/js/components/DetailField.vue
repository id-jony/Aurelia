<template>
    <div class="flex flex-wrap p-6">
        <div class="w-full md:w-1/4 flex-1 px-4">
            <div class="flex flex-col">
                <div class="flex items-start">
                    <span class="mr-2 float-left w-full">Позиция в категории:</span>
                </div>
                <div class="flex items-start">
                    <span class="font-bold text-2xl">{{ position }} место</span>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/4 flex-1 px-4">
            <div class="flex flex-col">
                <div class="flex items-start">
                    <span class="mr-2 float-left w-full">Актуальная цена:</span>
                </div>
                <div class="flex items-start">
                    <span class="font-bold text-2xl">{{ priceBase | numberFormat }} ₸</span>
                    <span class="font-bold text-1xl text-gray-400" style="text-decoration: line-through; margin: 6px 8px;"> {{ price_old | numberFormat }} ₸</span>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/4 flex-1 px-4">
            <div class="flex flex-col">
                <div class="flex items-start">
                    <span class="mr-2 float-left w-full">Маржа:</span>
                </div>
                <div class="flex items-start">
                    <span class="font-bold text-2xl">{{ margin | numberFormat }} ₸</span>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/4 flex-1 px-4">
            <div class="flex flex-col">
                <div class="flex items-start">
                    <span class="mr-2 float-left w-full">Себестоимость:</span>
                </div>
                <div class="flex items-start content-between">
                    <span class="font-bold text-2xl" v-if="!isEditingPriceCost">{{ priceCost | numberFormat }} ₸</span>
                    <input type="text" v-model="editedPriceCost" v-if="isEditingPriceCost"
                        class="border rounded-md px-2 py-1" style="width: 60px;" @click.stop @blur="savePriceCost" />
                    <span class="ml-1" v-if="!isEditingPriceCost" @click.stop="startEditingPriceCost">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/4 flex-1 px-4">
            <div class="flex flex-col">
                <div class="flex items-start">
                    <span class="mr-2 float-left w-full">Минимальная цена:</span>
                </div>
                <div class="flex items-start content-center">
                    <span class="font-bold text-2xl" v-if="!isEditing">{{ priceMin | numberFormat }} ₸</span>
                    <input type="text" v-model="editedPriceMin" v-if="isEditing" class="border rounded-md px-2 py-1"
                        style="width: 60px;" @click.stop @blur="savePriceMin" />
                    <span class="ml-1" v-if="!isEditing" @click.stop="startEditing">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="flex p-3">
        <div class="mt-3 flex items-center mr-4">
            <input type="checkbox" id="weeklyUpdate" class="mr-2 checkbox" v-model="weeklyUpdateEnabled"
                @click.stop="saveWeeklyUpdate" />
            <label for="weeklyUpdate">Обновлять каждые 7 дней</label>
        </div>
        <div class="mt-3 flex items-center">
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
        if (this.field.value.autoreduction == 1) {
            this.field.value.autoreduction = true;
        }

        if (this.field.value.keep_published == 1) {
            this.field.value.keep_published = true;
        }
        return {
            autosaleEnabled: this.field.value.autoreduction,
            weeklyUpdateEnabled: this.field.value.keep_published,
            isEditing: false,
            editedPriceMin: this.field.value.priceMin,
            isEditingPriceCost: false,
            editedPriceCost: this.field.value.price_cost,
        };
    },
    computed: {
        position() {
            return this.field.value.position;
        },
        priceBase() {
            return this.field.value.priceBase;
        },
        price_old() {
            return this.field.value.price_old;
        },
        priceMin() {
            return this.field.value.priceMin;
        },
        priceCost() {
            return this.field.value.price_cost;
        },
        margin() {
            // Расчет маржи (здесь используйте вашу логику расчета)
            // Например, если маржа представляет собой разницу между актуальной ценой, себестоимостью и скидкой:
            return this.field.value.priceBase - this.field.value.price_cost - (this.field.value.priceBase * this.field.value.commission / 100);
        },
    },
    methods: {
        saveAutosale() {
            const payload = {
                autosaleEnabled: this.autosaleEnabled ? 0 : 1,
            };
            Nova.request().post(`/api/save-autosale/${this.field.value.id}`, payload)
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
            Nova.request().post(`/api/save-weekly-update/${this.field.value.id}`, payload)
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
            if (this.editedPriceMin !== this.field.value.priceMin) {
                Nova.request().post(`/api/save-price-min/${this.field.value.id}`, payload)
                    .then(response => {
                        this.isEditing = false;
                        this.field.value.priceMin = this.editedPriceMin;

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
            if (this.editedPriceCost !== this.field.value.price_cost) {
                Nova.request().post(`/api/save-price-cost/${this.field.value.id}`, payload)
                    .then(response => {
                        this.isEditingPriceCost = false;
                        this.field.value.priceCost = this.editedPriceCost;

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

    filters: {
        numberFormat(value) {
            var separator = " ";
            return value.replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g, "$1" + separator);
        },
    },
};
</script>
  