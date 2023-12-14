<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\HasherFactory;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class HasherFactoryTest extends TestCase
{
    public function testService()
    {
        $service = new HasherFactory();
        $this->assertInstanceOf(PasswordHasherInterface::class, $service->create());
    }
}
