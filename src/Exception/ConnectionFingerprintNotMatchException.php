<?php
namespace Ssh2Transport\Exception;

use RuntimeException;

class ConnectionFingerprintNotMatchException extends RuntimeException implements 
   ConnectionExceptionInterface {
   private $knownHost;
   private $fingerprint;
   public function getKnownHost(): string {
      return $this->knownHost;
   }
   public function getFingerprint(): string {
      return $this->fingerprint;
   }
   public function __construct(string $known_host, string $fingerprint) {
      $this->knownHost = $known_host;
      $this->fingerprint = $fingerprint;
      parent::__construct("failed fingerprint verification; expected '$known_host', got instead: $fingerprint");
   }
}