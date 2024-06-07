<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Service;

interface EnvironmentInterface
{
    public function getCurrentCode(): string;

    public function getCurrentName(): string;

    public function isProduction(): bool;

    public function isPreproduction(): bool;

    public function isDevelopment(): bool;

    public function getEnvironmentSuffix(): string;
}
