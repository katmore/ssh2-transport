<?php
namespace Ssh2Transport;

class AuthenticatedConnection {
   public function __construct(VerifiedConnection $verified_connection,Authenticator $authenticator,$session) {
      $authenticator->authenticate($verified_connection,$session);
   }
}