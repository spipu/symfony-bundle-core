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

namespace Spipu\CoreBundle\Assets;

interface AssetInterface
{
    public const TYPE_VENDOR = 'vendor';
    public const TYPE_URL = 'url';
    public const TYPE_URL_ZIP = 'zip';
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return string
     */
    public function getSourceType(): string;

    /**
     * @return string
     */
    public function getSource(): string;

    /**
     * @return string[]
     */
    public function getMapping(): array;
}
