<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Assets\AssetInterface;
use Spipu\CoreBundle\Assets\ListAsset;
use Spipu\CoreBundle\Service\Assets;
use Spipu\CoreBundle\Tests\SpipuCoreMock;
use Spipu\CoreBundle\Tests\SymfonyMock;

class AssetsTest extends TestCase
{
    public function testErrorNoTargetDir()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');

        $this->expectException(\Exception::class);
        $service->installAssets();
    }

    public function testPublicFolderDoesNotExists()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $filesystem
            ->expects($this->exactly(2))
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', false],
                    ['/mock/project/my_public', false],
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');

        $this->expectException(\Exception::class);
        $service->setTargetDir('my_public');
    }

    public function testPublicFolderExistsDirectly()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                    ['/mock/project/my_public', false],
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $this->assertSame('my_public/bundles/', $service->setTargetDir('my_public'));
    }

    public function testPublicFolderExistsInProject()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', false],
                    ['/mock/project/my_public', true],
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $this->assertSame('/mock/project/my_public/bundles/', $service->setTargetDir('my_public'));
    }

    public function testAutomaticPublicFolderWithoutComposer()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $filesystem
            ->method('isFile')
            ->with('/mock/project/composer.json')
            ->willReturn(false);

        $filesystem
            ->method('isDir')
            ->with('public')
            ->willReturn(true);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $this->assertSame('public/bundles/', $service->setTargetDir(''));
    }

    public function testAutomaticPublicFolderWithComposerEmpty()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $filesystem
            ->method('isFile')
            ->with('/mock/project/composer.json')
            ->willReturn(true);

        $filesystem
            ->method('getContent')
            ->with('/mock/project/composer.json')
            ->willReturn('');

        $filesystem
            ->method('isDir')
            ->with('public')
            ->willReturn(true);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $this->assertSame('public/bundles/', $service->setTargetDir(''));
    }

    public function testAutomaticPublicFolderWithComposerGood()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $filesystem
            ->method('isFile')
            ->with('/mock/project/composer.json')
            ->willReturn(true);

        $filesystem
            ->method('getContent')
            ->with('/mock/project/composer.json')
            ->willReturn(json_encode(['extra' => ['public-dir' => 'my-public']]));

        $filesystem
            ->method('isDir')
            ->with('my-public')
            ->willReturn(true);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $this->assertSame('my-public/bundles/', $service->setTargetDir(''));
    }

    public function testNoAsset()
    {
        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                ]
            );

        $symfonyStyle = SymfonyMock::getConsoleSymfonyStyle($this);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->assertTrue($service->installAssets($symfonyStyle));
        $this->assertSame([], SymfonyMock::getConsoleOutputResult());
    }

    public function testAssetBadType()
    {
        $asset = $this->createAsset('aaa', 'bad-type', 'my-source', []);

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->expectException(\Exception::class);
        $service->installAssets();
    }

    public function testAssetVendorFile()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_VENDOR,
            'my-vendor/my-package',
            ['src/js/script.js' => 'js/script.js']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                    ['/mock/project/vendor/my-vendor/my-package/src/js/script.js', false],
                ]
            );

        $filesystem
            ->method('isFile')
            ->willReturnMap(
                [
                    ['/mock/project/vendor/my-vendor/my-package/src/js/script.js', true],
                ]
            );

        $filesystem
            ->expects($this->once())
            ->method('copy')
            ->with(
                '/mock/project/vendor/my-vendor/my-package/src/js/script.js',
                'my_public/bundles/aaa/js/script.js'
            );

        $symfonyStyle = SymfonyMock::getConsoleSymfonyStyle($this);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->assertTrue($service->installAssets($symfonyStyle));
        $this->assertSame(['=> aaa'], SymfonyMock::getConsoleOutputResult());
    }

    public function testAssetVendorDir()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_VENDOR,
            'my-vendor/my-package',
            ['src/css' => 'css']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                    ['/mock/project/vendor/my-vendor/my-package/src/css', true],
                ]
            );

        $filesystem
            ->method('isFile')
            ->willReturnMap(
                [
                    ['/mock/project/vendor/my-vendor/my-package/src/css', false],
                ]
            );

        $filesystem
            ->expects($this->once())
            ->method('mirror')
            ->with(
                '/mock/project/vendor/my-vendor/my-package/src/css',
                'my_public/bundles/aaa/css'
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->assertTrue($service->installAssets());
    }

    public function testAssetVendorBad()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_VENDOR,
            'my-vendor/my-package',
            ['src/wrong' => 'wrong']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                    ['/mock/project/vendor/my-vendor/my-package/src/wrong', false],
                ]
            );

        $filesystem
            ->method('isFile')
            ->willReturnMap(
                [
                    ['/mock/project/vendor/my-vendor/my-package/src/wrong', false],
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->expectException(\Exception::class);
        $service->installAssets();
    }

    public function testAssetUrlBad()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_URL,
            'bad/url',
            ['my-file.js' => 'js/my-file.js']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true]
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->expectException(\Exception::class);
        $service->installAssets();
    }

    public function testAssetUrlGood()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_URL,
            'http://my-mock.test/',
            ['my-file.js' => 'js/my-file.js']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true]
                ]
            );

        $filesystem
            ->expects($this->once())
            ->method('copy')
            ->with(
                'http://my-mock.test/my-file.js',
                'my_public/bundles/aaa/js/my-file.js'
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->assertTrue($service->installAssets());
    }

    public function testAssetZipBadUrl()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_URL_ZIP,
            'bad/url',
            ['my-file.js' => 'js/my-file.js']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true]
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->expectException(\Exception::class);
        $service->installAssets();
    }

    public function testAssetZipBadExt()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_URL_ZIP,
            'http://my-mock.test/file.md',
            ['my-file.js' => 'js/my-file.js']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true]
                ]
            );

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->expectException(\Exception::class);
        $service->installAssets();
    }

    public function testAssetZipBadFile()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_URL_ZIP,
            'http://my-mock.test/sources.zip',
            ['my-file.js' => 'js/my-file.js']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                    ['/tmp/spipu_core_asset_aaa.extract', false],
                ]
            );

        $filesystem
            ->method('isFile')
            ->willReturnMap(
                [
                    ['/tmp/spipu_core_asset_aaa', true],
                ]
            );

        $filesystem
            ->method('unZip')
            ->with('/tmp/spipu_core_asset_aaa', '/tmp/spipu_core_asset_aaa.extract')
            ->willReturn(false);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->expectException(\Exception::class);
        $service->installAssets();
    }

    public function testAssetZipGood()
    {
        $asset = $this->createAsset(
            'aaa',
            AssetInterface::TYPE_URL_ZIP,
            'http://my-mock.test/sources.zip',
            ['my-file.js' => 'js/my-file.js']
        );

        $filesystem = SpipuCoreMock::getFilesystem($this);
        $finderFactory = SpipuCoreMock::getFinderFactory($this);
        $list = $this->createList([$asset]);

        $filesystem
            ->method('isDir')
            ->willReturnMap(
                [
                    ['my_public', true],
                    ['/tmp/spipu_core_asset_aaa.extract', true],
                    ['/tmp/spipu_core_asset_aaa.extract/my-file.js', false],
                ]
            );

        $filesystem
            ->method('isFile')
            ->willReturnMap(
                [
                    ['/tmp/spipu_core_asset_aaa', true],
                    ['/tmp/spipu_core_asset_aaa.extract/my-file.js', true],
                ]
            );

        $filesystem
            ->method('unZip')
            ->with('/tmp/spipu_core_asset_aaa', '/tmp/spipu_core_asset_aaa.extract')
            ->willReturn(true);

        $service = new Assets($filesystem, $finderFactory, $list, '/mock/project');
        $service->setTargetDir('my_public');
        $this->assertTrue($service->installAssets());
    }

    /**
     * @param string $code
     * @param string $type
     * @param string $source
     * @param array $mapping
     * @return MockObject|AssetInterface
     */
    private function createAsset(string $code, string $type, string $source, array $mapping)
    {
        $asset = $this->createMock(AssetInterface::class);

        $asset->method('getCode')->willReturn($code);
        $asset->method('getSourceType')->willReturn($type);
        $asset->method('getSource')->willReturn($source);
        $asset->method('getMapping')->willReturn($mapping);

        /** @var AssetInterface $asset */
        return $asset;
    }

    /**
     * @param array $assets
     * @return MockObject|ListAsset
     */
    private function createList(array $assets)
    {
        $list = $this->createMock(ListAsset::class);

        $list->method('get')->willReturn($assets);

        /** @var ListAsset $list */
        return $list;
    }
}
