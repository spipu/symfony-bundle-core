<?php
namespace Spipu\CoreBundle\Tests;

use App\Kernel as AppKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Kernel extends AppKernel
{
    /**
     * @var string
     */
    private $projectDir;

    public function getProjectDir()
    {
        if (null === $this->projectDir) {
            $this->projectDir = dirname(dirname(__DIR__));

            while (!is_file($this->projectDir . '/composer.json')) {
                $this->projectDir = dirname($this->projectDir);
            }
        }

        return $this->projectDir;
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var-test/cache';
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var-test/log';
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        parent::configureContainer($container, $loader);

        $container->setParameter('APP_SETTINGS_DATABASE_URL', 'sqlite:///%kernel.project_dir%/var-test/test.sqlite');
    }
}
