# Borica EMV 3DS

Borica EMV 3DS is PHP library providing an easier way to integrate newly released Borica protocol called EMV 3DS.

**The library supports the new MAC_GENERAL signage algorithm and will work after the 1 August 2023 changes.**

## 0. Requirements

*TBD*

* PHP 7.1 or newer
* Properly configured `default_charset` php.ini directive

## 1. Installation

### 1.1. Using Composer

Installation is recommended to be done via composer by running:

```shell
composer require extreme-bg/borica-emv-3ds "1.*"
```

Alternatively you can add the following to the `require` section in your `composer.json` manually:

```json
"extreme-bg/borica-emv-3ds": "1.*"
```

## 2. Usage

### 2.1. Initialize

Create and configure `Borica` using your private key and certificate. The private key needs to be generated  by yourself (see `Cryptography` section for more details). The certificate (public key) is provided by Borica.

```php
$borica = new Borica();
$borica->setPrivateKey('/var/www/certificates/borica.pem') // Absolute file path
    ->setPrivateKeyPassword('<Private Key Password>')
    ->setCertificate('/var/www/certificates/borica.cer') // Absolute file path
    ->setSandboxMode(true)
    ->setSigningAlgorithm(SigningAlgorithm::MAC_GENERAL);
```

### 2.2. Create and send Sale Request `(TRTYPE=1)`

**At the moment it works only with Bulgarian lev (BGN). Borica works on Euro (EUR) support.**

To make a sale request (most commonly used one in e-commerce), create and configure `SaleRequest`. Both `<МИД>` and `<ТИД>` are obtained from Borica. For all properties check the library source code.

**Don't forget to use sanitized data instead of raw $_POST data.**

```php
require_once __DIR__ . '/vendor/autoload.php';

use BogdanKovachev\Borica\Borica;
use BogdanKovachev\Borica\SigningAlgorithm;
use BogdanKovachev\Borica\TransactionType;
use BogdanKovachev\Borica\Request\SaleRequest;

$request = new SaleRequest();
$request->setTransactionType(TransactionType::SALE)
    ->setAddendum('AD,TD')
    ->setAmount(100.0)
    ->setCountry('bg')
    ->setCurrency('BGN')
    ->setDescription('Order via 1337.bg')
    ->setEmail('extreme@1337.bg')
    ->setMerchant('<МИД>')
    ->setMerchantName('1337.bg')
    ->setMerchantTimezone('+02')
    ->setMerchantUrl('https://1337.bg')
    ->setNonce(strtoupper(bin2hex(openssl_random_pseudo_bytes(16))))
    ->setOrder(9001)
    ->setOrderIdentifier($request->getOrder() . ' Website')
    ->setTerminal('<ТИД>')
    ->setTimestamp(time())
    ->sign($borica);
```

Optionally you can validate that all the properties are correct with:

```php
if (!$request->validate()) {
    // List all errors
    var_dump($request->getErrors());
}
```

After you create the request, you need to generate an HTML form and redirect user to Borica payment page. See example implementation below:

```html
<div>
    <p>
        Ще бъдете прехвърлени към страницата за онлайн плащания на БОРИКА през защитена (SSL) връзка.
    </p>

    <p>
        За нареденото от вас плащане, няма да ви бъдат удържани банкови такси.
    </p>
</div>

<div style="display: none;">
    <?= $request->renderForm($borica) ?>
</div>

<script type="text/javascript">
    window.onload = function () {
        window.setTimeout(function () {
            document.getElementById('boricaForm').submit();
        }, 3000);
    };
</script>
```

### 2.3. Handle response

After a user pays on the Borica payment page, they will be redirected to the `backUrl` defined for the terminal in APGW database (**check with the bank that this URL is correctly set for the terminal**). Note that this is not guaranteed, because the user can close their browser or disable JavaScript used for redirecting. In this case see `2.4. Create Status Check Request`.

```php
require_once __DIR__ . '/vendor/autoload.php';

use BogdanKovachev\Borica\Borica;
use BogdanKovachev\Borica\SigningAlgorithm;
use BogdanKovachev\Borica\TransactionType;
use BogdanKovachev\Borica\Response\Response;

$borica = new Borica();
$borica->setPrivateKey('/var/www/certificates/borica.pem') // Absolute file path
    ->setPrivateKeyPassword('<Private Key Password>')
    ->setCertificate('/var/www/certificates/borica.cer') // Absolute file path
    ->setSandboxMode(true)
    ->setSigningAlgorithm(SigningAlgorithm::MAC_GENERAL);

$response = Response::withPost($_POST)->verify($borica);

if (!$response->signatureIsVerified) {
    ...
}

if ($response->isSuccessful()) {
    echo '<h1>Плащането е успешно</h1>';

    ...
} else {
    echo '<h1>Възникна грешка при плащане</h1>';
    echo '<p>Отговор на сървъра: ' . $response->responseCode . ' - ' . $response->responseCodeDescription() . '</p>';
}
```

### 2.4. Create Status Check Request `(TRTYPE=90)`

If you want to check the status of an already sent request, create and configure `StatusCheckRequest`. `<ТИД>` is obtained from Borica.

```php
require_once __DIR__ . '/vendor/autoload.php';

use BogdanKovachev\Borica\Borica;
use BogdanKovachev\Borica\Request\StatusCheckRequest;
use BogdanKovachev\Borica\Response\Response;
use BogdanKovachev\Borica\SigningAlgorithm;
use BogdanKovachev\Borica\TransactionType;

$request = new StatusCheckRequest();
$request->setTransactionType(TransactionType::STATUS_CHECK)
    ->setNonce(strtoupper(bin2hex(openssl_random_pseudo_bytes(16))))
    ->setOrder(9001)
    ->setOriginalTransactionType(TransactionType::SALE)
    ->setTerminal('<ТИД>')
    ->sign($borica);
```

### 2.5. Create Reversal Request `(TRTYPE=24)`

To reverse successful `SaleRequest` (TRTYPE=1) or completed deferred authorization (TRTYPE=24), create and configure `ReversalRequest`.

`<RRN>` and `<INT_REF>` are returned in response of `SaleRequest` and are unique for every transaction. Both `<МИД>` and `<ТИД>` are obtained from Borica. For all properties check the library source code.

```php
require_once __DIR__ . '/vendor/autoload.php';

use BogdanKovachev\Borica\Borica;
use BogdanKovachev\Borica\Request\ReversalRequest;
use BogdanKovachev\Borica\Response\Response;
use BogdanKovachev\Borica\SigningAlgorithm;
use BogdanKovachev\Borica\TransactionType;

$request = new ReversalRequest();
$request->setTransactionType(TransactionType::REVERSAL)
    ->setAddendum('AD,TD')
    ->setAmount(100.0)
    ->setCurrency('BGN')
    ->setDescription('Отмяна на плащане през bulmint.com')
    ->setInternalReference('<INT_REF>')
    ->setMerchant('<МИД>')
    ->setMerchantName('Мебели Дизма')
    ->setNonce(strtoupper(bin2hex(openssl_random_pseudo_bytes(16))))
    ->setOrder(9001)
    ->setOrderIdentifier($request->getOrder() . ' Website')
    ->setRetrievalReferenceNumber('<RRN>')
    ->setTerminal('<ТИД>')
    ->setTimestamp(time())
    ->sign($borica);
```

## 3. Cryptography

- Generate a private key with secure password:

```shell
$ openssl genrsa -out borica.key -aes256 2048
```

- Generate a code signing request (CSR) using your company information:

```shell
$ openssl req -new -key borica.key -out borica.csr

	Country Name (2 letter code) []:BG
	State or Province Name (full name) []:Plovdiv
	Locality Name (eg, city) []:Plovdiv
	Organization Name (eg, company) []:1337 LTD
	Organizational Unit Name (eg, section) []:V0000000
	Common Name (eg, fully qualified host name) []:1337.bg
	Email Address []:extreme@1337.bg
	A challenge password []: <empty>
```

- Rename `borica.csr` to match file pattern `TID_YYYYMMDD.csr` and send it to Borica. Use your `ТИД` and `current date` (i.e. `V0000000_20201105.csr`).

- In response you'll receive a signed certificate (`borica.cer`) and a public key (`borica.pub`) from Borica.

## 4. Contributing

*TBD*

## 5. License

Borica EMV 3DS is licensed under the MIT License.