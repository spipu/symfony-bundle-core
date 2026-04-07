<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Tests\Unit\Assets;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Assets\AssetInterface;
use Spipu\CoreBundle\Assets\ListAsset;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(ListAsset::class)]
class ListAssetTest extends TestCase
{
    public function testList(): void
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

    public function testBadCode(): void
    {
        $asset = $this->createMock(AssetInterface::class);
        $asset->expects($this->once())->method('getCode')->willReturn('Aaa/../');

        $this->expectException(\Exception::class);
        new ListAsset([$asset]);
    }
}
