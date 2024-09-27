<?php declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use OpenPGP\OpenPGP;

$passphase = "{8P@|gFI8YJgnf>dl:4B21BJ|8[]d|SK";

$keyData = <<<EOT
-----BEGIN PGP PRIVATE KEY BLOCK-----
Version: PHP Privacy v1
Comment: https://github.com/web-of-trust/php-privacy

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
=0gnP
-----END PGP PRIVATE KEY BLOCK-----
EOT;
$rsaPrivateKey = OpenPGP::decryptPrivateKey($keyData, $passphase);

$keyData = <<<EOT
-----BEGIN PGP PRIVATE KEY BLOCK-----
Version: PHP Privacy v1
Comment: https://github.com/web-of-trust/php-privacy

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
aCx35WNEMRaLjyALpTs=
=WXAi
-----END PGP PRIVATE KEY BLOCK-----
EOT;
$dsaPrivateKey = OpenPGP::decryptPrivateKey($keyData, $passphase);

$keyData = <<<EOT
-----BEGIN PGP PRIVATE KEY BLOCK-----
Version: PHP Privacy v1
Comment: https://github.com/web-of-trust/php-privacy

xcBIBGb2E+YTBSuBBAAjBCMEARQIJE/Dxo80pwP7O+C/WhIfrwAN6gcCS3vgnRKW234NSbFWRkq1
VKP0Un7+1hcLtbvR3VQ8BtNIub4hPvAXmS7wAHt+SBI5KowIQMJR7kowpKRMg+qdTa56ALputpLv
4zBcStVyYMzmiXBKv7NFn9sPqUXysfTdkQfTKaHvWLfsQBj//gcDCPftcmzK+2m/4CRz9XsmVAPd
yD+zUg6OQLi9tOFdqa0mJJU3OuhvZa4+3bdL3u51s011ZWgbJx+UzKS8FejbEuxjr8A7frk1qTgX
j1wwTyrx9BIfRIglRYAgeUVsEu0wWyWF763EjauKfgWpeFuqFZ00zSpOZ3V5ZW4gVmFuIE5ndXll
biA8bmd1eWVubnYxOTgxQGdtYWlsLmNvbT7CwEoEEBMKAHkFAmb2E+cWIQRAm3G+KT3komwXw3Tu
AGlwXgeMSgkQ7gBpcF4HjEo5FAAAAAAAEAAgc2FsdEBvcGVucGdwLm9yZwIWRlxmalvIin4hU2sm
D9Lascpq06ucbTkfLytIArJxAhsDBAsHCAkDFQgKBRYAAQIDAh4DAhkBAAAa/QIIoYslK11gyUOt
29BtIfgQGkO3vTNdYFToLcNradfZ0LB7ytDouEYRl3Z3KRpeT3n8QCT+CoSotlqzZFk3xs1caEMC
CQHqy+p7kcsmJIHV0lOCR7WrJdDGC+gSe7+Uw9A2Z34xABNIjO2kD/xADfj3EvmLV9yJ+y9vK625
ByUDCuvB6Ubvx80sTmd1eWVuIFZhbiBOZ3V5ZW4gPG5ndXllbm52QGl3YXl2aWV0bmFtLmNvbT7C
wEgEEBMKAHYFAmb2E+cWIQRAm3G+KT3komwXw3TuAGlwXgeMSgkQ7gBpcF4HjEo5FAAAAAAAEAAg
c2FsdEBvcGVucGdwLm9yZ58BX0MP95gUtl3tC1zTQHP15J2K3NZ+9ytLJod5cdK5AhsDBAsHCAkD
FQgKBRYAAQIDAh4DAABUmgIJAddoI68t8Xg51gQ5yzPmBEA3z+SAiFOUPL7e0OEKIb51kQpIGFn0
uFkgxE9h7OdtuCNFGL4fG5+/jigg1Xj+t6mjAgkBy2+Dt0yL3QD+FuOfk5T+8F2atsH/evr+rZeR
L2XjZhvoFEhnzJrRO99YT7sA6JLSTD8pIs6B4+fF8qdhwehP1EPHwEwEZvYT5xIFK4EEACMEIwQB
5/zW49tDXRwVs8MTZcxRH6IEenm3WkphCz4Q4yCttm1tY9SVzTDMT3/n9PqFuHcyBX397zfCLdW0
jLK1ledDFh8AxjHCrgC6vh+iBLrJCYMiuSof89xl5VhPAGlZaXiTmHVQsNnXY9+o2R/DwrMpiZ8F
LJ9yFIqpRTsfZrB0TZ+V1+8DAAoJ/gcDCMiDrpsdzk454KFF1HYvs+/DGHtdtv0q4IXmqKtZmF1S
SzK1H9RlRqY9QjWMJ8iGvMRzgF+qyx8Zi8rukycR2A7NVFZjmmqG7+kJwF+eUp5i1FhumlrV9aio
mL5D+mk1aOePCckhWzYxbDDPRy0LlpVzwsA0BBgTCgBkBQJm9hPnFiEEQJtxvik95KJsF8N07gBp
cF4HjEoJEO4AaXBeB4xKORQAAAAAABAAIHNhbHRAb3BlbnBncC5vcmdYrkkcJV/rNkpWHIMrIdnI
WshO6IXzFcQzmlXBJu8blwIbDAAABdUCCKhJiX5fS9eobqxIB9xyTMxbs9/YjXqxg/Rh8nAJMrPW
cXrJizQ9R8jZeigJTAdLCwfOrmiife5ln7q9emEfAu5wAgdOSuFWARFlnrBD6pFt1uNX/eDXKoMi
MPwMofGczg4pIeF6Zf1UKjsLSz2wQsA08tItrl6J/gDNw2FrKyTS2verFg==
=SjkS
-----END PGP PRIVATE KEY BLOCK-----
EOT;
$ecDsaPrivateKey = OpenPGP::decryptPrivateKey($keyData, $passphase);

$keyData = <<<EOT
-----BEGIN PGP PRIVATE KEY BLOCK-----
Version: PHP Privacy v1
Comment: https://github.com/web-of-trust/php-privacy

xYYEZvYT5xYJKwYBBAHaRw8BAQdAz6EScc8aKtK99coL8lAfh34G+XvDiowueuBHNU0JhrT+BwMI
khz8n+/HWZngWO+JsbvxNhniwR/nsIuV9wzTL8oxFs5ryEFE4HS0BFCLKhnsjknTv2nwEzVN7N6E
VHCSvjwarcKGQKlNOLl0yY04QkxH380qTmd1eWVuIFZhbiBOZ3V5ZW4gPG5ndXllbm52MTk4MUBn
bWFpbC5jb20+wsAHBBAWCgB5BQJm9hPnFiEEINYN82B6i6WuHDJXnt6JQmV45pQJEJ7eiUJleOaU
ORQAAAAAABAAIHNhbHRAb3BlbnBncC5vcmeeyPed2COwpofn832zsf7GqOVTYKqx+lHl6SonNgoK
MgIbAwQLBwgJAxUICgUWAAECAwIeAwIZAQAAaw0BAF88l/jCZjM9atPFj/9e4CBnSi6wvHFTcmnU
m/0t5fbUAQAAuybaUsNzdJ4BCSEmgMpZUH3VTo9haF4yo6NTQ5SECc0sTmd1eWVuIFZhbiBOZ3V5
ZW4gPG5ndXllbm52QGl3YXl2aWV0bmFtLmNvbT7CwAQEEBYKAHYFAmb2E+cWIQQg1g3zYHqLpa4c
Mlee3olCZXjmlAkQnt6JQmV45pQ5FAAAAAAAEAAgc2FsdEBvcGVucGdwLm9yZ2wTXRwwELAPHhpj
byBeuRETqMADGNYR+xHuwXRvlzjDAhsDBAsHCAkDFQgKBRYAAQIDAh4DAABKqwEAkK2Kxq2ldXmK
D8IXT00vleQyErlllhAVShUzVtkoX98BAEXpEQcnsKhAb9x7JH3qSAv87MhhxymnQNKFG2bxKDUL
x4sEZvYT5xIKKwYBBAGXVQEFAQEHQCZwzWJxIyMVyozKJFGR4jUFk70hPjlD0ff8sIbohTcFAwAI
B/4HAwiSmYzvfPhVvOA8vh/tskeYjgWcBJrSwstNN9EiiRd4+O08gzcU8BMKiaUY8/DCi25qEyf0
LgmiU4JLDQYvJT1c2U8wXCWlbmUr8Xfno7cvwrIEGBYKAGQFAmb2E+cWIQQg1g3zYHqLpa4cMlee
3olCZXjmlAkQnt6JQmV45pQ5FAAAAAAAEAAgc2FsdEBvcGVucGdwLm9yZyVxOTjEwhAuOGV62ERW
z0hTYzTdx7GLnIS2peI/h75/AhsMAACDowEAzqKJVZSgDPvR00paY+auufbPfDVI7p+zl0qXGqEU
mcYBAEnGTXv34M4dxMkPuz8qAMKX+MiHwiA3vBflJthea+EE
=bBuh
-----END PGP PRIVATE KEY BLOCK-----
EOT;
$edDsaPrivateKey = OpenPGP::decryptPrivateKey($keyData, $passphase);

echo "Sign cleartext message:" . PHP_EOL . PHP_EOL;
$text = <<<EOT
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc felis neque, interdum id iaculis ut, faucibus a ex.
Nam quam tortor, pharetra at dignissim ut, semper nec arcu. Vivamus mollis tortor vitae urna fringilla lacinia id
vel nunc. Ut laoreet pellentesque mattis. Curabitur viverra enim venenatis, mattis velit sed, fringilla lacus. Donec
nulla dui, vestibulum aliquam ultrices hendrerit, euismod iaculis magna. Praesent vitae ipsum id risus feugiat
auctor ac eget tellus.

What we need from the grocery store:
- tofu
- vegetables
- noodles
EOT;
$signedMessage = OpenPGP::signCleartext($text, [
    $rsaPrivateKey,
    $dsaPrivateKey,
    $ecDsaPrivateKey,
    $edDsaPrivateKey,
]);
echo $armored = $signedMessage->armor() . PHP_EOL;

echo "Verify signed message:" . PHP_EOL . PHP_EOL;
$verifications = OpenPGP::verify($armored, [
    $rsaPrivateKey->toPublic(),
    $dsaPrivateKey->toPublic(),
    $ecDsaPrivateKey->toPublic(),
    $edDsaPrivateKey->toPublic(),
]);
foreach ($verifications as $verification) {
    echo "Key ID: {$verification->getKeyID(true)}" . PHP_EOL;
    echo "Signature is verified: {$verification->isVerified()}" .
        PHP_EOL .
        PHP_EOL;
}

echo "Sign detached cleartext message:" . PHP_EOL . PHP_EOL;
$signature = OpenPGP::signDetachedCleartext($text, [
    $rsaPrivateKey,
    $dsaPrivateKey,
    $ecDsaPrivateKey,
    $edDsaPrivateKey,
]);
echo $armored = $signature->armor() . PHP_EOL;

echo "Verify detached signature:" . PHP_EOL . PHP_EOL;
$verifications = OpenPGP::verifyDetached($text, $armored, [
    $rsaPrivateKey->toPublic(),
    $dsaPrivateKey->toPublic(),
    $ecDsaPrivateKey->toPublic(),
    $edDsaPrivateKey->toPublic(),
]);
foreach ($verifications as $verification) {
    echo "Key ID: {$verification->getKeyID(true)}" . PHP_EOL;
    echo "Signature is verified: {$verification->isVerified()}" .
        PHP_EOL .
        PHP_EOL;
}
