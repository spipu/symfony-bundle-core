<?php
namespace Spipu\CoreBundle\Tests\Unit\Assets;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Assets\AssetInterface;
use Spipu\CoreBundle\Assets\ListAsset;

class ListAssetTest extends TestCase
{
    public function testList()
    {
        $assetA = $this->createMock(AssetInterface::class);
        $assetA->expects($this->once())->method('getCode')->willReturn('aaa');

        $assetB = $this->createMock(AssetInterface::class);
        $assetB->expects($this->once())->method('getCode')->willReturn('bbb');

        $assetC = $this->createMock(AssetInterface::class);
        $assetC->expects($this->once())->method('getCode')->willReturn('ccc');

        $list = new ListAsset([]);
        $this->assertSame([], $list->get());

        $list = new ListAsset([$assetA, $assetC, $assetB]);
        $this->assertSame(
            [
                'aaa' => $assetA,
                'bbb' => $assetB,
                'ccc' => $assetC,
            ],
            $list->get()
        );
    }

    public function testBadCode()
    {
        $asset = $this->createMock(AssetInterface::class);
        $asset->expects($this->once())->method('getCode')->willReturn('Aaa/../');

        $this->expectException(\Exception::class);
        new ListAsset([$asset]);
    }
}
