<?php

namespace BogdanKovachev\Borica\Request;

use BogdanKovachev\Borica\Borica;
use BogdanKovachev\Borica\TransactionType;

/**
 * @author Bogdan Kovachev (https://1337.bg)
 */
class Request {

    /**
     * Fields used for generating message authentication code (MAC)
     */
    const MAC_FIELDS = [
        TransactionType::SALE => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'TIMESTAMP'
        ],
        TransactionType::DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'TIMESTAMP',
            'DESC'
        ],
        TransactionType::COMPLETE_DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'TIMESTAMP',
            'DESC'
        ],
        TransactionType::REVERSE_DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'TIMESTAMP',
            'DESC'
        ],
        TransactionType::REVERSAL => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'TIMESTAMP',
            'DESC'
        ],
        TransactionType::STATUS_CHECK => [
            'TERMINAL',
            'TRTYPE',
            'ORDER'
        ]
    ];

    /**
     * Fields used for generating extended message authentication code (MAC)
     */
    const MAC_EXTENDED_FIELDS = [
        TransactionType::SALE => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'MERCHANT',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'MERCHANT',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::COMPLETE_DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'MERCHANT',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::REVERSE_DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'MERCHANT',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::REVERSAL => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'MERCHANT',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::STATUS_CHECK => [
            'TERMINAL',
            'TRTYPE',
            'ORDER',
            'NONCE'
        ]
    ];

    /**
     * Описание: Тип на транзакцията
     * Размер: 1-2
     * Съдържание: Възможни стойности 1, 12, 21, 22, 24, 90
     *
     * @var integer
     */
    protected $transactionType;

    /**
     * Описание: Сума
     * Размер: 1-12
     * Съдържание: Обща стойност на поръчката по стандарт ISO_4217 (https://en.wikipedia.org/wiki/ISO_4217) с десетичен разделител точка (напр. 12.00). Сумата на поръчката заедно с десетичната точка. Пример 10.20 Ако не се въведат цифри след десетичния разделител, сумата се възприема като цяло число, например 200 = 200 BGN
     *
     * @var float
     */
    protected $amount;

    /**
     * Описание: Валута
     * Размер: 3
     * Съдържание: Валута на поръчката: три буквен код на валута по стандарт ISO 4217 (https://en.wikipedia.org/wiki/ISO_4217)
     *
     * @var string
     */
    protected $currency = 'BGN';

    /**
     * Описание: Номер на поръчка
     * Размер: 6
     * Съдържание: Номер на поръчката за търговеца, 6 цифри, който трябва да бъде уникален за терминала в рамките на деня (напр. „000123“)
     *
     * @var integer
     */
    protected $order;

    /**
     * Описание: Описание
     * Размер: 1-50
     * Съдържание: Описание на поръчката. Използва са за предоставяне на информация на платежната страница от страна на търговеца за картодържателя. Възможно е използване на кирилица.
     *
     * @var string
     */
    protected $description;

    /**
     * Описание: URL на търговеца
     * Размер: 1-250
     * Съдържание: URL на web сайта на търговеца
     *
     * @var string
     */
    protected $merchantUrl;

    /**
     * Описание: Име на търговеца.
     * Размер: 1-80
     * Съдържание: Използва са за предоставяне на информация на платежната страница от страна на търговеца за картодържателя. Възможно е използване на кирилица.
     *
     * @var string
     */
    protected $merchantName;

    /**
     * Описание: Идентификатор на търговеца
     * Размер: 10-15
     * Съдържание: Merchant ID
     *
     * @var string
     */
    protected $merchant;

    /**
     * Описание: Идентификатор на терминала
     * Размер: 8
     * Съдържание: Terminal ID
     *
     * @var string
     */
    protected $terminal;

    /**
     * Размер: 80
     * Съдържание: E-mail адрес за уведомления. Ако това поле е попълнено, платежният сървър изпраща резултата от трансакцията на посочения e-mail адрес.
     *
     * @var string
     */
    protected $email;

    /**
     * Описание: Държава
     * Размер: 2
     * Съдържание: Двубуквен код на държавата, където се намира магазинът на търговеца, по стандарт ISO 3166-1 (https://en.wikipedia.org/wiki/ISO_3166-1). Трябва да се предостави, ако търговецът се намира в страна, различна от тази на gateway server-а.
     *
     * @var string
     */
    protected $country = 'BG';

    /**
     * Описание: Часова зона на търговеца
     * Размер: 1-5
     * Съдържание: Отстояние на часовата зона на търговеца от UTC/GMT (напр. +03). Трябва да се предостави, ако системата на търговеца се намира в различна зона от тази на gateway server-а.
     *
     * @var string
     */
    protected $merchantTimezone = '+03';

    /**
     * Описание: Тип на оригиналната транзакция
     * Размер: 1-2
     * Съдържание: Тип на оригиналната трансакция в заявка „Проверка на статус“
     *
    * @var string
    */
    protected $originalTransactionType;

    /**
     * Описание: Дата/час
     * Размер: 14
     * Съдържание: Време на транзакцията по UTC (GMT): YYYYMMDDHHMMSS. Разлика между времето на сървъра на търговеца и e-Gateway сървъра не трябва да надвишава 1 час. В противен случай e-Gateway ще отхвърли трансакцията.
     *
     * @var string
     */
    protected $timestamp;

    /**
     * Размер: 32
     * Съдържание: Съдържа 16 непредсказуеми случайни байтове, представенив шестнадесетичен формат. Може да съдържа главни латински букви A..Z и цифри 0..9. Трябва да бъде уникален за терминала в рамките на последните 24 часа.
     *
     * @var string
     */
    protected $nonce;

    /**
     * Описание: Подпис
     * Размер: 512
     * Съдържание: Код за автентициране на съобщението от APGW. Съдържа 256 байта в шестнадесетичен формат. Може да съдържа главни латински букви A..Z и цифри 0..9.
     *
     * @var string
     */
    protected $pSign;

    /**
     * Описание: Референция на транзакцията
     * Размер: 12
     * Съдържание: Референция на трансакцията (ISO-8583 -1987, поле 37).
     *
     * @var string
     */
    protected $retrievalReferenceNumber;

    /**
     * Описание: Вътрешна референция
     * Размер: 16
     * Съдържание: Вътрешна референция за е-Commerce gateway
     *
     * @var string
     */
    protected $internalReference;

    /**
     * Размер: 0-35000
     * Съдържание: Опционален набор от данни по протокола EMV 3DS v.2. Трябва да бъде Base64-encoded string of JSON-formatted “parameter”:”value data. Пример: {"threeDSRequestorChallengeInd":"04"}
     *
     * @var string
     */
    protected $mInfo;

    /**
     * Описание: Език
     * Размер: 2
     * Съдържание: Език на транзакцията BG или EN. По подразбиране е избран език BG.
     *
     * @var string
     */
    protected $language = 'BG';

    /**
     * Описание: Идентификатор на поръчката
     * Размер: 22
     * Съдържание: ORDER + 16 символа. Използва се за информация, с която търговецът и картодържателят да разпознават плащането. Предава се през финансовите файлове. Въведената информация следва да се състои от цифри и латински букви.
     * Използва за предаване на номера на поръчката към Банката на търговеца във финансовите файлове. Полето трябва да съдържа значението на поле ORDER (Поръчка) - 6 цифри, конкатенирано със символен низ с дължина до 16 символа. Същият низ може да се използва като символен номер на поръчка с размер до 16 символа.
     * Полето не трябва да съдържа символ “;”.
     *
     * @var string
     */
    protected $adCustBorOrderId; // TODO: Rename

    /**
     * Описание: Допълнение
     * Размер: 5
     * Съдържание: Служебно поле със стойност “AD,TD”. Подава се задължително, ако присъства поле „AD.CUST_BOR_ORDER_ID”.
     *
     * @var string
     */
    protected $addendum = 'AD,TD';

    /**
     * Validation errors in format: `property` => [`error`, ...]
     *
     * @var array
     */
    protected $errors = [];

    /**
     * @param integer $transactionType
     * @return Request
     */
    public function setTransactionType(int $transactionType) : Request {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @param float $amount
     * @return Request
     */
    public function setAmount(float $amount) : Request {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param string $currency
     * @return Request
     */
    public function setCurrency(string $currency) : Request {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @param integer $order
     * @return Request
     */
    public function setOrder(int $order) : Request {
        $this->order = $order;

        return $this;
    }

    /**
     * @param string $description
     * @return Request
     */
    public function setDescription(string $description) : Request {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $merchantName
     * @return Request
     */
    public function setMerchantName($merchantName) : Request {
        $this->merchantName = $merchantName;

        return $this;
    }

    /**
     * @param string $merchantUrl
     * @return Request
     */
    public function setMerchantUrl(string $merchantUrl) : Request {
        $this->merchantUrl = $merchantUrl;

        return $this;
    }

    /**
     * @param string $merchant
     * @return Request
     */
    public function setMerchant(string $merchant) : Request {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * @param string $terminal
     * @return Request
     */
    public function setTerminal(string $terminal) : Request {
        $this->terminal = $terminal;

        return $this;
    }

    /**
     * @param string $email
     * @return Request
     */
    public function setEmail(string $email) : Request {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $country
     * @return Request
     */
    public function setCountry(string $country) : Request {
        $this->country = $country;

        return $this;
    }

    /**
     * @param string $merchantTimezone
     * @return Request
     */
    public function setMerchantTimezone(string $merchantTimezone) : Request {
        $this->merchantTimezone = $merchantTimezone;

        return $this;
    }

    /**
     * @param integer $timestamp
     * @return Request
     */
    public function setTimestamp(int $timestamp) : Request {
        $this->timestamp = gmdate('YmdHis', $timestamp);

        return $this;
    }

    /**
     * @param string $nonce
     * @return Request
     */
    public function setNonce(string $nonce) : Request {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * @param string $pSign
     * @return Request
     */
    public function setPSign(string $pSign) : Request {
        $this->pSign = $pSign;

        return $this;
    }

    /**
     * @param string $retrievalReferenceNumber
     * @return Request
     */
    public function setRetrievalReferenceNumber(string $retrievalReferenceNumber) : Request {
        $this->retrievalReferenceNumber = $retrievalReferenceNumber;

        return $this;
    }

    /**
     * @param string $internalReference
     * @return Request
     */
    public function setInternalReference(string $internalReference) : Request {
        $this->internalReference = $internalReference;

        return $this;
    }

    /**
     * @param string $mInfo
     * @return Request
     */
    public function setMInfo(string $mInfo) : Request {
        $this->mInfo = $mInfo;

        return $this;
    }

    /**
     * @param string $orderIdentifier
     * @return Request
     */
    public function setOrderIdentifier(string $orderIdentifier) : Request {
        $this->adCustBorOrderId = str_replace(';', '-', $orderIdentifier);

        return $this;
    }

    /**
     * @param string $addendum
     * @return Request
     */
    public function setAddendum(string $addendum) : Request {
        $this->addendum = $addendum;

        return $this;
    }

    /**
     * @param string $originalTransactionType
     * @return Request
     */
    public function setOriginalTransactionType(string $originalTransactionType) : Request {
        $this->originalTransactionType = $originalTransactionType;

        return $this;
    }

    /**
     * @param string $language
     * @return Request
     */
    public function setLanguage(string $language) : Request {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount() : string {
        return number_format($this->amount, 2, '.', '');
    }

    /**
     * @return string
     */
    public function getOrder() : string {
        return str_pad(strval($this->order), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Render HTML form
     *
     * @return string
     */
    public function renderForm($borica) : string {
        $html = '<form action="' . $borica->getApiUrl() . '" method="POST" id="boricaForm">';

        foreach ($this->toPostData() as $key => $value) {
            $html .= '<input name="' . self::encode($key) . '" value="' . self::encode($value) . '" style="width: 100%;"><br>';
        }

        $html .= '<button type="submit">Send to Borica</button></form>';

        return $html;
    }

    /**
     * Encodes special characters into HTML entities
     *
     * @param string $text
     * @return string
     */
    private static function encode(string $text) : string {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * @return boolean
     */
    public function hasErrors() : bool {
        return $this->errors !== [];
    }

    /**
     * @return array
     */
    public function getErrors() : array {
        return $this->errors;
    }

    /**
     * @return void
     */
    public function clearErrors() : void {
        $this->errors = [];
    }

}
