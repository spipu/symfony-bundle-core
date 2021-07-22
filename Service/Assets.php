<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\CoreBundle\Service;

use Exception;
use Spipu\CoreBundle\Assets\AssetInterface;
use Spipu\CoreBundle\Assets\ListAsset;
use Symfony\Component\Console\Style\SymfonyStyle;

class Assets
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var FinderFactory
     */
    private $finderFactory;

    /**
     * @var ListAsset
     */
    private $assets;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $targetDir;

    /**
     * Assets constructor.
     * @param Filesystem $filesystem
     * @param FinderFactory $finderFactory
     * @param ListAsset $assets
     * @param string $projectDir
     */
    public function __construct(
        Filesystem $filesystem,
        FinderFactory $finderFactory,
        ListAsset $assets,
        string $projectDir
    ) {
        $this->filesystem = $filesystem;
        $this->finderFactory = $finderFactory;
        $this->assets = $assets;
        $this->projectDir = $projectDir;
    }

    /**
     * @param string $targetArg
     * @return string
     * @throws Exception
     */
    public function setTargetDir(string $targetArg): string
    {
        $targetArg = rtrim($targetArg, '/');

        if ($targetArg === '') {
            $targetArg = $this->getPublicDirectory();
        }

        if (!$this->filesystem->isDir($targetArg)) {
            if (!$this->filesystem->isDir($this->projectDir . DIRECTORY_SEPARATOR . $targetArg)) {
                throw new Exception(
                    sprintf(
                        'The target directory "%s" does not exist.',
                        $targetArg
                    )
                );
            }
            $targetArg = $this->projectDir . DIRECTORY_SEPARATOR . $targetArg;
        }

        $this->targetDir = $targetArg . DIRECTORY_SEPARATOR . 'bundles' . DIRECTORY_SEPARATOR;

        return $this->targetDir;
    }

    /**
     * @return string
     */
    private function getPublicDirectory(): string
    {
        $defaultPublicDir = 'public';

        $composerFilePath = $this->projectDir. DIRECTORY_SEPARATOR . 'composer.json';
        if (!$this->filesystem->isFile($composerFilePath)) {
            return $defaultPublicDir;
        }

        $composerConfig = json_decode($this->filesystem->getContent($composerFilePath), true);
        if (is_array($composerConfig) && isset($composerConfig['extra']['public-dir'])) {
            return $composerConfig['extra']['public-dir'];
        }

        return $defaultPublicDir;
    }

    /**
     * @param SymfonyStyle|null $sfIo
     * @return bool
     * @throws Exception
     */
    public function installAssets(?SymfonyStyle $sfIo = null): bool
    {
        if (!$this->targetDir) {
            throw new Exception('You must set the target dir first');
        }

        $assets = $this->assets->get();
        foreach ($assets as $asset) {
            if ($sfIo !== null) {
                $sfIo->text('  => '.$asset->getCode());
            }
            $this->installAsset($asset);
        }

        return true;
    }

    /**
     * @param AssetInterface $asset
     * @return void
     * @throws Exception
     */
    private function installAsset(AssetInterface $asset): void
    {
        $libDir = $this->targetDir . $asset->getCode() . DIRECTORY_SEPARATOR;

        $this->filesystem->remove($libDir);
        $this->filesystem->mkdir($libDir, 0775);

        switch ($asset->getSourceType()) {
            case AssetInterface::TYPE_VENDOR:
                $this->installAssetFromVendor($asset);
                break;

            case AssetInterface::TYPE_URL:
                $this->installAssetFromUrl($asset);
                break;

            case AssetInterface::TYPE_URL_ZIP:
                $this->installAssetFromZip($asset);
                break;

            default:
                throw new Exception('Invalid Assert Type');
        }
    }

    /**
     * @param AssetInterface $asset
     * @return void
     * @throws Exception
     */
    private function installAssetFromVendor(AssetInterface $asset): void
    {
        $vendorDir = $this->projectDir . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR
            . $asset->getSource() . DIRECTORY_SEPARATOR;

        $libDir = $this->targetDir . $asset->getCode() . DIRECTORY_SEPARATOR;

        $this->installAssetFromPath($vendorDir, $libDir, $asset->getMapping());
    }

    /**
     * @param string $folderFrom
     * @param string $folderTo
     * @param array $mapping
     * @return void
     * @throws Exception
     */
    private function installAssetFromPath(string $folderFrom, string $folderTo, array $mapping): void
    {
        foreach ($mapping as $source => $dest) {
            if ($this->filesystem->isDir($folderFrom . $source)) {
                $this->filesystem->mkdir($folderTo . $dest, 0775);

                $iterator = $this->finderFactory->create();
                $iterator->ignoreDotFiles(false)->in($folderFrom . $source);

                $this->filesystem->mirror($folderFrom . $source, $folderTo . $dest, $iterator);
                continue;
            }

            if ($this->filesystem->isFile($folderFrom . $source)) {
                $this->filesystem->mkdir(dirname($folderTo . $dest), 0775);
                $this->filesystem->copy($folderFrom . $source, $folderTo . $dest);
                continue;
            }

            throw new Exception(sprintf('Invalid asset path %s', $folderFrom . $source));
        }
    }

    /**
     * @param AssetInterface $asset
     * @return void
     * @throws Exception
     */
    private function installAssetFromUrl(AssetInterface $asset): void
    {
        if (!filter_var($asset->getSource(), FILTER_VALIDATE_URL)) {
            throw new Exception('The source must be a url');
        }

        $libDir = $this->targetDir . $asset->getCode() . DIRECTORY_SEPARATOR;
        foreach ($asset->getMapping() as $source => $dest) {
            $this->filesystem->mkdir(dirname($libDir . $dest), 0775);
            $this->filesystem->copy($asset->getSource() . $source, $libDir . $dest);
        }
    }

    /**
     * @param AssetInterface $asset
     * @return void
     * @throws Exception
     */
    private function installAssetFromZip(AssetInterface $asset): void
    {
        if (!filter_var($asset->getSource(), FILTER_VALIDATE_URL)) {
            throw new Exception('The source must be a zip url');
        }

        if (substr($asset->getSource(), -4) !== '.zip') {
            throw new Exception('The source must be a zip url');
        }

        $libDir = $this->targetDir . $asset->getCode() . DIRECTORY_SEPARATOR;

        $zipFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'spipu_core_asset_' . $asset->getCode();
        $zipFolder = $zipFilename.'.extract';

        try {
            $this->filesystem->copy($asset->getSource(), $zipFilename);

            if (!$this->filesystem->unZip($zipFilename, $zipFolder)) {
                throw new Exception('Unable to unzip ' . $zipFilename);
            }

            $this->installAssetFromPath($zipFolder . DIRECTORY_SEPARATOR, $libDir, $asset->getMapping());
        } finally {
            if ($this->filesystem->isFile($zipFilename)) {
                $this->filesystem->remove($zipFilename);
            }
            if ($this->filesystem->isDir($zipFolder)) {
                $this->filesystem->remove($zipFolder);
            }
        }
    }
}
