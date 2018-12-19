<?php
namespace Ssh2Transport\Exception;

use RuntimeException;

class Ssh2ConnectionErrorException extends RuntimeException
implements Ssh2ExceptionInterface
{
   public function __construct(string $error) {
      parent::__construct($error);
   }
}