<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Model;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Model\MailHeader;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(MailHeader::class)]
class MailHeaderTest extends TestCase
{
    public function testModel(): void
    {
        $header = new MailHeader('X-Custom-Header', 'custom-value');

        $this->assertSame('X-Custom-Header', $header->getKey());
        $this->assertSame('custom-value', $header->getValue());
    }
}
