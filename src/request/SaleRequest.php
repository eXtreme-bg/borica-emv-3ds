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
    public function sign(Borica $borica) : SaleRequest {
        $mac = Borica::generateMacExtended($this->toPostData(), false);

        $this->setPSign($borica->signWithPrivateKey($mac));

        return $this;
    }

    /**
     * Generate POST data array
     *
     * @return array
     */
    public function toPostData() : array {
        $postData = [
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->currency,
            'TERMINAL' => $this->terminal,
            'MERCHANT' => $this->merchant,
            'TRTYPE' => $this->transactionType,
            'ORDER' => $this->getOrder(),
            'TIMESTAMP' => $this->timestamp,
            'NONCE' => $this->nonce,
            'P_SIGN' => $this->pSign
        ];

        if ($this->description) {
            $postData['DESC'] = $this->description;
        }

        if ($this->merchantName) {
            $postData['MERCH_NAME'] = $this->merchantName;
        }

        if ($this->merchantUrl) {
            $postData['MERCH_URL'] = $this->merchantUrl;
        }

        if ($this->email) {
            $postData['EMAIL'] = $this->email;
        }

        if ($this->country) {
            $postData['COUNTRY'] = $this->country;
        }

        if ($this->merchantTimezone) {
            $postData['MERCH_GMT'] = $this->merchantTimezone;
        }

        if ($this->language) {
            $postData['LANG'] = $this->language;
        }

        if ($this->backref) {
            $postData['BACKREF'] = $this->backref;
        }

        if ($this->adCustBorOrderId) {
            $postData['AD.CUST_BOR_ORDER_ID'] = $this->adCustBorOrderId;
            $postData['ADDENDUM'] = $this->addendum;
        }

        return $postData;
    }

}
