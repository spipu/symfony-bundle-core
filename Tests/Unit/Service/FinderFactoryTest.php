<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\FinderFactory;
use Symfony\Component\Finder\Finder;

class FinderFactoryTest extends TestCase
{
    public function testService()
    {
        $finderFactory = new FinderFactory();
        $finder = $finderFactory->create();

        $this->assertInstanceOf(Finder::class, $finder);
    }
}
