<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spipu\CoreBundle\Tests;

use App\Kernel as AppKernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class Kernel extends AppKernel
{
    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var-test/cache';
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var-test/log';
    }

    /**
     * @param ContainerConfigurator $container
     */
    protected function configureContainer(ContainerConfigurator $container): void
    {
        parent::configureContainer($container);

        $container->parameters()->set('APP_SETTINGS_DATABASE_URL', 'sqlite:///%kernel.project_dir%/var-test/test.sqlite');
    }
}
