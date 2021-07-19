<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\EncoderFactory;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class EncoderFactoryTest extends TestCase
{
    public function testService()
    {
        $service = new EncoderFactory();
        $this->assertInstanceOf(PasswordHasherInterface::class, $service->create());
    }
}
