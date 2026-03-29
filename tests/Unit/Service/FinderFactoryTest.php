<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\FinderFactory;
use Symfony\Component\Finder\Finder;

class FinderFactoryTest extends TestCase
{
    public function testService(): void
    {
        $finderFactory = new FinderFactory();
        $finder = $finderFactory->create();

        $this->assertInstanceOf(Finder::class, $finder);
    }
}
