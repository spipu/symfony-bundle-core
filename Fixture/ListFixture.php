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

namespace Spipu\CoreBundle\Fixture;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;

class ListFixture
{
    /**
     * @var FixtureInterface[]
     */
    private array $fixtures = [];

    public function __construct(iterable $list)
    {
        foreach ($list as $fixture) {
            $this->addFixture($fixture);
        }

        $this->orderFixtures();
    }

    private function addFixture(FixtureInterface $fixture): void
    {
        $this->fixtures[$fixture->getCode()] = $fixture;
    }

    private function orderFixtures(): void
    {
        uasort($this->fixtures, [$this, 'sortFixtures']);
    }

    /**
     * @param FixtureInterface $rowA
     * @param FixtureInterface $rowB
     * @return int
     * @SuppressWarnings(PMD.UnusedPrivateMethod)
     */
    private function sortFixtures(FixtureInterface $rowA, FixtureInterface $rowB): int
    {
        return $rowA->getOrder() <=> $rowB->getOrder();
    }

    public function get(string $code): FixtureInterface
    {
        if (!array_key_exists($code, $this->fixtures)) {
            throw new Exception('Unknown fixture code');
        }

        return $this->fixtures[$code];
    }

    public function load(OutputInterface $output): void
    {
        foreach ($this->fixtures as $fixture) {
            $fixture->load($output);
        }
    }

    public function remove(OutputInterface $output): void
    {
        foreach ($this->fixtures as $fixture) {
            $fixture->remove($output);
        }
    }
}
