<?php
/*
Plugin Name: Pay2Pay for WooCommerce
Plugin URI: http://www.pay2pay.com/modul-oplaty-wordpress.html
Description: Модуль оплаты Pay2Pay для плагина WooCommerce для Wordpress.
Version: 1.0
Author: Oleg Fedorov
Author URI: http://www.pay2pay.com
*/

add_action("init", "wp_wc_pay2pay_init");
function wp_wc_pay2pay_init(){
    load_plugin_textdomain("wp_wc_pay2pay", false, basename(dirname(__FILE__)));
}

add_action( 'plugins_loaded', 'init_WC_Pay2Pay_Payment_Gateway' );
function init_WC_Pay2Pay_Payment_Gateway() {
	if (!class_exists('WC_Payment_Gateway'))
		return; // if the WC payment gateway class is not available, do nothing
    /**
     * Класс для работы с методом оплаты Pay2Pay для WooCommerce.
     * Смотри также наследуемый абстрактный класс WC_Payment_Gateway (есть комменты перед заготовками методов)
     */
    class WC_Pay2Pay_Payment_Gateway extends WC_Payment_Gateway{
        public function __construct(){
            $this->id = 'pay2pay';

            $this->has_fields = false;
            $this->method_title = 'Pay2Pay';
            $this->method_description = __( 'Payment by <a href="http://www.pay2pay.com/" title="Pay2Pay is a full service of your website in the field of organization and receiving electronic payments." target="_blank">Pay2Pay</a>.', 'wp_wc_pay2pay' );

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->merchant_id = $this->get_option('merchant_id');
            $this->secret_key = $this->get_option('secret_key');
            $this->hidden_key = $this->get_option('hidden_key');
            $this->pay_mode = $this->get_option('pay_mode');
            $this->test_mode = $this->get_option('test_mode');
            $this->icon_type = $this->get_option('icon_type');
            if($this->icon_type)
                $this->icon = apply_filters('woocommerce_pay2pay_icon', plugin_dir_url(__FILE__) . 'icons/pay2pay_88x31_' . $this->icon_type . '.png');


            // хук для сохранения опций, доступных в настройках метода оплаты в админке (опр. в ф-ции init_form_fields)
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            // хук для отрисовки формы перед переходом на мерчант (см. ф-цию receipt_page)
            add_action( 'woocommerce_receipt_pay2pay', array( $this, 'receipt_page' ) );
            // хук для обработки Result URL
            add_action( 'woocommerce_api_wc_pay2pay_payment_gateway', array( $this, 'check_ipn_response' ) );
        }

        /**
         * Метод определяет, какие поля будут доступны в настройках метода оплаты в админке.
         * Описание API см. здесь - http://docs.woothemes.com/document/settings-api/
         * @return string|void
         */
        public function init_form_fields(){
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Enable/Disable', 'wp_wc_pay2pay' ),
                    'type' => 'checkbox',
                    'label' => __( 'Enable', 'wp_wc_pay2pay' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __( 'Title', 'wp_wc_pay2pay' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'wp_wc_pay2pay' ),
                    'default' => 'Pay2Pay',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __( 'Description', 'wp_wc_pay2pay' ),
                    'type' => 'textarea',
                    'description' => __( 'This controls the description which the user sees during checkout.', 'wp_wc_pay2pay' ),
                    'default' => __( 'Payment by bank credit cards, terminals, money transfer and electronic payment systems (Webmoney, QIWI Purse, LiqPay, Yandex.Money etc.).', 'wp_wc_pay2pay' ),
                ),
                'merchant_id' => array(
                    'title' => __( 'Merchant ID', 'wp_wc_pay2pay' ),
                    'type' => 'text',
                    'description' => __( 'Unique id of the store in Pay2Pay system. You can find it in your <a href="https://cp.pay2pay.com" target="_blank">shop control panel</a>.', 'wp_wc_pay2pay' ),
                ),
                'secret_key' => array(
                    'title' => __( 'Secret key', 'wp_wc_pay2pay' ),
                    'type' => 'text',
                    'description' => __( 'Custom character set is used to sign messages are forwarded.', 'wp_wc_pay2pay' ),
                ),
                'hidden_key' => array(
                    'title' => __( 'Hidden key', 'wp_wc_pay2pay' ),
                    'type' => 'text',
                    'description' => __( 'Custom character set is used to sign hidden messages to Result URL.', 'wp_wc_pay2pay' ),
                ),
                'test_mode' => array(
                    'title' => __( 'Test mode', 'wp_wc_pay2pay' ),
                    'type' => 'checkbox',
                    'description' => __( '(optional) Test mode.', 'wp_wc_pay2pay' ),
                    'label' =>  __( 'Enable', 'wp_wc_pay2pay' ),
                ),
                'icon_type' => array(
                    'title' => __( 'Image', 'wp_wc_pay2pay' ),
                    'description' =>  __( '(optional) Pay2Pay icon which the user sees during checkout on payment selection page.', 'wp_wc_pay2pay' ),
                    'type' => 'select',
                    'options' => array(
                        '' => __( "Don't use", 'wp_wc_pay2pay' ),
                        'green' => __( 'Green', 'wp_wc_pay2pay' ),
                        'gray' => __( 'Grey', 'wp_wc_pay2pay' ),
                        'transp' => __( 'Transparent', 'wp_wc_pay2pay' ),
                    ),
                ),
                'pay_mode' => array(
                    'title' => __( 'Payment method', 'wp_wc_pay2pay' ),
                    'type' => 'text',
                    'description' => __( '(optional) Payment code. If set selected payment method will be chosen automatically. Available payment codes you can <a href="https://cp.pay2pay.com/?page=dev" title="Available payment codes list" target="_blank">look under "For developers" tab</a> in shop control panel.', 'wp_wc_pay2pay' ),
                ),
            );
        }

        /**
         * Метод обрабатывает событие "Размещения заказа".
         * Переводит покупателя на страницу, где формируется форма для перехода на мерчант.
         * @param $order_id номер заказа
         * @return array|void
         */
        public function process_payment( $order_id ){
            global $woocommerce;
            $order = new WC_Order( $order_id );
            return array(
                'result' => 'success',
                'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            );
        }

        public function receipt_page($order_id){
            global $woocommerce;
            $order = new WC_Order( $order_id );
            $lang = get_locale();
            switch($lang){
                case 'en_EN':
                    $lang = 'en';
                    break;
                case 'ru_RU':
                    $lang = 'ru';
                    break;
                default:
                    $lang = 'ru';
                    break;
            }
            $amount = number_format($order->order_total, 2, '.', '');
            $currency = get_woocommerce_currency();
            $available_currencies = array('BYR', 'EUR', 'RUB', 'UAH', 'USD', 'UZS');
            if($currency == 'RUR')
                $currency = 'RUB';
            if(!in_array($currency, $available_currencies))
                $currency = 'RUB';
            $desc = 'Оплата заказа №' . $order_id;
            $pay_mode = ($this->pay_mode)?'<paymode><code>' . $this->pay_mode . '</code></paymode>':'';
            $test_mode = ($this->test_mode == 'yes')?'<test_mode>1</test_mode>':'';
            $success_url = $this->get_return_url($order);
            $fail_url = $order->get_cancel_order_url();
            $result_url = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', __CLASS__, home_url( '/' ) ) );
            $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request>
    <version>1.2</version>
    <merchant_id>$this->merchant_id</merchant_id>
    <language>$lang</language>
    <order_id>$order_id</order_id>
    <amount>$amount</amount>
    <currency>$currency</currency>
    <description>$desc</description>
    $pay_mode
    <success_url><![CDATA[$success_url]]></success_url>
    <fail_url><![CDATA[$fail_url]]></fail_url>
    <result_url><![CDATA[$result_url]]></result_url>
    $test_mode
</request>
XML;
            $xml_encoded = base64_encode($xml);
            $sign = base64_encode(md5($this->secret_key . $xml . $this->secret_key));
            $thank_you =  __( '<p>Thank you for your order! Now you will be redirected to the payment page.</p>', 'wp_wc_pay2pay' );
            $form = <<<FORM
$thank_you
<form action="https://merchant.pay2pay.com/?page=init" method="POST" id="pay2pay_payment_form">
    <input type="hidden" name="xml" value="$xml_encoded">
    <input type="hidden" name="sign" value="$sign">
</form>
<script type="text/javascript">
    document.getElementById('pay2pay_payment_form').submit();
</script>
FORM;
            echo $form;
        }

        function check_ipn_response(){
            global $woocommerce;
            if (!isset($_POST['xml']) || !isset($_POST['sign']))
                $this->echoXmlError('POST xml and sign fields are supposed');
            @ob_clean();
            $_POST = stripslashes_deep($_POST);
            $xml_encoded = str_replace(' ', '+', $_POST['xml']);
            $sign = str_replace(' ', '+', $_POST['sign']);
            $xml_decoded = base64_decode($xml_encoded);
            $xml_check_sign = base64_encode( md5($this->hidden_key . $xml_decoded . $this->hidden_key) );
            if($xml_check_sign != $sign)
                $this->echoXmlError("Ключи не совпадают! Поле из POST:".$sign.", поле для проверки:".$xml_check_sign);
            $xml_object = simplexml_load_string($xml_decoded); // преобразуем входной xml в удобный для использования формат
            $order_id = (string)$xml_object->order_id; // номер заказа из xml ответа
            if(!$order_id)
                $this->echoXmlError("Проблема с получением id заказа из объекта xml");
            $order = new WC_Order($order_id); // создание объекта заказа из id заказа
            if(!$order)
                $this->echoXmlError("Order does not exist");
            $order_amount = $order->order_total; // сумма из заказа
            $response_amount = (double)$xml_object->amount; // сумма заказа из xml ответа
            // сравнение суммы заказа
            if($response_amount < $order_amount)
                $this->echoXmlError("Не совпадают суммы заказа! Сумма из корзины: ".$order_amount.", сумма из xml: ".$response_amount);
            switch((string)$xml_object->status){
                case 'success':
                    if($order->status == 'completed' || $order->status=='processing')
                        $this->echoXmlError("Order already has been paid.");
                    $order->payment_complete();
                    break;
                case 'fail':
                    $order->update_status('failed', __('Payment of the order is canceled.', 'wp_wc_pay2pay'));
                    break;
                case 'process':
                    $order->update_status('pending', __('Payment is expected.', 'wp_wc_pay2pay'));
                    break;
                case 'reserve':
                    $order->update_status('pending', __('Funds are reserved.', 'wp_wc_pay2pay'));
                    break;
                default:
                    $this->echoXmlError("Unknown status field value in XML request");
                    break;
            }
            // отвечаем серверу pay2pay об успешном прохождении операции
            $msg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <response>
                        <status>yes</status>
                        <err_msg></err_msg>
                    </response>
            ";
            die($msg);
        }

        /**
         * Выводит ошибку в виде xml строки на экран, завершает выполнение скрипта.
         * Также заносит описание ошибки в лог Woocommerce (см. в папке /plugins/woocommerce/logs).
         * @param string $error_desc - описание ошибки
         */
        private function echoXmlError($error_desc = 'unknown error'){
            global $woocommerce;
            $msg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                <response>
                    <status>no</status>
                    <err_msg>$error_desc</err_msg>
                </response>
            ";
            $woocommerce->logger()->add('pay2pay', $error_desc);
            die($msg);
        }
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_WC_Pay2Pay_Payment_Gateway' );
function add_WC_Pay2Pay_Payment_Gateway( $methods ){
    $methods[] = 'WC_Pay2Pay_Payment_Gateway';
    return $methods;
}