<?php declare(strict_types=1);
/**
 * This file is part of the PHP PG library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenPGP\Packet;

use OpenPGP\Enum\SignatureSubpacketType;

/**
 * Sub packet reader class
 * 
 * @package   OpenPGP
 * @category  Packet
 * @author    Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright Copyright © 2023-present by Nguyen Van Nguyen.
 */
class SubpacketReader
{
    /**
     * Constructor
     *
     * @param int $type
     * @param string $data
     * @param int $offset
     * @param bool $isLong
     * @return self
     */
    public function __construct(
        private readonly int $type = 0,
        private readonly string $data = '',
        private readonly int $offset = 0,
        private readonly bool $isLong = false
    )
    {
    }

    /**
     * Get type
     * 
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Get data
     * 
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Get offset
     * 
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Get is long
     * 
     * @return bool
     */
    public function isLong(): bool
    {
        return $this->isLong;
    }

    /**
     * Read signature sub packet from byte string
     *
     * @param string $bytes
     * @return array
     */
    public static function readSignatureSubpackets(string $bytes): array
    {
        $offset = 0;
        $length = strlen($bytes);
        $subpackets = [];
        while ($offset < $length) {
            $reader = self::read($bytes, $offset);
            $offset = $reader->getOffset();
            $data = $reader->getData();
            if (!empty($data)) {
                $critical = (($reader->getType() & 0x80) != 0);
                $type = SignatureSubpacketType::from($reader->getType() & 0x7f);
                switch ($type) {
                    case SignatureSubpacketType::SignatureCreationTime:
                        $subpackets[] = new Signature\SignatureCreationTime(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::SignatureExpirationTime:
                        $subpackets[] = new Signature\SignatureExpirationTime(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::ExportableCertification:
                        $subpackets[] = new Signature\ExportableCertification(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::TrustSignature:
                        $subpackets[] = new Signature\TrustSignature(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::RegularExpression:
                        $subpackets[] = new Signature\RegularExpression(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::Revocable:
                        $subpackets[] = new Signature\Revocable(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::KeyExpirationTime:
                        $subpackets[] = new Signature\KeyExpirationTime(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::PreferredSymmetricAlgorithms:
                        $subpackets[] = new Signature\PreferredSymmetricAlgorithms(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::RevocationKey:
                        $subpackets[] = new Signature\RevocationKey(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::IssuerKeyID:
                        $subpackets[] = new Signature\IssuerKeyID(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::NotationData:
                        $subpackets[] = new Signature\NotationData(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::PreferredHashAlgorithms:
                        $subpackets[] = new Signature\PreferredHashAlgorithms(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::PreferredCompressionAlgorithms:
                        $subpackets[] = new Signature\PreferredCompressionAlgorithms(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::KeyServerPreferences:
                        $subpackets[] = new Signature\KeyServerPreferences(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::PreferredKeyServer:
                        $subpackets[] = new Signature\PreferredKeyServer(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::PrimaryUserID:
                        $subpackets[] = new Signature\PrimaryUserID(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::PolicyURI:
                        $subpackets[] = new Signature\PolicyURI(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::KeyFlags:
                        $subpackets[] = new Signature\KeyFlags(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::SignerUserID:
                        $subpackets[] = new Signature\SignerUserID(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::RevocationReason:
                        $subpackets[] = new Signature\RevocationReason(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::Features:
                        $subpackets[] = new Signature\Features(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::SignatureTarget:
                        $subpackets[] = new Signature\SignatureTarget(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::EmbeddedSignature:
                        $subpackets[] = new Signature\EmbeddedSignature(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    case SignatureSubpacketType::IssuerFingerprint:
                        $subpackets[] = new Signature\IssuerFingerprint(
                            $data, $critical, $reader->isLong()
                        );
                        break;
                    default:
                        $subpackets[] = new SignatureSubpacket(
                            $type->value, $data, $critical, $reader->isLong()
                        );
                        break;
                }
            }
        }
        return $subpackets;
    }

    /**
     * Read user attribute sub packet from byte string
     *
     * @param string $bytes
     * @return array
     */
    public static function readUserAttributes(string $bytes): array
    {
        $attributes = [];
        $offset = 0;
        $len = strlen($bytes);
        while ($offset < $len) {
            $reader = self::read($bytes, $offset);
            $offset = $reader->getOffset();
            if (!empty($reader->getData())) {
                switch ($reader->getType()) {
                    case ImageUserAttribute::JPEG:
                        $attributes[] = new ImageUserAttribute(
                            $reader->getData(),
                            $reader->isLong()
                        );
                        break;
                    default:
                        $attributes[] = new UserAttributeSubpacket(
                            $reader->getType(),
                            $reader->getData(),
                            $reader->isLong()
                        );
                        break;
                }
            }
        }
        return $attributes;
    }

    /**
     * Read sub packet from byte string
     *
     * @param string $bytes
     * @param int $offset
     * @return SubpacketReader
     */
    public static function read(
        string $bytes, int $offset = 0
    ): SubpacketReader
    {
        $header = ord($bytes[$offset++]);
        if ($header < 192) {
            return new SubpacketReader(
                ord($bytes[$offset]),
                substr($bytes, $offset + 1, $header - 1),
                $offset + $header
            );
        }
        elseif ($header < 255) {
            $length = (($header - 192) << 8) + (ord($bytes[$offset++])) + 192;
            return new SubpacketReader(
                ord($bytes[$offset]),
                substr($bytes, $offset + 1, $length - 1),
                $offset + $length,
            );
        }
        elseif ($header == 255) {
            $unpacked = unpack('N', substr($bytes, $offset, 4));
            $length = reset($unpacked);
            $offset += 4;
            return new SubpacketReader(
                $bytes[$offset],
                substr($bytes, $offset + 1, $length - 1),
                $offset + $length,
                true,
            );
        }
        return new SubpacketReader();
    }
}
