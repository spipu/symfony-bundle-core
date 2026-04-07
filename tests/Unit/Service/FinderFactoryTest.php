<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\FinderFactory;
use Symfony\Component\Finder\Finder;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(FinderFactory::class)]
class FinderFactoryTest extends TestCase
{
    public function testService(): void
    {
        $finderFactory = new FinderFactory();
        $finder = $finderFactory->create();

        $this->assertInstanceOf(Finder::class, $finder);
    }
}
