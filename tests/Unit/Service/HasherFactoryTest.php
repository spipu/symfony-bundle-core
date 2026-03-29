<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\HasherFactory;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class HasherFactoryTest extends TestCase
{
    public function testService(): void
    {
        $service = new HasherFactory();
        $this->assertInstanceOf(PasswordHasherInterface::class, $service->create());
    }
}
