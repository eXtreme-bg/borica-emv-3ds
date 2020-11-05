# Borica EMV 3DS

Borica EMV 3DS is PHP library providing an easier way to integrate newly released Borica protocol called EMV 3DS.

## 0. Requirements

*TBD*

## 1. Installation

### 1.1. Using Composer

Installation is recommended to be done via composer by running:

```
composer require extreme-bg/borica-emv-3ds "^0.3"
```

Alternatively you can add the following to the `require` section in your `composer.json` manually:

```
"extreme-bg/borica-emv-3ds": "^0.3"
```

## 2. Usage

### 2.1. Initialize

Create and configure `Borica` using your private key and certificate. The private key needs to be generated  by yourself (see `Cryptography` section for more details). The certificate (public key) is provided by Borica.

```
$borica = new Borica();
$borica->setPrivateKey('/var/www/certificates/borica.pem') // Absolute file path
    ->setPrivateKeyPassword('<Private Key Password>')
    ->setCertificate('/var/www/certificates/borica.cer') // Absolute file path
    ->setSandboxMode(true);
```

### 2.2. Create and send Sale Request `(TRTYPE=1)`

To make a sale request (most commonly used one in e-commerce), create and configure `SaleRequest`. Both `<МИД>` and `<ТИД>` are obtained from Borica. For all properties check the library source code.

```
$request = new SaleRequest();
$request->setTransactionType(TransactionType::SALE)
    ->setAmount(100.0)
    ->setCurrency('BGN')
    ->setOrder(9001)
    ->setDescription('Order via 1337.bg')
    ->setMerchantName('1337.bg')
    ->setMerchantUrl('https://1337.bg')
    ->setMerchant('<МИД>')
    ->setTerminal('<ТИД>')
    ->setEmail('extreme@1337.bg')
    ->setCountry('bg')
    ->setMerchantTimezone('+02')
    ->setTimestamp(time())
    ->setNonce(strtoupper(bin2hex(openssl_random_pseudo_bytes(16))))
    ->setBackRef('https://1337.bg/borica/response') // Absolute URL
    ->setOrderIdentifier($request->getOrder() . ' Website')
    ->setAddendum('AD,TD')
    ->sign($borica);
```

After you create the request, you need to generate an HTML form and redirect user to Borica payment page. See example implementation below:

```
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

After a user pays on the Borica payment page, they will be redirected to the `backUrl` specified in the request. Note that this is not guaranteed, because the user can close their browser or disable JavaScript used for redirecting. In this case see `2.4. Create Status Check Request`.

```
$borica = new Borica();
$borica->setPrivateKey('/var/www/certificates/borica.pem') // Absolute file path
    ->setPrivateKeyPassword('<Private Key Password>')
    ->setCertificate('/var/www/certificates/borica.cer') // Absolute file path
    ->setSandboxMode(true);

$response = Response::withPost($_POST)->verify($borica);

if (!$response->signatureIsVerified) {
    ...
}

if ($response->isSuccessful()) {
    ...
}
```

### 2.4. Create Status Check Request `(TRTYPE=90)`

If you want to check the status of an already sent request, create and configure `StatusCheckRequest`. `<ТИД>` is obtained from Borica.

```
$request = new StatusCheckRequest();
$request->setTransactionType(TransactionType::STATUS_CHECK)
    ->setTerminal('<ТИД>')
    ->setOrder(9001)
    ->setNonce(strtoupper(bin2hex(openssl_random_pseudo_bytes(16))))
    ->setOriginalTransactionType(TransactionType::SALE)
    ->sign($borica);
```

## 3. Cryptography

- Generate a private key with secure password:

```
$ openssl genrsa -out borica.key -aes256 2048
```

- Generate a code signing request (CSR) using your company information:

```
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