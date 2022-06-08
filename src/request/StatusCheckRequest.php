<?php

namespace BogdanKovachev\Borica\Request;

use BogdanKovachev\Borica\Borica;

require_once "Request.php";

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
    public function sign(Borica $borica) : StatusCheckRequest {
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
            'TERMINAL' => $this->terminal,
            'TRTYPE' => $this->transactionType,
            'ORDER' => $this->getOrder(),
            'NONCE' => $this->nonce,
            'TRAN_TRTYPE' => $this->originalTransactionType,
            'P_SIGN' => $this->pSign
        ];

        return $postData;
    }

}
