<?php
namespace Spipu\CoreBundle\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\Filesystem;
use Spipu\CoreBundle\Service\FinderFactory;
use Spipu\CoreBundle\Service\MailManager;
use Symfony\Component\Finder\Finder;

class SpipuCoreMock extends TestCase
{
    /**
     * @param TestCase $testCase
     * @return MockObject|Filesystem
     */
    public static function getFilesystem(TestCase $testCase)
    {
        $filesystem = $testCase->createMock(Filesystem::class);

        /** @var Filesystem $filesystem */
        return $filesystem;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|FinderFactory
     */
    public static function getFinderFactory(TestCase $testCase)
    {
        $finder = $testCase->createMock(Finder::class);
        $finder->method('directories')->willReturn($finder);
        $finder->method('files')->willReturn($finder);
        $finder->method('depth')->willReturn($finder);
        $finder->method('date')->willReturn($finder);
        $finder->method('name')->willReturn($finder);
        $finder->method('ignoreDotFiles')->willReturn($finder);
        $finder->method('ignoreVCS')->willReturn($finder);
        $finder->method('ignoreUnreadableDirs')->willReturn($finder);
        $finder->method('filter')->willReturn($finder);
        $finder->method('followLinks')->willReturn($finder);
        $finder->method('in')->willReturn($finder);

        $finderFactory = $testCase->createMock(FinderFactory::class);
        $finderFactory->method('create')->willReturn($finder);

        /** @var FinderFactory $finderFactory */
        return $finderFactory;
    }

    /**
     * @param TestCase $testCase
     * @return MockObject|MailManager
     */
    public static function getMailManager(TestCase $testCase)
    {
        $mailManager = $testCase->createMock(MailManager::class);

        /** @var MailManager $mailManager */
        return $mailManager;
    }
}
