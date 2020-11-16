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

use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class EncoderFactory
{
    /**
     * @return PasswordEncoderInterface
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function create(): PasswordEncoderInterface
    {
        return new NativePasswordEncoder();
    }
}
