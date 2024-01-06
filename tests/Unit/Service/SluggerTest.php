<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\Slugger;
use Spipu\CoreBundle\Service\SluggerInterface;

class SluggerTest extends TestCase
{
    public function testServiceOk()
    {
        $service = new Slugger();
        $this->assertInstanceOf(SluggerInterface::class, $service);

        $this->assertSame('', $service->slug(''));
        $this->assertSame('a1z', $service->slug('a1Z'));
        $this->assertSame('aec', $service->slug('Aéç'));
    }
}
