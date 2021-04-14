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

## Razvijalec

[WooCart](https://woocart.com/) je specializirano gostovanje za WooCommerce spletne trgovine. [Kontakt](https://woocart.com/contact).
