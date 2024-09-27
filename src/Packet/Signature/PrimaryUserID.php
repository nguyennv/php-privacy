<?php declare(strict_types=1);
/**
 * This file is part of the PHP Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenPGP\Packet\Signature;

use OpenPGP\Enum\SignatureSubpacketType;
use OpenPGP\Packet\SignatureSubpacket;

/**
 * PrimaryUserID sub-packet class
 *
 * @package  OpenPGP
 * @category Packet
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class PrimaryUserID extends SignatureSubpacket
{
    /**
     * Constructor
     *
     * @param string $data
     * @param bool $critical
     * @param bool $isLong
     * @return self
     */
    public function __construct(
        string $data,
        bool $critical = false,
        bool $isLong = false
    ) {
        parent::__construct(
            SignatureSubpacketType::PrimaryUserID->value,
            $data,
            $critical,
            $isLong
        );
    }

    /**
     * Is primary user ID
     *
     * @return bool
     */
    public function isPrimaryUserID(): bool
    {
        return ord($this->getData()) != 0;
    }
}
