<?php

namespace Jamesstandbridge\PhpSmsPartner\Core;

use Exception;
use Throwable;

/**
 * Define a custom exception class
 */
class SMSPartnerApiException extends Exception
{
    private int $statusCode;

    // Redefine the exception so message isn't optional
    public function __construct($content, $code = 0, Throwable $previous = null) {
        $content = json_decode($content, true);
        $this->statusCode = $content["code"];

        // make sure everything is assigned properly
        parent::__construct(isset($content["message"]) ? $content["message"] : "SMSPartner couldn't process request", $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->statusCode}]: {$this->message}\n";
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}