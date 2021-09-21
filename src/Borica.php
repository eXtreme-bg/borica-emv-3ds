<?php

namespace BogdanKovachev\Borica;

use BogdanKovachev\Borica\TransactionType;
use BogdanKovachev\Borica\Request\Request;
use BogdanKovachev\Borica\Response\Response;

require_once 'TransactionType.php';
require_once 'request/Request.php';
require_once 'response/Response.php';

/**
 * @author Bogdan Kovachev (https://1337.bg)
 */
class Borica {

    /**
     * API URL (Development)
     */
    const API_URL_DEVELOPMENT = 'https://3dsgate-dev.borica.bg/cgi-bin/cgi_link';

    /**
     * API URL (Production)
     */
    const API_URL_PRODUCTION = 'https://3dsgate.borica.bg/cgi-bin/cgi_link';

    /**
     * @var boolean
     */
    public $sandboxMode = false;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    public $privateKey;

    /**
     * @var string
     */
    public $privateKeyPassword;

    /**
     * @var string
     */
    public $certificate;

    /**
     * @param boolean $sandbox Sandbox mode
     * @return Borica
     */
    public function setSandboxMode(bool $sandbox) : Borica {
        $this->sandboxMode = $sandbox;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiUrl() : string {
        return $this->sandboxMode ? self::API_URL_DEVELOPMENT : self::API_URL_PRODUCTION;
    }

    /**
     * @param string $filePath Absolute file path to private key (e.g. /home/username/public_html/certificates/borica.pem)
     * @return Borica
     */
    public function setPrivateKey(string $filePath) : Borica {
        $this->privateKey = 'file://' . $filePath;

        return $this;
    }

    /**
     * @param string $password Private key password
     * @return Borica
     */
    public function setPrivateKeyPassword(string $password) : Borica {
        $this->privateKeyPassword = $password;

        return $this;
    }

    /**
     * @param string $filePath Absolute file path to certificate (e.g. /home/username/public_html/certificates/borica.cer)
     * @return Borica
     */
    public function setCertificate(string $filePath) : Borica {
        $this->certificate = 'file://' . $filePath;

        return $this;
    }

    /**
     * Generate message authentication code (MAC) for signing
     *
     * @param array $data
     * @param boolean $isResponse
     * @return string
     */
    public static function generateMac(array $data, bool $isResponse) : string {
        $macFields = $isResponse ? Response::MAC_FIELDS : Request::MAC_FIELDS;

        $message = '';

        foreach ($macFields[$data['TRTYPE']] as $field) {
            $message .= mb_strlen($data[$field]) . $data[$field];
        }

        return $message;
    }

    /**
     * Generate extended message authentication code (MAC) for signing
     *
     * @param array $data
     * @param boolean $isResponse
     * @return string
     */
    public static function generateMacExtended(array $data, bool $isResponse) : string {
        $macFields = $isResponse ? Response::MAC_EXTENDED_FIELDS : Request::MAC_EXTENDED_FIELDS;

        $message = '';

        foreach ($macFields[$data['TRTYPE']] as $field) {
            $value = $data[$field];

            // When field in response is missing, use symbol `-`
            if ($isResponse && mb_strlen($value) == 0) {
                $message .= '-';
            } else {
                $message .= mb_strlen($value) . $value;
            }
        }

        return $message;
    }

    /**
     * Sign data using private key
     *
     * @param string $data
     * @return string
     */
    function signWithPrivateKey(string $data) : string {
        // Get a private key
        $privateKey = openssl_pkey_get_private($this->privateKey, $this->privateKeyPassword);
        if (!$privateKey) {
            // TODO: Perform someting
        }

        // Generate signature
        if (!openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            // TODO: Perform someting
        }

        // PHP 8 automatically frees the key instance and deprecates the function
        if (PHP_VERSION_ID < 80000) {
            openssl_free_key($privateKey);
        }

        return strtoupper(bin2hex($signature));
    }

    /**
     * Verify signed data using public key
     *
     * @param string $data
     * @param string $signature
     * @return boolean
     */
    public function verifySignature(string $data, string $signature) : bool {
        // Get a public key
        $publicKey = openssl_pkey_get_public($this->certificate);
        if (!$publicKey) {
            // TODO: Perform someting
        }

        // Verify signature
        $result = openssl_verify($data, hex2bin($signature), $publicKey, OPENSSL_ALGO_SHA256);

        // PHP 8 automatically frees the key instance and deprecates the function
        if (PHP_VERSION_ID < 80000) {
            openssl_free_key($publicKey);
        }

        if ($result == 0) {
            // TODO: Log the error: openssl_error_string()

            return false;
        }

        return true;
    }

}
