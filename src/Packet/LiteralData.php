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

use OpenPGP\Enum\LiteralFormat as Format;
use OpenPGP\Enum\PacketTag;
use OpenPGP\Type\ForSigningInterface;

/**
 * Implementation of the Literal Data Packet (Tag 11)
 * See RFC 4880, section 5.9.
 * 
 * A Literal Data packet contains the body of a message; data that is not to be further interpreted.
 * 
 * @package   OpenPGP
 * @category  Packet
 * @author    Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright Copyright © 2023-present by Nguyen Van Nguyen.
 */
class LiteralData extends AbstractPacket implements ForSigningInterface
{
    private readonly int $time;

    /**
     * Constructor
     *
     * @param string $data
     * @param Format $format
     * @param string $filename
     * @param int $time
     * @return self
     */
    public function __construct(
        private readonly string $data,
        private readonly Format $format = Format::Utf8,
        private readonly string $filename = '',
        int $time = 0
    )
    {
        parent::__construct(PacketTag::LiteralData);
        $this->time = empty($time) ? time() : $time;
    }

    /**
     * Reads literal data packet from byte string
     *
     * @param string $bytes
     * @return self
     */
    public static function fromBytes(string $bytes): self
    {
        $offset = 0;
        $format = Format::from(ord($bytes[$offset++]));
        $length = ord($bytes[$offset++]);
        $filename = substr($bytes, $offset, $length);

        $offset += $length;
        $unpacked = unpack('N', substr($bytes, $offset, 4));
        $time = reset($unpacked);

        $offset += 4;
        $data = substr($bytes, $offset);

        return new self(
            $data, $format, $filename, $time
        );
    }

    /**
     * Builds literal data packet from text
     *
     * @param string $text
     * @param int $time
     * @return self
     */
    public static function fromText(string $text, int $time = 0): self
    {
        return new self(
            $text, Format::Utf8, '', empty($time) ? time() : $time
        );
    }

    /**
     * Gets literal format
     *
     * @return Format
     */
    public function getFormat(): Format
    {
        return $this->format;
    }

    /**
     * Gets filename
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Gets time
     *
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * Gets data
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function toBytes(): string
    {
        return implode([
            chr($this->format->value),
            chr(strlen($this->filename)),
            $this->filename,
            pack('N', $this->time),
            $this->getSignBytes(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignBytes(): string
    {
        if ($this->format == Format::Text || $this->format == Format::Utf8) {
            return preg_replace('/\r?\n/', "\r\n", $this->data);
        }
        else {
            return $this->data;
        }
    }
}
