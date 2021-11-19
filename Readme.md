# WooCommerce UPN nalog

Prikaže podatke za nakazilo in UPN nalog s QR kodo ob koncu naročila, v emailu za potrditev naročila in pod pregledom naročil.

![alt](pic1.png)

## Namestitev

**Prenesi [zadnjo verzijo](https://github.com/woocart/woocommerce-upn/releases/latest) »**

Za delovanje potrebuje nastavljen BACS (Direct Bank Transfer plačilni modul). 

Pozor! IBAN mora biti pravilen drugače se obrazec ne prikaže.

Za prilagajanje se lahko uporabi naslednje filtre:

```php
apply_filters('upn_code', function(){return "OTHR";});
apply_filters('upn_reference', function(){return "SI00 %s";});
apply_filters('upn_purpose', function(){return 'Plačilo naročila %s';});
```

Pri page builder Elementor se UPN za neprijavljene uproabnike ne prikazuje. Če želite, da se UPN nalog prikazuje tudi za neprijavljene uporabnike, v functions.php dodatje sledeče.

```php
add_action( 'woocommerce_thankyou', 'adding_customers_details_to_thankyou', 10, 1 );
function adding_customers_details_to_thankyou( $order_id ) {
    // Only for non logged in users
    if ( ! $order_id || is_user_logged_in() ) return;

    $order = wc_get_order($order_id); // Get an instance of the WC_Order object

    wc_get_template( 'order/order-details-customer.php', array('order' => $order ));
}
```
## Razvijalec

[WooCart](https://woocart.com/) je specializirano gostovanje za WooCommerce spletne trgovine. [Kontakt](https://woocart.com/contact).
