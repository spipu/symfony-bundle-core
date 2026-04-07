<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\Slugger;
use Spipu\CoreBundle\Service\SluggerInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(Slugger::class)]
class SluggerTest extends TestCase
{
    public function testServiceOk(): void
    {
        $service = new Slugger();
        $this->assertInstanceOf(SluggerInterface::class, $service);

        $this->assertSame('', $service->slug(''));
        $this->assertSame('a1z', $service->slug('a1Z'));
        $this->assertSame('aec', $service->slug('Aéç'));
    }
}
