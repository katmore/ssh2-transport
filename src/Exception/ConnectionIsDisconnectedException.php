<?php
namespace Ssh2Transport\Exception;

use RuntimeException;

class ConnectionIsDisconnectedException extends RuntimeException implements 
   ConnectionExceptionInterface {
   public function getDisconnectMessage(): string {
      return $this->disconnectMessage;
   }
   private $disconnectMessage;
   public function __construct(string $disconnect_message, int $reason_code) {
      $this->disconnectMessage = $disconnect_message;
      parent::__construct(printf("Server disconnected with reason code [%d] and message: %s", $reason_code, $disconnect_message), $reason_code);
   }
}