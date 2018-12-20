<?php
namespace Ssh2Transport\Authenticator;

use Ssh2Transport\Authenticator;
use Ssh2Transport\Exception;

class AgentAuthenticator extends Authenticator {
   /**
    *
    * @var string
    * @private
    */
   private $username;
   protected function authenticateSession($session): void {
      $error = [];
      $error_handler = set_error_handler(function ($errno, string $errstr, $errfile, $errline) use (&$error) {
         $error[] = $errstr;
      });
      $error_reporting = error_reporting(error_reporting() & E_WARNING & E_NOTICE);
      $fingerprint = ssh2_auth_agent($session, $this->username);
      error_reporting($error_reporting);
      set_error_handler($error_handler);
      if (false === $fingerprint) {
         throw new Exception\AuthenticationErrorException(
            implode("; ", $error));
      }
   }
   public function __construct(string $username) {
      $this->username = $username;
   }
}