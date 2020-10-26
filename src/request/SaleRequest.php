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
            'DESC' => $this->desc,
            'TERMINAL' => $this->terminal,
            'MERCH_NAME' => $this->merchantName, // TODO: Review when new official documentation. In EMV 3DS v2.2 is MERCHANT_NAME.
            'MERCH_URL' => $this->merchUrl,
            'MERCHANT' => $this->merchant,
            'TRTYPE' => $this->transactionType,
            'ORDER' => $this->getOrder(),
            'COUNTRY' => $this->country,
            'TIMESTAMP' => $this->timestamp,
            'MERCH_GMT' => $this->merchGmt,
            'NONCE' => $this->nonce,
            'P_SIGN' => $this->pSign,
            'BACKREF' => $this->backref, // TODO: Review when new official documentation. Not included in EMV 3DS v2.2 but required.
            // 'M_INFO' => $this->mInfo
        ];

        if ($this->email) {
            $postData['EMAIL'] = $this->email;
        }

        if ($this->adCustBorOrderId) {
            $postData['AD.CUST_BOR_ORDER_ID'] = $this->adCustBorOrderId;
            $postData['ADDENDUM'] = $this->addendum;
        }

        return $postData;
    }

}
