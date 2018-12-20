<?php
namespace Ssh2Transport\Authenticator;

use Ssh2Transport\Authenticator;
use Ssh2Transport\Exception;

class PubkeyFileAuthenticator extends Authenticator {
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
   private $pubkeyfile;
   /**
    *
    * @var string
    * @private
    */
   private $privkeyfile;
   /**
    *
    * @var string
    * @private
    */
   private $passphrase;
   protected function authenticateSession($session): void {
      $error = [];
      $error_handler = set_error_handler(function ($errno, string $errstr, $errfile, $errline) use (&$error) {
         $error[] = $errstr;
      });
      $error_reporting = error_reporting(error_reporting() & E_WARNING & E_NOTICE);
      $fingerprint = ssh2_auth_pubkey_file($session, $this->username,  $this->pubkeyfile, $this->privkeyfile, $this->passphrase );
      error_reporting($error_reporting);
      set_error_handler($error_handler);
      if (false === $fingerprint) {
         throw new Exception\AuthenticationErrorException(
            implode("; ", $error));
      }
   }
   /**
    * Authenticate using a public key
    *
    * @param string $username<p>
    *           </p>
    * @param string $pubkeyfile<p>
    *           </p>
    * @param string $privkeyfile<p>
    *           </p>
    * @param string $passphrase
    *           [optional] <p>
    *           If privkeyfile is encrypted (which it should
    *           be), the passphrase must be provided.
    *           </p>
    */
   public function __construct(string $username,string $pubkeyfile, string $privkeyfile, string $passphrase = null) {
      $this->username = $username;
      $this->pubkeyfile = $pubkeyfile;
      $this->privkeyfile = $privkeyfile;
      $this->passphrase = $passphrase;
   }
}