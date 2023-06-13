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

use ZipArchive;

class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    public function isDir(string $filename): bool
    {
        if (!$this->exists($filename)) {
            return false;
        }

        return is_dir($filename);
    }

    public function isFile(string $filename): bool
    {
        if (!$this->exists($filename)) {
            return false;
        }

        return is_file($filename);
    }

    public function getContent(string $filename): string
    {
        return file_get_contents($filename);
    }

    public function unZip(string $zipFilename, string $folderDestination): bool
    {
        $zip = new ZipArchive();
        $zip->open($zipFilename);
        $result = $zip->extractTo($folderDestination);
        $zip->close();

        return $result;
    }
}
