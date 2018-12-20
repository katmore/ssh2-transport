# Ssh2Transport
php ssh2 wrapper lib

## Usage

### Establish and Verify an ssh connection

```php
use Ssh2Transport\Connection;

$connection = new Connection('example.com');
$verifiedConnection = $connection->verify('6F89C2F0A719B30CC38ABDF90755F2E4');
//
// enjoy the verified ssh connection
//
```

### Authenticate an ssh connection

```php
use Ssh2Transport\Authenticator;
$authenticatedConnection = $verifiedConnection->authenticate(new Authenticator\PasswordAuthenticator('my-username','my-password'));
//
// enjoy the authenticated ssh connection
//
```



## Legal
"Ssh2Transport" is distributed under the terms of the [MIT license](LICENSE) or the [GPLv3](GPLv3) license.

Copyright (c) 2018 Doug Bird. All rights reserved.
