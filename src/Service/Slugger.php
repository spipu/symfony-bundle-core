<?php

declare(strict_types=1);

namespace Spipu\CoreBundle\Service;

use Spipu\CoreBundle\Service\SluggerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface as SymfonySluggerInterface;

class Slugger implements SluggerInterface
{
    private SymfonySluggerInterface $slugger;

    public function __construct()
    {
        $this->slugger = new AsciiSlugger('en');
    }

    public function slug(string $string, string $separator = '-', string $locale = null): string
    {
        return $this->slugger->slug($string, $separator, $locale)->lower()->toString();
    }
}
