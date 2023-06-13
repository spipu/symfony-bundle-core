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
    private string $keyPair;

    public function __construct(string $keyPair)
    {
        $this->keyPair = $keyPair;
    }

    public function encrypt(string $value): string
    {
        try {
            $value = sodium_crypto_box_seal($value, sodium_crypto_box_publickey($this->getKeyPair()));
            return $this->binToBase64($value);
        } catch (SodiumException $e) {
            throw new EncryptorException($e->getMessage(), $e->getCode());
        }
    }

    public function decrypt(string $value): ?string
    {
        try {
            $value = $this->base64ToBin($value);
            $value = sodium_crypto_box_seal_open($value, $this->getKeyPair());
        } catch (SodiumException $e) {
            throw new EncryptorException($e->getMessage(), $e->getCode());
        }

        if ($value === false) {
            return null;
        }

        return $value;
    }

    private function binToBase64(string $value): string
    {
        return sodium_bin2base64($value, SODIUM_BASE64_VARIANT_ORIGINAL);
    }

    private function base64ToBin(string $value): string
    {
        return sodium_base642bin($value, SODIUM_BASE64_VARIANT_ORIGINAL);
    }

    private function getKeyPair(): string
    {
        try {
            $keyPair = $this->base64ToBin($this->keyPair);
        } catch (SodiumException $e) {
            throw $this->getKeyPairException();
        }

        if (empty($keyPair)) {
            throw $this->getKeyPairException();
        }

        return $keyPair;
    }

    private function getKeyPairException(): EncryptorException
    {
        return new EncryptorException(
            'The encryptor Key Pair is invalid, ' .
            'please regenerate a new one with `spipu:encryptor:generate-key-pair` ' .
            'and save it in `spipu.core.encryptor.key_pair`'
        );
    }

    public function generateKeyPair(): string
    {
        return $this->binToBase64(sodium_crypto_box_keypair());
    }
}
