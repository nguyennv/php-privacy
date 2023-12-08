<?php declare(strict_types=1);
/**
 * This file is part of the PHP Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenPGP\Cryptor\Mac;

use OpenPGP\Enum\SymmetricAlgorithm;
use OpenPGP\Cryptor\Math\Bitwise;
use phpseclib3\Crypt\Common\BlockCipher;
use phpseclib3\Crypt\AES;

/**
 * CMac class
 * A Cipher based MAC generator (Based upon the CMAC specification)
 * See http://csrc.nist.gov/publications/nistpubs/800-38B/SP_800-38B.pdf
 * 
 * @package  OpenPGP
 * @category Cryptor
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class CMac
{
    const ZERO_CHAR = "\x0";

    private readonly BlockCipher $cipher;

    private readonly int $blockSize;

    private readonly string $zeroBlock;

    /**
     * Constructor
     *
     * @param SymmetricAlgorithm $symmetric
     * @param int $macSize
     * @return self
     */
    public function __construct(
        SymmetricAlgorithm $symmetric = SymmetricAlgorithm::Aes128,
        private int $macSize = 0
    )
    {
        $this->cipher = $this->cipherEngine($symmetric);
        $this->blockSize = $symmetric->blockSize();
        $this->zeroBlock = str_repeat(self::ZERO_CHAR, $this->blockSize);
        $this->cipher->setIV($this->zeroBlock);

        if ($this->macSize == 0) {
            $this->macSize = $this->blockSize;
        }

        if ($this->macSize > $this->blockSize) {
            throw new \LengthException(
                'MAC size must be less or equal to ' . $this->blockSize
            );
        }
    }

    /**
     * Generate the MAC using the supplied data
     *
     * @param string $data - The data to use to generate the MAC with
     * @param string $key - The key to generate the MAC
     * @return string The generated MAC of the appropriate size
     */
    public function generate(string $data, string $key): string
    {
        $this->cipher->setKey($key);
        $keys    = $this->generateKeys();
        $mBlocks = $this->splitData($data, $keys);
        $cBlock  = $this->zeroBlock;
        foreach ($mBlocks as $block) {
            $cBlock = $this->cipher->encryptBlock($cBlock ^ $block);
        }
        return substr($cBlock, 0, $this->macSize);
    }

    /**
     * Get the size, in bytes, of the MAC produced by this implementation.
     *
     * @return int
     */
    public function getMacSize(): int
    {
        return $this->macSize;
    }

    /**
     * Get block cipher engine
     *
     * @return BlockCipher
     */
    private function cipherEngine(
        SymmetricAlgorithm $symmetric = SymmetricAlgorithm::Aes128
    ): BlockCipher
    {
        return match($symmetric) {
            SymmetricAlgorithm::Plaintext => throw new \InvalidArgumentException(
                'Symmetric algorithm "Plaintext" is unsupported.'
            ),
            SymmetricAlgorithm::Idea => new class extends \OpenPGP\Cryptor\Symmetric\IDEA {
                use CMacCipherTrait;
            },
            SymmetricAlgorithm::TripleDes => new class extends \phpseclib3\Crypt\TripleDES {
                use CMacCipherTrait;
            },
            SymmetricAlgorithm::Cast5 => new class extends \OpenPGP\Cryptor\Symmetric\CAST5 {
                use CMacCipherTrait;
            },
            SymmetricAlgorithm::Blowfish => new class extends \phpseclib3\Crypt\Blowfish {
                use CMacCipherTrait;
            },
            SymmetricAlgorithm::Aes128, SymmetricAlgorithm::Aes192, SymmetricAlgorithm::Aes256
                => new class extends \phpseclib3\Crypt\AES {
                    use CMacCipherTrait;
                },
            SymmetricAlgorithm::Twofish => new class extends \phpseclib3\Crypt\Twofish {
                use CMacCipherTrait;
            },
            SymmetricAlgorithm::Camellia128, SymmetricAlgorithm::Camellia192, SymmetricAlgorithm::Camellia256
                => new class extends \OpenPGP\Cryptor\Symmetric\Camellia {
                    use CMacCipherTrait;
                },
        };
    }

    /**
     * Generate a pair of keys by encrypting a block of all 0's,
     * and then maniuplating the result
     *
     * @return array The generated keys
     */
    private function generateKeys(): array
    {
        $keys = [];
        $rVal = $this->getRValue($this->blockSize);
        $lVal = $this->cipher->encryptBlock($this->zeroBlock);

        $keys[0] = $this->leftShift($lVal, 1);
        if (ord(substr($lVal, 0, 1)) > 127) {
            $keys[0] = $keys[0] ^ $rVal;
        }

        $keys[1] = $this->leftShift($keys[0], 1);
        if (ord(substr($keys[0], 0, 1)) > 127) {
            $keys[1] = $keys[1] ^ $rVal;
        }
        return $keys;
    }

    /**
     * Get an RValue based upon the block size
     *
     * @param int $size - The size of the block in bytes
     *
     * @see http://csrc.nist.gov/publications/nistpubs/800-38B/SP_800-38B.pdf
     * @return string A RValue of the appropriate block size
     */
    protected function getRValue(int $size) {
        switch ($size * 8) {
            case 64:
                return str_repeat(self::ZERO_CHAR, 7) . "\x1B";
            case 128:
                return str_repeat(self::ZERO_CHAR, 15) . "\x87";
            default:
                throw new \LengthException(
                    'Unsupported block size for the cipher'
                );
        }
    }

    private function leftShift(string $data, int $bits): string
    {
        $mask   = (Bitwise::MASK_8BITS << (8 - $bits)) & Bitwise::MASK_8BITS;
        $state  = 0;
        $result = '';
        $length = strlen($data);
        for ($i = $length - 1; $i >= 0; $i--) {
            $tmp     = ord($data[$i]);
            $result .= chr(($tmp << $bits) | $state);
            $state   = ($tmp & $mask) >> (8 - $bits);
        }
        return strrev($result);
    }

    /**
     * Split the data into appropriate block chunks, encoding with the kyes
     *
     * @param string $data - The data to split
     * @param array $keys - The keys to use for encoding
     * @return array The array of chunked and encoded data
     */
    private function splitData(string $data, array $keys): array
    {
        $data      = str_split($data, $this->blockSize);
        $last      = end($data) ?: '';
        if (strlen($last) != $this->blockSize) {
            //Pad the last element
            $last .= "\x80" . substr($this->zeroBlock, 0, $this->blockSize - 1 - strlen($last));
            $last  = $last ^ $keys[1];
        }
        else {
            $last  = $last ^ $keys[0];
        }
        $data[count($data) - 1] = $last;
        return $data;
    }
}
