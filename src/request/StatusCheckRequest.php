<?php

namespace BogdanKovachev\Borica\Request;

use BogdanKovachev\Borica\Borica;

/**
 * @author Bogdan Kovachev (https://1337.bg)
 */
class StatusCheckRequest extends Request {

    /**
     * Generate extended MAC and use it to sign sended data
     *
     * @param Borica $borica
     * @return StatusCheckRequest
     */
    public function sign(Borica $borica): StatusCheckRequest {
        $mac = Borica::generateMac($this->toPostData(), false, $borica->signingAlgorithm);

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
            'nonce',
            'order',
            'originalTransactionType',
            'pSign',
            'terminal',
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
            'NONCE' => $this->nonce,
            'ORDER' => $this->getOrder(),
            'P_SIGN' => $this->pSign,
            'TERMINAL' => $this->terminal,
            'TRAN_TRTYPE' => $this->originalTransactionType,
            'TRTYPE' => $this->transactionType
        ];

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
