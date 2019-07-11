<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\Filesystem;

class FilesystemTest extends TestCase
{
    public function testService()
    {
        $service = new Filesystem();


        $this->assertTrue($service->isFile(__FILE__));
        $this->assertFalse($service->isDir(__FILE__));

        $this->assertTrue($service->isDir(__DIR__));
        $this->assertFalse($service->isFile(__DIR__));

        $this->assertFalse($service->isFile(__FILE__ . '.badfile'));
        $this->assertFalse($service->isDir(__FILE__ . '.badfile'));

        $this->assertSame(file_get_contents(__FILE__), $service->getContent(__FILE__));
    }
}
