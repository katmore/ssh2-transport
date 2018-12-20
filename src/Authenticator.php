<?php
namespace Ssh2Transport;

abstract class Authenticator {
   final public function authenticate(VerifiedConnection $verified_connection, $session) {
      $this->authenticateSession($session);
   }
   /**
    *
    * @private
    * @param resource $session
    *           ssh2 connection link identifier
    */
   abstract protected function authenticateSession($session): void;
}