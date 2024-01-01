<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Service;

class Environment
{
    public const ENV_DEV     = 'dev';
    public const ENV_PREPROD = 'preprod';
    public const ENV_PROD    = 'prod';

    private array $environments = [
        self::ENV_DEV     => ['color' => '', 'name' => 'Development'],
        self::ENV_PREPROD => ['color' => '', 'name' => 'PreProduction'],
        self::ENV_PROD    => ['color' => '', 'name' => 'Production'],
    ];

    private string $currentCode;

    public function __construct(string $currentCode)
    {
        $this->currentCode = $currentCode;

        $this->setColor(self::ENV_DEV, 'secondary');
        $this->setColor(self::ENV_DEV, 'danger');
        $this->setColor(self::ENV_DEV, 'primary');
    }

    protected function setColor(string $code, string $color): void
    {
        $this->environments[$code]['color'] = $color;
    }

    public function getCurrentCode(): string
    {
        return $this->currentCode;
    }

    public function getCurrentName(): string
    {
        return $this->environments[$this->currentCode]['name'];
    }

    public function getCurrentColor(): string
    {
        return $this->environments[$this->currentCode]['color'];
    }

    public function isProduction(): bool
    {
        return ($this->currentCode === self::ENV_PROD);
    }

    public function isPreproduction(): bool
    {
        return ($this->currentCode === self::ENV_PREPROD);
    }

    public function isDevelopment(): bool
    {
        return ($this->currentCode === self::ENV_DEV);
    }

    public function getEnvironmentSuffix(): string
    {
        if ($this->isProduction()) {
            return '';
        }

        return " [{$this->getCurrentCode()}]";
    }
}
