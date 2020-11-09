<?php

namespace BogdanKovachev\Borica\Response;

use BogdanKovachev\Borica\Borica;
use BogdanKovachev\Borica\TransactionType;

/**
 * @author Bogdan Kovachev (https://1337.bg)
 */
class Response {

    /**
     * Fields used for generating message authentication code (MAC)
     */
    const MAC_FIELDS = [
        TransactionType::SALE => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'TIMESTAMP'
        ],
        TransactionType::DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'ORDER',
            'TIMESTAMP'
        ],
        TransactionType::COMPLETE_DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'ORDER',
            'TIMESTAMP'
        ],
        TransactionType::REVERSE_DEFERRED_AUTHORIZATION => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'ORDER',
            'TIMESTAMP'
        ],
        TransactionType::REVERSAL => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'ORDER',
            'TIMESTAMP'
        ],
        TransactionType::STATUS_CHECK => [
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'TIMESTAMP'
        ]
    ];

    /**
     * Fields used for generating extended message authentication code (MAC)
     */
    const MAC_EXTENDED_FIELDS = [
        TransactionType::SALE => [
            'ACTION',
            'RC',
            'APPROVAL',
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'RRN',
            'INT_REF',
            'PARES_STATUS',
            'ECI',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::DEFERRED_AUTHORIZATION => [
            'ACTION',
            'RC',
            'APPROVAL',
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'RRN',
            'INT_REF',
            'PARES_STATUS',
            'ECI',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::COMPLETE_DEFERRED_AUTHORIZATION => [
            'ACTION',
            'RC',
            'APPROVAL',
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'RRN',
            'INT_REF',
            'PARES_STATUS',
            'ECI',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::REVERSE_DEFERRED_AUTHORIZATION => [
            'ACTION',
            'RC',
            'APPROVAL',
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'RRN',
            'INT_REF',
            'PARES_STATUS',
            'ECI',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::REVERSAL => [
            'ACTION',
            'RC',
            'APPROVAL',
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'RRN',
            'INT_REF',
            'PARES_STATUS',
            'ECI',
            'TIMESTAMP',
            'NONCE'
        ],
        TransactionType::STATUS_CHECK => [
            'ACTION',
            'RC',
            'APPROVAL',
            'TERMINAL',
            'TRTYPE',
            'AMOUNT',
            'CURRENCY',
            'ORDER',
            'RRN',
            'INT_REF',
            'PARES_STATUS',
            'ECI',
            'TIMESTAMP',
            'NONCE'
        ]
    ];

    /**
     * Response codes
     */
    const RESPONSE_CODES = [
        '00' => 'Successfully completed',
        '01' => 'Refer to card issuer',
        '04' => 'PICK UP',
        '05' => 'Do not Honour',
        '06' => 'Error',
        '12' => 'Invalid transaction',
        '13' => 'Invalid amount',
        '14' => 'No such card',
        '15' => 'No such issuer',
        '17' => 'Customer cancellation 30 Format error',
        '35' => 'Pick-up, card acceptor contact acquirer',
        '36' => 'Pick up, card restricted',
        '37' => 'Pick up, call acquirer security',
        '38' => 'Pick up, Allowable PIN tries exceeded',
        '39' => 'No credit account',
        '40' => 'Requested function not supported',
        '41' => 'Pick up, lost card',
        '42' => 'No universal account',
        '43' => 'Pick up, stolen card',
        '54' => 'Expired card / target',
        '55' => 'Incorrect PIN',
        '56' => 'No card record',
        '57' => 'Transaction not permitted to cardholder',
        '58' => 'Transaction not permitted to terminal',
        '59' => 'Suspected fraud',
        '85' => 'No reason to decline',
        '88' => 'Cryptographic failure',
        '89' => 'Authentication failure',
        '91' => 'Issuer or switch is inoperative',
        '95' => 'Reconcile error / Auth Not found',
        '96' => 'System Malfunction'
    ];

    /**
     * Response error codes
     */
    const RESPONSE_ERROR_CODES = [
        -1 => 'В заявката не е попълнено задължително поле',
        -2 => 'Заявката съдържа поле с некоректно име',
        -3 => 'Aвторизационният хост не отговаря или форматът на отговора е неправилен',
        -4 => 'Няма връзка с авторизационния хост',
        -9 => 'Грешна дата на валидност на картата',
        -11 => 'Грешка в поле "Валута" в заявката',
        -12 => 'Грешка в "Идентификатор на търговец"',
        -15 => 'Грешка в поле "RRN" в заявката',
        -17 => 'Отказан достъп до платежния сървър ( напр. грешка при проверка на P_SIGN)',
        -19 => 'Грешка в искането за автентикация или неуспешна автентикация',
        -20 => 'Разрешената разлика между времето на сървъра на търговеца и e-Gateway сървъра е надвишена',
        -21 => 'Трансакцията вече е била изпълнена',
        -24 => 'Заявката съдържа стойности за полета, които не могат да бъдат обработени. Например валутата е различна от валутата на терминала.',
        -25 => 'Трансакцията е отказана (напр. от картодържателя)',
        -27 => 'Неправилно име на търговеца',
        -32 => 'Дублирана отказана трансакция'
    ];

    /**
     * Описание: Терминал
     * Размер: 8
     * Съдържание: Ехо от заявката
     *
     * @var string
     */
    public $terminal;

    /**
     * Описание: Тип на трансакция
     * Размер: 1-2
     * Съдържание: Ехо от заявката
     *
     * @var integer
     */
    public $transactionType;

    /**
     * Описание: Поръчка
     * Размер: 6
     * Съдържание: Ехо от заявката
     *
     * @var string
     */
    public $order;

    /**
     * Описание: Сума
     * Размер: 12
     * Съдържание: Сума на поръчката
     *
     * @var float
     */
    public $amount;

    /**
     * Описание: Валута
     * Размер: 3
     * Съдържание: Ехо от заявката
     *
     * @var string
     */
    public $currency;

    /**
     * Описание: Действие
     * Размер: 1
     * Съдържание: E-Gateway код на действие: 0 – успешно приключена трансакция; 1 – дублирана трансакция; 2 – отказана трансакция; 3 – грешка при обработка на трансакцията
     *
     * @var integer
     */
    public $action;

    /**
     * Описание: Код на завършване
     * Размер: 2
     * Съдържание: Отговор при обработка на трансакция (ISO-8583, поле 39)
     *
     * @var string
     */
    public $responseCode;

    /**
     * Описание: Одобрение
     * Размер: 6
     * Съдържание: Код за одобрение (ISO- 8583, поле 38). Може да бъде празно, ако не е подадено от издателя на картата.
     *
     * @var string
     */
    public $approval;

    /**
     * Описание: Референция на трансакцията
     * Размер: 12
     * Съдържание: Референция на трансакцията (ISO-8583 - 1987, поле 37).
     *
     * @var string
     */
    public $rrn; // TODO: Rename to `retrievalReferenceNumber`

    /**
     * Описание: Вътрешна референция
     * Размер: 16
     * Съдържание: Вътрешна референция за е-Commerce gateway
     *
     * @var string
     */
    public $internalReference;

    /**
     * Описание: Тип на оригинална трансакция
     * Размер: 1-2
     * Съдържание: Тип на оригинална трансакцияв отговор на „Проверка на статус“
     *
     * @var integer
     */
    public $originalTranscationType;

    /**
     * Описание: Текстово описание на код на завършване
     * Размер: 1-255
     * Съдържание: Текстово описание на код на завършване
     *
     * @var string
     */
    public $statusMessage;

    /**
     * Описание: Маскиран номер карта
     * Размер: 16-19
     * Съдържание: Маскиран номер карта в в отговор на „Проверка на статус“ (напр. „5100XXXXXXXX0022“)
     *
     * @var string
     */
    public $cardNumber;

    /**
     * Описание: Дата/час
     * Размер: 19
     * Съдържание: Дата/час на оригиналната трансакция: YYYYMMDDHHMMSS
     *
     * @var string
     */
    public $originalTransactionDate;

    /**
     * Описание: Дата/час
     * Размер: 14
     * Съдържание: Дата/час на трансакцията по GMT (UTC): YYYYMMDDHHMMSS
     *
     * @var string
     */
    public $timestamp;

    /**
     * Описание: Статус на автентикация
     * Размер: 1
     * Съдържание: Статус на автентикация, използван в схемата 3-D Secure
     *
     * @var string // TODO: Change type?
     */
    public $paresStatus;

    /**
     * Размер: 2
     * Съдържание: E-commerce индикатор (ECI)
     *
     * @var string
     */
    public $eci; // TODO: Rename to eCommerceIdentifier

    /**
     * Размер: 32
     * Съдържание: Съдържа 16 непредсказуеми случайни байтове, представени в шестнадесетичен формат. Може да съдържа главни латински букви A..Z и цифри 0..9.
     *
     * @var string
     */
    public $nonce;

    /**
     * Описание:: Подпис
     * Размер: 512
     * Съдържание: Код за автентициране на съобщението от APGW. Съдържа 256 байтав шестнадесетичен формат. Може да съдържа главни латински букви A..Z и цифри 0..9.
     *
     * @var string
     */
    public $pSign;

    /**
     * @var array
     */
    public $postData = [];

    /**
     * @var boolean
     */
    public $signatureIsVerified = false;

    /**
     * @param array $postData
     * @return Response|null
     */
    public static function withPost(array $postData) : Response {
        $instance = new self();

        $instance->transactionType = intval($postData['TRTYPE']);
        $instance->postData = $postData;

        if ($instance->transactionType == TransactionType::SALE) {
            $instance->terminal = $postData['TERMINAL'];
            $instance->transactionType = $postData['TRTYPE'];
            $instance->order = $postData['ORDER'];
            $instance->amount = floatval($postData['AMOUNT']);
            $instance->currency = $postData['CURRENCY'];
            $instance->action = intval($postData['ACTION']);
            $instance->responseCode = $postData['RC'];
            $instance->approval = $postData['APPROVAL'];
            $instance->rrn = $postData['RRN'];
            $instance->internalReference = $postData['INT_REF'];
            $instance->statusMessage = $postData['STATUSMSG'];
            $instance->cardNumber = $postData['CARD'];
            $instance->originalTransactionDate = $postData['TRAN_DATE'];
            $instance->timestamp = $postData['TIMESTAMP'];
            $instance->paresStatus = $postData['PARES_STATUS'];
            $instance->eci = $postData['ECI'];
            $instance->nonce = $postData['NONCE'];
            $instance->pSign = $postData['P_SIGN'];
            // TODO: Review `TRAN_TRTYPE` and `LANG` when new official documentation.
        } elseif ($instance->transactionType == TransactionType::DEFERRED_AUTHORIZATION) {

        } elseif ($instance->transactionType == TransactionType::COMPLETE_DEFERRED_AUTHORIZATION) {

        } elseif ($instance->transactionType == TransactionType::REVERSE_DEFERRED_AUTHORIZATION) {

        } elseif ($instance->transactionType == TransactionType::REVERSAL) {

        } elseif ($instance->transactionType == TransactionType::STATUS_CHECK) {

        } else {
            return null;
        }

        return $instance;
    }

    /**
     * Request was successful
     *
     * @return boolean
     */
    public function isSuccessful() : bool {
        return $this->responseCode == '00';
    }

    /**
     * @param Borica $borica
     * @return Response
     */
    public function verify(Borica $borica) : Response {
        $mac = Borica::generateMacExtended($this->postData, true);

        $this->signatureIsVerified = $borica->verifySignature($mac, $this->pSign);

        return $this;
    }

}
