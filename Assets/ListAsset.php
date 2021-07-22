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

use Exception;

/**
 * All the assets
 */
class ListAsset
{
    /**
     * @var AssetInterface[]
     */
    private $assets = [];

    /**
     * ListAsset constructor.
     * @param iterable $list
     * @throws Exception
     */
    public function __construct(iterable $list)
    {
        foreach ($list as $asset) {
            $this->addAsset($asset);
        }

        $this->orderAssets();
    }

    /**
     * @param AssetInterface $asset
     * @return void
     * @throws Exception
     */
    private function addAsset(AssetInterface $asset): void
    {
        $code = $asset->getCode();

        if (!preg_match('/^[a-z0-9\-]+$/', $code)) {
            throw new Exception('Invalid asset code');
        }

        $this->assets[$code] = $asset;
    }

    /**
     * @return void
     */
    private function orderAssets(): void
    {
        ksort($this->assets);
    }

    /**
     * @return AssetInterface[]
     */
    public function get(): array
    {
        return $this->assets;
    }
}
