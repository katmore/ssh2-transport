# Ssh2Transport
php ssh2 wrapper lib

## Usage
This lib is designed to force safe and sane ssh connection use. 

You cannot play with your ssh2 connection resource until have successfully    


```php
use Ssh2Transport\Connection;
use Ssh2Transport\Exception\ConnectionFingerprintNotMatchException;

$connection = new Connection('example.com');

//
// be safe: verify remote server fingerprint
//   (the library is designed to "force" this step)
// 
try {
  $verifiedConnection = $connection->verify('6F89C2F0A719B30CC38ABDF90755F2E4');
} catch (ConnectionFingerprintNotMatchException $e) {
  echo "got an unexpected fingerprint: ".$e->getFingerprint();
  throw $e;
}

//
// one more step: need to authenticate
//
use Ssh2Transport\Authenticator\PasswordAuthenticator;
$authenticatedConnection = $verifiedConnection->authenticate(new PasswordAuthenticator('my-username','my-password'));

//
// finally, you may safely enjoy the authenticated ssh connection with native ssh_* functions as desired
//
$session = $authenticatedConnection->getSession();
ssh2_exec($session,"some command");
```



## Legal
"Ssh2Transport" is distributed under the terms of the [MIT license](LICENSE) or the [GPLv3](GPLv3) license.

Copyright (c) 2018 Doug Bird. All rights reserved.
