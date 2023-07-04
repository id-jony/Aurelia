<template>
    <div>

        <Head title="Kaspi Settings" />

        <Heading class="mb-6">Настройки Kaspi</Heading>
        <form @submit.prevent="saveSettings">
            <Card class="bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-100 dark:divide-gray-700 mb-8">

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:py-5">
                        <h2 class="font-normal text-md md:text-xl mb-2">
                            Kaspi Токен
                        </h2>
                        <p class="dark:text-white text-md opacity-90">
                            Уникальный идентификатор, позволяющий получить доступ к программному интерфейсу Kaspi.kz.
                            Сгенерировать токен авторизации можно самостоятельно через личный кабинет на Kaспи мерчант.
                            <a href="#" class="hover:underline underline-offset-4">Подробнее по ссылке.</a>
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="name-update-pokupatel-mombaeva-laura-text-field"
                            class="inline-block pt-2 leading-tight space-x-1">
                            <span>Токен:</span><span class="text-red-500 text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5">
                        <div class="space-y-1">
                            <input v-model="token" type="text" placeholder="Введите токен"
                                class="w-full form-control form-input form-input-bordered" id="token" dusk="token"
                                maxlength="-1">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:py-5">
                        <h2 class="font-normal text-md md:text-xl mb-2">
                            Kaspi Мерчант
                        </h2>
                        <p class="dark:text-white text-md opacity-90">
                            Введите данные для входа в личный кабинет Каспи Мерчант, чтобы иметь возможность управления
                            товарами через платформу. В личном кабинете Каспи Мерчант создайте нового пользователя разрешив
                            доступ во все разделы.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="name-update-pokupatel-mombaeva-laura-text-field"
                            class="inline-block pt-2 leading-tight space-x-1">
                            <span>Электронная почта:</span><span class="text-red-500 text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5">
                        <div class="space-y-1">
                            <input v-model="username" type="text" placeholder="Введите email"
                                class="w-full form-control form-input form-input-bordered" id="username" dusk="username"
                                maxlength="-1">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="name-update-pokupatel-mombaeva-laura-text-field"
                            class="inline-block pt-2 leading-tight space-x-1">
                            <span>Пароль:</span><span class="text-red-500 text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5">
                        <div class="space-y-1">
                            <input type="password" placeholder="Введите пароль"
                                class="w-full form-control form-input form-input-bordered" id="password" dusk="password"
                                maxlength="-1">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:py-5">
                        <h2 class="font-normal text-md md:text-xl mb-2">
                            Общие настройки
                        </h2>
                        <p class="dark:text-white text-md opacity-90">
                            Интервал автоснижения - этот параметр устанавливает шаг снижения цены относительно цены
                            конкурента.
                            Процент продаж - этот параметр устанавливает комиссию каспи для вычисления маржи.

                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="name-update-pokupatel-mombaeva-laura-text-field"
                            class="inline-block pt-2 leading-tight space-x-1">
                            <span>Интервал автоснижения:</span><span class="text-red-500 text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5 relative">
                        <div class="flex items-center">
                            <span class="absolute pl-4 text-gray-500 pr-2">₸</span>
                            <input v-model="interval_demp" type="text" placeholder="Введите сумму"
                                class="pl-10 w-full form-control form-input form-input-bordered" id="percent_sales"
                                dusk="percent_sales" maxlength="-1">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row" index="0">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
                        <label for="name-update-pokupatel-mombaeva-laura-text-field"
                            class="inline-block pt-2 leading-tight space-x-1">
                            <span>Процент продаж:</span><span class="text-red-500 text-sm">*</span>
                        </label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:py-5 relative">
                        <div class="flex items-center">
                            <span class="absolute pl-4 text-gray-500 pr-2">%</span>
                            <input v-model="percent_sales" type="text" placeholder="Введите процент продаж"
                                class="pl-10 w-full form-control form-input form-input-bordered" id="percent_sales"
                                dusk="percent_sales" maxlength="-1">
                        </div>
                    </div>
                </div>
            </Card>

            <div
                class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-3">
                <button
                    class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900"
                    type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            token: '',
            username: '',
            password: '',
            interval_demp: '',
            percent_sales: '',
        };
    },
    methods: {
        fetchSettings() {
            Nova.request().get('/kaspi-settings')
                .then(response => {
                    const data = response.data;
                    this.token = data.token || '';
                    this.username = data.username || '';
                    this.password = data.password || '';
                    this.interval_demp = data.interval_demp || '';
                    this.percent_sales = data.percent_sales || '';
                })
                .catch(error => {
                    console.error(error);
                });
        },
        saveSettings() {
            Nova.request().post('/kaspi-settings', {
                token: this.token,
                username: this.username,
                password: this.password,
                interval_demp: this.interval_demp,
                percent_sales: this.percent_sales,
            })
                .then(() => {
                    Nova.success('Настройки успешно сохранены.');
                })
                .catch(error => {
                    console.error(error);
                    Nova.error('Ошибка! Настройки не сохранены.');
                });
        },
    },
    created() {
        this.fetchSettings();
    },
};
</script>
  
<style>
/* Scoped Styles */
</style>
  