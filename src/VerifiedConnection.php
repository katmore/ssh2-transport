<?php
namespace Ssh2Transport;

class VerifiedConnection {
   
   /**
    *
    * @var int
    * @private
    */
   private $disconnectReason;
   /**
    *
    * @var string
    * @private
    */
   private $disconnectMessage;
   /**
    *
    * @var resource ssh2 connection link identifier
    * @private
    */
   private $session;
   
   /**
    *
    * @return resource ssh2 connection link identifier
    * @throws \Ssh2Transport\Exception\ConnectionIsDisconnectedException
    * @private
    */
   protected function getSession() {
      if ($this->disconnectReason !== null) {
         throw new Exception\ConnectionIsDisconnectedException(
            $this->disconnectMessage,
            $this->disconnectReason);
      }
   }
   
   public function __construct(Connection $connection,$session) {
      $this->session = $session;
      $connection->setDisconnectCallback(function ($reason, $message, $language) {
         $this->disconnectMessage = $message;
         $this->disconnectReason = $reason;
      });
   }
   
   public function authenticate(Authenticator $authenticator) : AuthenticatedConnection {
      
      return new AuthenticatedConnection($this,$authenticator,$this->getSession());
      
   }
}