<?php declare(strict_types=1);
/**
 * This file is part of the PHP PG library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenPGP\Cryptor\Asymmetric;

use phpseclib3\Math\BigInteger;

/**
 * ElGamal public key class
 *
 * @package    OpenPGP
 * @category   Cryptor
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2023-present by Nguyen Van Nguyen.
 */
class ElGamalPublicKey extends ElGamal
{
    /**
     * Encryption
     *
     * @param string $plainText
     * @return string
     */
    public function encrypt(string $plainText): string
    {
        $inputSize = (int) (($this->getBitSize() - 1) / 8);
        $outputSize = 2 * (($this->getBitSize() + 7) >> 3);

        if (strlen($plainText) > $inputSize) {
            throw new \InvalidArgumentException('input too large for ' . self::ALGORITHM . ' cipher.');
        }

        $prime = $this->getPrime();
        $input = $this->bits2int($plainText);
        if ($input->compare($prime) > 0) {
            throw new \InvalidArgumentException('input too large for ' . self::ALGORITHM . ' cipher.');
        }

        $byteLength = (int) ($outputSize / 2);
        do {
            $k = BigInteger::randomRange(self::$one, $prime->subtract(self::$one));
            $gamma = $this->getGenerator()->modPow($k, $prime);
            list(, $phi) = $input->multiply($this->getY()->modPow($k, $prime))->divide($prime);
        } while ($gamma->getLengthInBytes() < $byteLength || $phi->getLengthInBytes() < $byteLength);

        return implode([
            substr($gamma->toBytes(true), 0, $byteLength),
            substr($phi->toBytes(true), 0, $byteLength),
        ]);
    }

    /**
     * Returns the public key
     *
     * @param string $type
     * @param array $options optional
     * @return string
     */
    public function toString($type, array $options = [])
    {
        return json_encode([
            'p' => $this->getPrime(),
            'g' => $this->getGenerator(),
            'y' => $this->getY(),
        ]);
    }
}
