=== Plugin Name ===
Contributors: pay2pay
Donate link: http://www.pay2pay.com/modul-oplaty-wordpress.html
Tags: pay2pay, woocommerce, payment module, payment gateway, e-commerce, ecommerce, online payments
Requires at least: 3.5
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Pay2Pay payment gateway for WooCommerce eCommerce store plugin.

== Description ==

This plugin integrates Pay2Pay payment gateway for WooCoommerce 2.x eCommerce solution. Please notice that WooCommerce must be installed and activated.

Before installation you need to register at [pay2pay.com](http://www.pay2pay.com) ("Register" section).
Then, in [shop control panel](https://cp.pay2pay.com) complete the following fields that are used to configure the plugin:

*   Secret key - custom character set is used to sign messages are forwarded;
*   Hidden key - custom character set is used to sign hidden messages to Result URL.

Fields "Success URL", "Fail URL" and "Result URL" are not neccesary to fill.

How to install the module:

*   In the WP admin console select next menu items: Plugins / Add / Upload;
*   Select the plugin archive (wp-wc-pay2pay.zip) and press "Download";
*   In the console, select WordPress Plugins / Installed, click the "Activate";
*   Go to the WooCommerce -> Settings -> Payment Gateways, you will see the available methods of payment. Click on the link "Pay2Pay" to get access to the settings of the plugin;
*   Fill out all required plugin setting similar to your shop settings in [shop control panel](https://cp.pay2pay.com);
*   Set the test mode on.

To verify successful configuration adjustment, you must conduct test payments. During test payment funds will not be charged. Please note that in the [shop control panel](https://cp.pay2pay.com) successful operations status should be "completed". To switch your shop to the operation mode you need an agreement with the manager.

Testing of the plugin is done up to Wordpress 3.8 and WooCommerce 2.0.20.

== Installation ==

*   In the WP admin console select next menu items: Plugins / Add / Upload;
*   Select the plugin archive (wp-wc-pay2pay.zip) and press "Download";
*   In the console, select WordPress Plugins / Installed, click the "Activate";
*   Go to the WooCommerce -> Settings -> Payment Gateways, you will see the available methods of payment. Click on the link "Pay2Pay" to get access to the settings of the plugin;
*   Fill out all required plugin setting similar to your shop settings in [shop control panel](https://cp.pay2pay.com);
*   Set the test mode on.

== Описание ==
Данный плагин интеграции с Pay2Pay разработан для Wordpress WooCommerce 2.0.x.
Перед установкой модуля необходимо зарегистрироваться на сайте pay2pay.com (раздел "Подключиться") и в панели управления магазином заполнить следующие поля, значения которых используются при настройке плагина:

*   Секретный ключ - произвольный набор символов, используется для подписи сообщений при переадресации;
*   Скрытый ключ - произвольный набор символов, используется для подписи скрытых сообщений на Result URL;

Поля Success URL, Fail URL, Result URL заполнять не нужно.

Порядок установки модуля:

*   Выберите меню в консоли WordPress: Плагины / Добавить новый / Загрузить;
*   Выберите файл с архивом (wp-wc-pay2pay.zip) и нажмите "Загрузить";
*   В консоли WordPress выберите пункт меню  Плагины / Установленные, нажмите "Активировать";
*   Перейдите в меню WooCommerce -> Настройки -> Способы оплаты, Вы увидите доступные способы оплаты. Нажмите на ссылку "Pay2Pay" для перехода к настройкам плагина;
*   Укажите все необходимые для работы с Pay2Pay настройки, аналогичные настройкамсвоего магазина в панели управления Pay2Pay (https://cp.pay2pay.com);
*   Установите тестовый режим работы.

Для проверки настройки системы необходимо провести тестовые платежи, денежные средства по которым сниматься не будут. Обратите внимание, что в панели управления магазином на сайте pay2pay.com статус успешно выполненных операций должен иметь значение "Выполнен". Переключение в рабочий режим происходит после согласования с менеджером.

Тестирование работы плагина происходило вплоть до Wordpress 3.8 и WooCommerce 2.0.20.