<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\Filesystem;

class FilesystemTest extends TestCase
{
    public function testFiles()
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

    public function testZip()
    {
        $service = new Filesystem();

        $assetFolder = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Assets';
        $zipFilename = $assetFolder . DIRECTORY_SEPARATOR . 'unit-test.zip';
        $extractFolder = $assetFolder . DIRECTORY_SEPARATOR . '_temp';

        if ($service->isDir($extractFolder)) {
            $service->remove($extractFolder);
        }
        $service->mkdir($extractFolder);
        $this->assertTrue($service->isDir($extractFolder));

        $this->assertTrue($service->unZip($zipFilename, $extractFolder));
        $this->assertTrue($service->isFile($extractFolder . DIRECTORY_SEPARATOR . 'unit-test.md'));
        $this->assertSame(
            "this is a test\n",
            $service->getContent($extractFolder . DIRECTORY_SEPARATOR . 'unit-test.md')
        );

        $service->remove($extractFolder);
        $this->assertFalse($service->isDir($extractFolder));
    }
}
