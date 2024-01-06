<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\Environment;

class EnvironmentTest extends TestCase
{
    public function testServiceOk()
    {
        $service = new Environment('dev');
        $this->assertSame('dev', $service->getCurrentCode());
        $this->assertSame('Development', $service->getCurrentName());
        $this->assertSame('secondary', $service->getCurrentColor());
        $this->assertSame(true, $service->isDevelopment());
        $this->assertSame(false, $service->isPreproduction());
        $this->assertSame(false, $service->isProduction());
        $this->assertSame(' [dev]', $service->getEnvironmentSuffix());

        $service = new Environment('preprod');
        $this->assertSame('preprod', $service->getCurrentCode());
        $this->assertSame('PreProduction', $service->getCurrentName());
        $this->assertSame('danger', $service->getCurrentColor());
        $this->assertSame(false, $service->isDevelopment());
        $this->assertSame(true, $service->isPreproduction());
        $this->assertSame(false, $service->isProduction());
        $this->assertSame(' [preprod]', $service->getEnvironmentSuffix());

        $service = new Environment('prod');
        $this->assertSame('prod', $service->getCurrentCode());
        $this->assertSame('Production', $service->getCurrentName());
        $this->assertSame('primary', $service->getCurrentColor());
        $this->assertSame(false, $service->isDevelopment());
        $this->assertSame(false, $service->isPreproduction());
        $this->assertSame(true, $service->isProduction());
        $this->assertSame('', $service->getEnvironmentSuffix());
    }
}
