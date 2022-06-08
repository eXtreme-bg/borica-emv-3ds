<?php

namespace BogdanKovachev\Borica\Request;

use BogdanKovachev\Borica\Borica;

require_once "Request.php";

/**
 * @author Bogdan Kovachev (https://1337.bg)
 */
class ReversalRequest extends Request {

    /**
     * Generate extended MAC and use it to sign sended data
     *
     * @param Borica $borica
     * @return ReversalRequest
     */
    public function sign(Borica $borica) : ReversalRequest {
        $mac = Borica::generateMacExtended($this->toPostData(), false);

        $this->setPSign($borica->signWithPrivateKey($mac));

        return $this;
    }

    /**
     * @return boolean
     */
    public function validate() : bool {
        $this->clearErrors();

        // Validate all mandatory properties
        foreach ([
            'terminal',
            'transactionType',
            'amount',
            'currency',
            'order',
            'description',
            'merchant',
            'merchantName',
            'addendum',
            'adCustBorOrderId',
            'retrievalReferenceNumber',
            'internalReference',
            'timestamp',
            'nonce',
            'pSign'
        ] as $property) {
            if ($this->$property === null || mb_strlen($this->$property) === 0) {
                $this->errors[$property][] = $property . ' is required.';
            }
        }

        // TODO: Validate optional properties `merchantUrl`, `email`, `country`, `merchantTimezone` and `language`

        // TODO: Add additional validators

        return !$this->hasErrors();
    }

    /**
     * Generate POST data array
     *
     * @return array
     */
    public function toPostData() : array {
        $postData = [
            'TERMINAL' => $this->terminal,
            'TRTYPE' => $this->transactionType,
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->currency,
            'ORDER' => $this->getOrder(),
            'DESC' => $this->description,
            'MERCHANT' => $this->merchant,
            'MERCH_NAME' => $this->merchantName,
            'ADDENDUM' => $this->addendum,
            'AD.CUST_BOR_ORDER_ID' => $this->adCustBorOrderId,
            'RRN' => $this->retrievalReferenceNumber,
            'INT_REF' => $this->internalReference,
            'TIMESTAMP' => $this->timestamp,
            'NONCE' => $this->nonce,
            'P_SIGN' => $this->pSign
        ];

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

        return $postData;
    }

    /**
     * @param Borica $borica
     * @return array
     */
    public function makeApiRequest(Borica $borica): array {
        $curl = curl_init();

        $postFields = http_build_query($this->toPostData());

        curl_setopt_array($curl, [
            CURLOPT_URL => $borica->getApiUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        return $response;
    }
}
