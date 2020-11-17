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

interface EncryptorInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function encrypt(string $value): string;

    /**
     * @param string $value
     * @return string
     */
    public function decrypt(string $value): string;
}
