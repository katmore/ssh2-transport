<?php
namespace Ssh2Transport\Exception;

use RuntimeException;

class AuthenticationErrorException extends RuntimeException implements 
AuthenticationExceptionInterface {
   public function __construct(string $error) {
      parent::__construct($error);
   }
}