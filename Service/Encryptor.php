<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\CoreBundle\Service;

use SodiumException;
use Spipu\CoreBundle\Exception\EncryptorException;

class Encryptor implements EncryptorInterface
{
    /**
     * @var string
     */
    private $keyPair;

    /**
     * Encryptor constructor.
     * @param string $keyPair
     */
    public function __construct(string $keyPair)
    {
        $this->keyPair = $keyPair;
    }

    /**
     * @param string $value
     * @return string
     */
    public function encrypt(string $value): string
    {
        $value = sodium_crypto_box_seal($value, sodium_crypto_box_publickey($this->getKeyPair()));
        $value = $this->binToBase64($value);

        return $value;
    }

    /**
     * @param string $value
     * @return string|null
     */
    public function decrypt(string $value): ?string
    {
        $value = $this->base64ToBin($value);
        $value = sodium_crypto_box_seal_open($value, $this->getKeyPair());
        if ($value === false) {
            return null;
        }

        return $value;
    }

    /**
     * @param string $value
     * @return string
     * @throws SodiumException
     */
    private function binToBase64(string $value): string
    {
        return (string) sodium_bin2base64($value, SODIUM_BASE64_VARIANT_ORIGINAL);
    }

    /**
     * @param string $value
     * @return string
     * @throws SodiumException
     */
    private function base64ToBin(string $value): string
    {
        return (string) sodium_base642bin($value, SODIUM_BASE64_VARIANT_ORIGINAL);
    }

    /**
     * @return string
     * @throws EncryptorException
     */
    private function getKeyPair(): string
    {
        try {
            $keyPair = $this->base64ToBin($this->keyPair);
        } catch (SodiumException $e) {
            throw $this->getKeyPairException();
        }

        if (!$keyPair || empty($keyPair) || $keyPair === '') {
            throw $this->getKeyPairException();
        }

        return $keyPair;
    }

    /**
     * @return EncryptorException
     */
    private function getKeyPairException(): EncryptorException
    {
        return new EncryptorException(
            'The encryptor Key Pair is invalid, '.
            'please regenerate a new one with `spipu:encryptor:generate-key-pair` ' .
            'and save it in `spipu.core.encryptor.key_pair`'
        );
    }

    /**
     * @return string
     * @throws SodiumException
     */
    public function generateKeyPair(): string
    {
        return $this->binToBase64(sodium_crypto_box_keypair());
    }
}
