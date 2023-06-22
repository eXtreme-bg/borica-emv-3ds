<?php

namespace BogdanKovachev\Borica\Request;

use BogdanKovachev\Borica\Borica;

require_once "Request.php";

/**
 * @author Bogdan Kovachev (https://1337.bg)
 */
class SaleRequest extends Request {

    /**
     * Generate extended MAC and use it to sign sended data
     *
     * @param Borica $borica
     * @return SaleRequest
     */
    public function sign(Borica $borica): SaleRequest {
        $mac = Borica::generateMac($this->toPostData(), false);

        $this->setPSign($borica->signWithPrivateKey($mac));

        return $this;
    }

    /**
     * @return boolean
     */
    public function validate(): bool {
        $this->clearErrors();

        // Validate all mandatory properties
        foreach ([
            'adCustBorOrderId',
            'addendum',
            'amount',
            'currency',
            'description',
            'merchant',
            'merchantName',
            'nonce',
            'order',
            'pSign',
            'terminal',
            'timestamp',
            'transactionType'
        ] as $property) {
            if ($this->$property === null || mb_strlen($this->$property) === 0) {
                $this->errors[$property][] = $property . ' is required.';
            }
        }

        // TODO: Add additional validators

        return !$this->hasErrors();
    }

    /**
     * Generate POST data array
     *
     * @return array
     */
    public function toPostData(): array {
        $postData = [
            'AD.CUST_BOR_ORDER_ID' => $this->adCustBorOrderId,
            'ADDENDUM' => $this->addendum,
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->currency,
            'DESC' => $this->description,
            'MERCH_NAME' => $this->merchantName,
            'MERCHANT' => $this->merchant,
            'NONCE' => $this->nonce,
            'ORDER' => $this->getOrder(),
            'P_SIGN' => $this->pSign,
            'TERMINAL' => $this->terminal,
            'TIMESTAMP' => $this->timestamp,
            'TRTYPE' => $this->transactionType
        ];

        if ($this->country) {
            $postData['COUNTRY'] = $this->country;
        }

        if ($this->email) {
            $postData['EMAIL'] = $this->email;
        }

        if ($this->language) {
            $postData['LANG'] = $this->language;
        }

        if ($this->merchantTimezone) {
            $postData['MERCH_GMT'] = $this->merchantTimezone;
        }

        if ($this->merchantUrl) {
            $postData['MERCH_URL'] = $this->merchantUrl;
        }

        return $postData;
    }
}
