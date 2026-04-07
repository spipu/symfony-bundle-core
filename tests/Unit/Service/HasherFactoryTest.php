<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\HasherFactory;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(HasherFactory::class)]
class HasherFactoryTest extends TestCase
{
    public function testService(): void
    {
        $service = new HasherFactory();
        $this->assertInstanceOf(PasswordHasherInterface::class, $service->create());
    }
}
