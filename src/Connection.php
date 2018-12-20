<?php
namespace Ssh2Transport;

class Connection {

   /**
    *
    * @var callable
    * @private
    */
   private $disconnectCallback;
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
    * Connect to an SSH server
    *
    * @param string $host
    *           <p>
    *           </p>
    *           
    * @param int $port
    *           [optional] <p>
    *           </p>
    * @param array $methods
    *           [optional] <p>
    *           methods may be an associative array with up to four parameters
    *           as described below.
    *           </p>
    *           <p>
    *           <table>
    *           methods may be an associative array
    *           with any or all of the following parameters.
    *           <tr valign="top">
    *           <td>Index</td>
    *           <td>Meaning</td>
    *           <td>Supported Values*</td>
    *           </tr>
    *           <tr valign="top">
    *           <td>kex</td>
    *           <td>
    *           List of key exchange methods to advertise, comma separated
    *           in order of preference.
    *           </td>
    *           <td>
    *           diffie-hellman-group1-sha1,
    *           diffie-hellman-group14-sha1, and
    *           diffie-hellman-group-exchange-sha1
    *           </td>
    *           </tr>
    *           <tr valign="top">
    *           <td>hostkey</td>
    *           <td>
    *           List of hostkey methods to advertise, comma separated
    *           in order of preference.
    *           </td>
    *           <td>
    *           ssh-rsa and
    *           ssh-dss
    *           </td>
    *           </tr>
    *           <tr valign="top">
    *           <td>client_to_server</td>
    *           <td>
    *           Associative array containing crypt, compression, and
    *           message authentication code (MAC) method preferences
    *           for messages sent from client to server.
    *           </td>
    *           </tr>
    *           <tr valign="top">
    *           <td>server_to_client</td>
    *           <td>
    *           Associative array containing crypt, compression, and
    *           message authentication code (MAC) method preferences
    *           for messages sent from server to client.
    *           </td>
    *           </tr>
    *           </table>
    *           </p>
    *           <p>
    *           * - Supported Values are dependent on methods supported by underlying library.
    *           See libssh2 documentation for additional
    *           information.
    *           </p>
    *           <p>
    *           <table>
    *           client_to_server and
    *           server_to_client may be an associative array
    *           with any or all of the following parameters.
    *           <tr valign="top">
    *           <td>Index</td>
    *           <td>Meaning</td>
    *           <td>Supported Values*</td>
    *           </tr>
    *           <tr valign="top">
    *           <td>crypt</td>
    *           <td>List of crypto methods to advertise, comma separated
    *           in order of preference.</td>
    *           <td>
    *           rijndael-cbc@lysator.liu.se,
    *           aes256-cbc,
    *           aes192-cbc,
    *           aes128-cbc,
    *           3des-cbc,
    *           blowfish-cbc,
    *           cast128-cbc,
    *           arcfour, and
    *           none**
    *           </td>
    *           </tr>
    *           <tr valign="top">
    *           <td>comp</td>
    *           <td>List of compression methods to advertise, comma separated
    *           in order of preference.</td>
    *           <td>
    *           zlib and
    *           none
    *           </td>
    *           </tr>
    *           <tr valign="top">
    *           <td>mac</td>
    *           <td>List of MAC methods to advertise, comma separated
    *           in order of preference.</td>
    *           <td>
    *           hmac-sha1,
    *           hmac-sha1-96,
    *           hmac-ripemd160,
    *           hmac-ripemd160@openssh.com, and
    *           none**
    *           </td>
    *           </tr>
    *           </table>
    *           </p>
    *           <p>
    *           Crypt and MAC method "none"
    *           <p>
    *           For security reasons, none is disabled by the underlying
    *           libssh2 library unless explicitly enabled
    *           during build time by using the appropriate ./configure options. See documentation
    *           for the underlying library for more information.
    *           </p>
    *           </p>
    *           
    * @link http://www.php.net/manual/en/function.ssh2-connect.php
    *      
    * @throws \Ssh2Transport\Exception\ConnectionConnectErrorException
    */
   final public function __construct(string $host, int $port = 22, array $methods = null) {
      $error = [];
      $error_handler = set_error_handler(function ($errno, string $errstr, $errfile, $errline) use (&$error) {
         $error[] = $errstr;
      });
      $error_reporting = error_reporting(error_reporting() & E_WARNING & E_NOTICE);
      $this->session = ssh2_connect($host, $port, $methods,
         [
            'disconnect' => function ($reason, $message, $language) {
               $this->disconnectMessage = $message;
               $this->disconnectReason = $reason;
               if (is_callable($this->disconnectCallback)) {
                  $this->disconnectCallback($reason, $message, $language);
               }
            }
         ]);
      error_reporting($error_reporting);
      set_error_handler($error_handler);
      if (false === $this->session) {
         throw new Exception\ConnectionConnectErrorException(
            implode("; ", $error));
      }
   }


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

   /**
    *
    * @param callable $callback
    *           invoked when <b>SSH2_MSG_DISCONNECT</b> packet is received
    */
   public function setDisconnectCallback(callable $callback): void {
      $this->disconnectCallback = $callback;
   }
   /**
    *
    * @param int $flags
    *           [optional] <p>
    *           flags may be either of
    *           SSH2_FINGERPRINT_MD5 or
    *           SSH2_FINGERPRINT_SHA1 logically ORed with
    *           SSH2_FINGERPRINT_HEX or
    *           SSH2_FINGERPRINT_RAW.
    *           </p>
    * @return string the hostkey hash as a string.
    * @throws \Ssh2Transport\Exception\ConnectionFingerprintErrorException
    */
   public function getFingerprint(int $flags = null): string {
      $error = [];
      $error_handler = set_error_handler(function ($errno, string $errstr, $errfile, $errline) use (&$error) {
         $error[] = $errstr;
      });
      $error_reporting = error_reporting(error_reporting() & E_WARNING & E_NOTICE);
      $fingerprint = ssh2_fingerprint($this->getSession());
      error_reporting($error_reporting);
      set_error_handler($error_handler);
      if (false === $fingerprint) {
         throw new Exception\ConnectionFingerprintErrorException(
            implode("; ", $error));
      }
      return $fingerprint;
   }

   /**
    * Verify fingerprint of remote server against a known host.
    *
    * @param string $known_host
    *           expected fingerprint of the known host
    * @param int $flags
    *           [optional] <p>
    *           flags may be either of
    *           SSH2_FINGERPRINT_MD5 or
    *           SSH2_FINGERPRINT_SHA1 logically ORed with
    *           SSH2_FINGERPRINT_HEX or
    *           SSH2_FINGERPRINT_RAW.
    *           </p>
    * @return \Ssh2Transport\VerifiedConnection verified connection object
    * @throws \Ssh2Transport\Exception\ConnectionFingerprintErrorException
    * @throws \Ssh2Transport\Exception\ConnectionFingerprintNotMatchException
    */
   public function verify(string $known_host, int $flags = null): VerifiedConnection {
      if ($known_host !== ($fingerprint = $this->getFingerprint($flags))) {
         throw new Exception\ConnectionFingerprintNotMatchException(
            $known_host,
            $fingerprint);
      }
   }
}