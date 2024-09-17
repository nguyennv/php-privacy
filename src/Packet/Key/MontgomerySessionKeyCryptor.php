<?php declare(strict_types=1);
/**
 * This file is part of the PHP Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenPGP\Packet\Key;

use OpenPGP\Enum\{
    MontgomeryCurve,
    SymmetricAlgorithm,
};
use OpenPGP\Type\{
    SecretKeyPacketInterface,
    SessionKeyCryptorInterface,
    SessionKeyInterface,
};
use phpseclib3\Crypt\{
    DH,
    EC,
};
use phpseclib3\Crypt\EC\{
    PrivateKey,
    PublicKey,
};

/**
 * Montgomery session key cryptor class.
 *
 * @package  OpenPGP
 * @category Packet
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class MontgomerySessionKeyCryptor implements SessionKeyCryptorInterface
{
    /**
     * Constructor
     *
     * @param string $ephemeralKey
     * @param string $wrappedKey
     * @param int $pkeskVersion
     * @param MontgomeryCurve $curve
     * @return self
     */
    public function __construct(
        private readonly string $ephemeralKey,
        private readonly string $wrappedKey,
        private readonly int $pkeskVersion,
        private readonly MontgomeryCurve $curve = MontgomeryCurve::Curve25519,
    )
    {
    }

    /**
     * Read encrypted session key from byte string
     *
     * @param string $bytes
     * @param int $pkeskVersion
     * @param MontgomeryCurve $curve
     * @return self
     */
    public static function fromBytes(
        string $bytes,
        int $pkeskVersion,
        MontgomeryCurve $curve = MontgomeryCurve::Curve25519,
    ): self
    {
        $length = ord($bytes[$curve->payloadSize()]);
        return new self(
            substr($bytes, 0, $curve->payloadSize()),
            substr($bytes, $curve->payloadSize() + 1, $length),
            $pkeskVersion,
            $curve,
        );
    }

    /**
     * Produce cryptor by encrypting session key
     *
     * @param SessionKeyInterface $sessionKey
     * @param PublicKey $publicKey
     * @param int $pkeskVersion
     * @param MontgomeryCurve $curve
     * @return self
     */
    public static function encryptSessionKey(
        SessionKeyInterface $sessionKey,
        PublicKey $publicKey,
        int $pkeskVersion,
        MontgomeryCurve $curve = MontgomeryCurve::Curve25519
    ): self
    {
        if ($sessionKey->getSymmetric() !== $curve->symmetricAlgorithm()) {
            throw new \InvalidArgumentException(
                'Symmetric algorithm of the session key mismatch!'
            );
        }
        $privateKey = EC::createKey(
            $publicKey->getCurve()
        );
        $sharedSecret = DH::computeSecret(
            $privateKey,
            $publicKey
        );
        $ephemeralKey = $privateKey->getPublicKey()->getEncodedCoordinates();

        $kek = hash_hkdf(
            $curve->hashAlgorithm(),
            implode([
                $ephemeralKey,
                $publicKey->getEncodedCoordinates(),
                $sharedSecret,
            ]),
            $curve->kekSize()->value,
            $curve->hkdfInfo()
        );
        $keyWrapper = new AesKeyWrapper(
            $curve->kekSize()
        );

        return new self(
            $ephemeralKey,
            $keyWrapper->wrap(
                $kek,
                $pkeskVersion === self::PKESK_VERSION_3 ?
                    $sessionKey->toBytes() :
                    $sessionKey->getEncryptionKey()
            ),
            $pkeskVersion,
            $curve,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes(): string
    {
        return implode([
            $this->ephemeralKey,
            chr(strlen($this->wrappedKey)),
            $this->wrappedKey,
        ]);
    }

    /**
     * Get ephemeral key
     *
     * @return string
     */
    public function getEphemeralKey(): string
    {
        return $this->ephemeralKey;
    }

    /**
     * Get wrapped key
     *
     * @return string
     */
    public function getWrappedKey(): string
    {
        return $this->wrappedKey;
    }

    /**
     * {@inheritdoc}
     */
    public function decryptSessionKey(
        SecretKeyPacketInterface $secretKey
    ): SessionKeyInterface
    {
        $decrypted = $this->decrypt(
            $secretKey->getKeyMaterial()->getECPrivateKey(),
        );
        return $this->pkeskVersion === self::PKESK_VERSION_3 ?
            new SessionKey(
                substr($decrypted, 1),
                SymmetricAlgorithm::from(ord($decrypted[0]))
            ) :
            new SessionKey(
                $decrypted, $this->curve->symmetricAlgorithm()
            );
    }

    /**
     * Decrypt session key by using private key
     *
     * @param PrivateKey $privateKey
     * @return string
     */
    protected function decrypt(
        PrivateKey $privateKey
    ): string
    {
        $publicKey = EC::loadFormat('MontgomeryPublic', $this->ephemeralKey);
        $sharedSecret = DH::computeSecret(
            $privateKey,
            $publicKey
        );

        $kek = hash_hkdf(
            $this->curve->hashAlgorithm(),
            implode([
                $this->ephemeralKey,
                $privateKey->getEncodedCoordinates(),
                $sharedSecret,
            ]),
            $this->curve->kekSize()->value,
            $this->curve->hkdfInfo()
        );
        $keyWrapper = new AesKeyWrapper(
            $this->curve->kekSize()
        );
        return $keyWrapper->unwrap($kek, $this->wrappedKey);
    }
}
