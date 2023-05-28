PHP PG (PHP Privacy Guard) - The OpenPGP library in PHP language
================================================================
PHP PG is an implementation of the OpenPGP standard in PHP language.
It implements [RFC4880](https://www.rfc-editor.org/rfc/rfc4880), [RFC6637](https://www.rfc-editor.org/rfc/rfc6637),
parts of [RFC4880bis](https://datatracker.ietf.org/doc/html/draft-ietf-openpgp-rfc4880bis).

## Requirement
* PHP 8.1.x or later,
* [phpseclib](https://github.com/phpseclib/phpseclib) library provides cryptography algorithms,
* (optional) PHPUnit to run tests,

## Features
* Support data signing & encryption.
* Support key management: key generation, key reading, key decryption.
* Support public-key algorithms: [RSA](https://en.wikipedia.org/wiki/RSA_(cryptosystem)),
  [DSA](https://en.wikipedia.org/wiki/Digital_Signature_Algorithm),
  [ElGamal](https://en.wikipedia.org/wiki/ElGamal_encryption),
  [ECDSA](https://en.wikipedia.org/wiki/Elliptic_Curve_Digital_Signature_Algorithm),
  [EdDSA](https://en.wikipedia.org/wiki/EdDSA)
  and [ECDH](https://en.wikipedia.org/wiki/Elliptic-curve_Diffie%E2%80%93Hellman).
* Support symmetric ciphers: 3DES, IDEA (for backward compatibility), CAST5, Blowfish, Twofish,
  [AES-128, AES-192, AES-256](https://en.wikipedia.org/wiki/Advanced_Encryption_Standard),
  [Camellia-128, Camellia-192, Camellia-256](https://en.wikipedia.org/wiki/Camellia_(cipher)).
* Support hash algorithms: MD5, SHA-1, RIPEMD-160, SHA-256, SHA-384, SHA-512, SHA-224.
* Support compression algorithms: Uncompressed, ZIP, ZLIB.
* Support [ECC](https://en.wikipedia.org/wiki/Elliptic-curve_cryptography) curves:
  [secP256k1, secP384r1, secP521r1](https://www.rfc-editor.org/rfc/rfc6090),
  [brainpoolP256r1, brainpoolP384r1, brainpoolP512r1](https://www.rfc-editor.org/rfc/rfc5639),
  [curve25519](https://www.rfc-editor.org/rfc/rfc7748), [ed25519](https://www.rfc-editor.org/rfc/rfc8032),
  [prime256v1](https://www.secg.org/sec2-v2.pdf).
  