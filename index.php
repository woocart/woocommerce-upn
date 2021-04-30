<?php
/*
Plugin Name: UPN plačilni nalog
Plugin URI: https://woocart.com
Description: Doda UPN plačilni nalog s QR kodo v vašo WooCommerce trgovino.
Version: 1.0.1
Author: WooCart
Author Email: info@woocart.com
License: GPLv2 or later
 */

namespace WooCart\UPNalog {

    require_once "vendor/autoload.php";

    class UPN
    {
        public $account_details;
		public $instructions;

        public function __construct()
        {

            // Get the gateways instance
            $gateways = \WC_Payment_Gateways::instance();

            // Get all available gateways, [id] => Object
            $available_gateways = $gateways->get_available_payment_gateways();
            if (isset($available_gateways['bacs'])) {
                // If the gateway is available, remove the action hooks
                remove_action('woocommerce_thankyou_bacs', array($available_gateways['bacs'], 'thankyou_page'));
                remove_action('woocommerce_email_before_order_table', array($available_gateways['bacs'], 'email_instructions'), 10, 3);
                $this->account_details = $available_gateways['bacs']->account_details;
				$this->instructions = $available_gateways['bacs']->instructions;
                add_action('woocommerce_email_before_order_table', array($this, 'upn_instructions'), 10, 3);
                add_action('woocommerce_order_details_after_customer_details', array($this, 'upn_page'), 20);
            }

        }

        public function genUPN($order)
        {

            if (empty($this->account_details)) {
				throw Exception("Empty accoutn details for BACS.");
                return;
            }

            $bacs_accounts = apply_filters('woocommerce_bacs_accounts', $this->account_details, $order->get_id());

            $bacs_account = (object) $bacs_accounts[0];

            $png = (new \Media24si\UpnGenerator\UpnGenerator())
                ->setPayerName(sprintf("%s %s", $order->get_formatted_billing_full_name(), $order->get_billing_last_name()))
                ->setPayerAddress($order->get_billing_address_1())
                ->setPayerPost(sprintf("%s %s", $order->get_billing_postcode(), $order->get_billing_city()))
                ->setReceiverName($bacs_account->account_name)
                ->setReceiverAddress(WC()->countries->get_base_address())
                ->setReceiverPost(sprintf("%s %s", WC()->countries->get_base_city(),
                    WC()->countries->get_base_postcode()))
                ->setReceiverIban(preg_replace('/\s+/', '', $bacs_account->iban))
                ->setAmount($order->get_total())
                ->setCode(apply_filters('upn_code', "OTHR"))
                ->setReference(sprintf(apply_filters('upn_reference', "SI00 %s"), $order->get_order_number()))
                ->setDueDate(new \DateTime($order->order_date))
                ->setPurpose(sprintf(apply_filters('upn_purpose', 'Plačilo naročila %s'), $order->get_order_number()))
                ->png();

            // Check for gd errors / buffer errors
            if (!empty($png)) {

                $data = base64_encode($png);

                // Check for base64 errors
                if ($data !== false) {

                    // Success
                    echo "<br/><img src='data:image/png;base64,$data'><br/>";
                }
            }
        }

        public function genUPNDescription($order)
        {
            if (empty($this->account_details)) {
                return;
            }

            $bacs_accounts = apply_filters('woocommerce_bacs_accounts', $this->account_details, $order->get_id());

            $bacs_account = (object) $bacs_accounts[0];

            ?>
                <table class="woocommerce-table shop_table">
                <tbody>
                    <tr>
                        <th>Prejemnik</th>
                        <td>

                        <?php echo wptexturize(wp_kses_post($bacs_account->account_name)); ?></br>
                        <?php echo wptexturize(wp_kses_post(WC()->countries->get_base_address())); ?></br>
                        <?php echo wptexturize(wp_kses_post(sprintf("%s %s", WC()->countries->get_base_city(), WC()->countries->get_base_postcode()))); ?>

                        </td>
                    </tr>
                    <tr>
                        <th>IBAN Prejemnika</th>
                        <td><?php echo wptexturize(wp_kses_post($bacs_account->iban)); ?></td>
                    </tr>
                    <tr>
                        <th>Namen</th>
                        <td><?php echo sprintf(apply_filters('upn_purpose', 'Plačilo naročila %s'), $order->get_order_number()); ?></td>
                    </tr>
                    <tr>
                        <th>Referenca Prejemnika</th>
                        <td><?php echo sprintf(apply_filters('upn_reference', "SI00 %s"), $order->get_order_number()); ?></td>
                    </tr>

                </tbody>
            </table>
            <?php
}

        /**
         * Add content to the WC emails.
         *
         * @param WC_Order $order Order object.
         * @param bool     $sent_to_admin Sent to admin.
         * @param bool     $plain_text Email format: plain text or HTML.
         */
        public function upn_instructions($order, $sent_to_admin, $plain_text = false)
        {

            if (!$sent_to_admin && 'bacs' === $order->get_payment_method() && $order->has_status('on-hold')) {
                if ($this->instructions) {
                    echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
                }
                $this->genUPNDescription($order);
                $this->genUPN($order);
            }

        }

        /**
         * Output for the order received page.
         *
         * @param int $order_id Order ID.
         */
        public function upn_page($order_id)
        {

            echo '</br>';
            echo '<h2 class="woocommerce-column__title">UPN Nalog</h2>';
            $order = wc_get_order($order_id);
            $this->genUPNDescription($order);
            $this->genUPN($order);
            if ($this->instructions) {
                echo wp_kses_post(wpautop(wptexturize(wp_kses_post($this->instructions))));
            }

        }

    }

    \add_action("woocommerce_init", function () {
        return new UPN();
    });
}
