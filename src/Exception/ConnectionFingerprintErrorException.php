<?php
namespace Ssh2Transport\Exception;

use RuntimeException;

class ConnectionFingerprintErrorException extends RuntimeException implements 
   ConnectionExceptionInterface {
   public function __construct(string $error) {
      parent::__construct($error);
   }
}