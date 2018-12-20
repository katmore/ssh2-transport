<?php
namespace Ssh2Transport\Authenticator;

use Ssh2Transport\Authenticator;
use Ssh2Transport\Exception;

class HostbasedFileAuthenticator extends Authenticator {
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
   private $hostname;
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
   /**
    *
    * @var string
    * @private
    */
   private $localUsername;
   protected function authenticateSession($session): void {
      $error = [];
      $error_handler = set_error_handler(function ($errno, string $errstr, $errfile, $errline) use (&$error) {
         $error[] = $errstr;
      });
      $error_reporting = error_reporting(error_reporting() & E_WARNING & E_NOTICE);
      $fingerprint = ssh2_auth_hostbased_file($session, $this->username, $this->hostname, $this->pubkeyfile, $this->privkeyfile, $this->passphrase , $this->localUsername );
      error_reporting($error_reporting);
      set_error_handler($error_handler);
      if (false === $fingerprint) {
         throw new Exception\AuthenticationErrorException(
            implode("; ", $error));
      }
   }
   /**
    * Authenticate using a public hostkey
    *
    * @param string $username<p>
    *           </p>
    * @param string $hostname<p>
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
    * @param string $local_username
    *           [optional] <p>
    *           If local_username is omitted, then the value
    *           for username will be used for it.
    *           </p>
    */
   public function __construct(string $username, string $hostname, string $pubkeyfile, string $privkeyfile, string $passphrase = null, string $local_username = null) {
      $this->username = $username;
      $this->hostname = $hostname;
      $this->pubkeyfile = $pubkeyfile;
      $this->privkeyfile = $privkeyfile;
      $this->passphrase = $passphrase;
      $this->localUsername = $local_username;
   }
}