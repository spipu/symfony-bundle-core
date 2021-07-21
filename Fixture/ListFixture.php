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

/**
 * All the fixtures
 */
class ListFixture
{
    /**
     * @var FixtureInterface[]
     */
    private $fixtures = [];

    /**
     * ListFixture constructor.
     * @param iterable $list
     */
    public function __construct(iterable $list)
    {
        foreach ($list as $fixture) {
            $this->addFixture($fixture);
        }

        $this->orderFixtures();
    }

    /**
     * @param FixtureInterface $fixture
     * @return void
     */
    private function addFixture(FixtureInterface $fixture): void
    {
        $this->fixtures[$fixture->getCode()] = $fixture;
    }

    /**
     * @return void
     */
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

    /**
     * @param string $code
     * @return FixtureInterface
     * @throws Exception
     */
    public function get(string $code): FixtureInterface
    {
        if (!array_key_exists($code, $this->fixtures)) {
            throw new Exception('Unknown fixture code');
        }

        return $this->fixtures[$code];
    }

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function load(OutputInterface $output): void
    {
        foreach ($this->fixtures as $fixture) {
            $fixture->load($output);
        }
    }

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function remove(OutputInterface $output): void
    {
        foreach ($this->fixtures as $fixture) {
            $fixture->remove($output);
        }
    }
}
