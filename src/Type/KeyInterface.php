<?php declare(strict_types=1);
/**
 * This file is part of the PHP PG library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenPGP\Type;

use DateTime;

/**
 * Key interface
 * 
 * @package   OpenPGP
 * @category  Type
 * @author    Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright Copyright © 2023-present by Nguyen Van Nguyen.
 */
interface KeyInterface
{
    /**
     * Returns key packet
     *
     * @return KeyPacketInterface
     */
    function getKeyPacket(): KeyPacketInterface;

    /**
     * Returns key as public key
     *
     * @return KeyInterface
     */
    function toPublic(): KeyInterface;

    /**
     * Returns the expiration time of the key or null if key does not expire.
     *
     * @return DateTime
     */
    function getExpirationTime(): ?DateTime;

    /**
     * Is revoked key
     *
     * @param SignaturePacketInterface $certificate
     * @param DateTime $time
     * @return bool
     */
    function isRevoked(
        ?SignaturePacketInterface $certificate = null, ?DateTime $time = null
    ): bool;

    /**
     * Verify key.
     * Checks for revocation signatures, expiration time and valid self signature.
     * 
     * @param string $userID
     * @param DateTime $time
     * @return bool
     */
    function verify(string $userID = '', ?DateTime $time = null): bool;
}
