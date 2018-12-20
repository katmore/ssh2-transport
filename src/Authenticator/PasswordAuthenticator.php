<?php
namespace Ssh2Transport\Authenticator;

use Ssh2Transport\Authenticator;
use Ssh2Transport\Exception;

class PasswordAuthenticator extends Authenticator {
   /**
    *
    * @var string
    * @private
    */
   private $username;
   /**
    *
    * @var string
    * @private
    */
   private $password;
   protected function authenticateSession($session): void {
      $error = [];
      $error_handler = set_error_handler(function ($errno, string $errstr, $errfile, $errline) use (&$error) {
         $error[] = $errstr;
      });
         $error_reporting = error_reporting(error_reporting() & E_WARNING & E_NOTICE);
         $fingerprint = ssh2_auth_password ($session, $this->username, $this->password);
         error_reporting($error_reporting);
         set_error_handler($error_handler);
         if (false === $fingerprint) {
            throw new Exception\AuthenticationErrorException(
               implode("; ", $error));
         }
   }
   public function __construct(string $username, string $password) {
      $this->username = $username;
      $this->password = $password;
   }
}