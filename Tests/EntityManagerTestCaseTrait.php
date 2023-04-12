<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spipu\CoreBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerTestCaseTrait
{
    /**
     * @var EntityManagerInterface|null
     */
    private $entityManager = null;

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        if ($this->entityManager === null) {
            $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        }

        return $this->entityManager;
    }
}
