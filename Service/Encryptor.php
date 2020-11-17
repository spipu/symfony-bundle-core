<?php
/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Spipu\CoreBundle\Service;

class Encryptor implements EncryptorInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * Encryptor constructor.
     * @param string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param string $value
     * @return string
     */
    public function encrypt(string $value): string
    {
        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public function decrypt(string $value): string
    {
        return $value;
    }
}
