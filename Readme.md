# WooCommerce UPN nalog

Prikaže podatke za nakazilo in UPN nalog s QR kodo ob koncu naročila, v emailu za potrditev naročila in pod pregledom naročil.

![alt](pic1.png)

## Namestitev

**Prenesi [zadnjo verzijo](https://github.com/woocart/woocommerce-upn/releases/latest) »**

Za delovanje potrebuje nastavljen BACS (Direct Bank Transfer plačilni modul). 

Pozor! IBAN mora biti pravilen drugače se obrazec ne prikaže.

Za prilagajanje se lahko uprabi naslednje filtre:

```php
apply_filters('upn_code', "OTHR");
apply_filters('upn_reference', "SI00 %s");
apply_filters('upn_purpose', 'Plačilo naročila %s');
```
