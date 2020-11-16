<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class EncoderFactoryTest extends TestCase
{
    public function testService()
    {
        $service = new EncoderFactory();
        $this->assertInstanceOf(PasswordEncoderInterface::class, $service->create());
    }
}
