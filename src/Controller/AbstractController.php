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

namespace Spipu\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;

abstract class AbstractController extends SymfonyAbstractController
{
    protected function addFlashTrans(
        string $type,
        string $message,
        array $params = [],
        ?string $domain = null
    ): void {
        $this->addFlash($type, $this->trans($message, $params, $domain));
    }

    protected function trans(
        string $message,
        array $params = [],
        ?string $domain = null
    ): string {
        return $this->container->get('translator')->trans($message, $params, $domain);
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
                'translator',
            ];
    }
}
