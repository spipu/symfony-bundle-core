<?php
namespace Spipu\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class WebTestCase extends BaseWebTestCase
{
    protected static $dataPrimerInitialized = false;

    public function setUp(): void
    {
        self::bootKernel();

        if (!self::$dataPrimerInitialized) {
            self::$dataPrimerInitialized = true;

            DatabasePrimer::prime(self::$kernel);
            $output = SymfonyMock::getConsoleOutput($this);

            $listFixture = self::$container->get('Spipu\CoreBundle\Fixture\ListFixture');
            $listFixture->get('sample-user')->setMaxSteps(2);
            $listFixture->load($output);
        }
    }

    /**
     * @return string The Kernel class name
     */
    protected static function getKernelClass()
    {
        return Kernel::class;
    }

    /**
     * @param Crawler $crawler
     * @param string $message
     * @return void
     */
    protected function assertCrawlerHasAlert(Crawler $crawler, string $message): void
    {
        $this->assertGreaterThan(
            0,
            $crawler->filter('div[role=alert]:contains("' . $message . '")')->count()
        );
    }

    /**
     * @param Crawler $crawler
     * @param string $fieldName
     * @param string $fieldValue
     */
    protected function assertCrawlerHasFieldValue(Crawler $crawler, string $fieldName, string $fieldValue): void
    {
        $this->assertGreaterThan(
            0,
            $crawler->filter('td[data-field-name=' . $fieldName . ']:contains("'. $fieldValue .'")')->count()
        );
    }

    /**
     * @param Crawler $crawler
     * @param string $inputId
     * @param string $fieldValue
     */
    protected function assertCrawlerHasInputValue(Crawler $crawler, string $inputId, string $fieldValue): void
    {
        $this->assertSame(
            $fieldValue,
            $crawler->filter('input#' . $inputId)->attr('value')
        );
    }

    /**
     * @param Crawler $crawler
     * @param string $message
     * @return void
     */
    protected function assertCrawlerHasFormError(Crawler $crawler, string $message): void
    {
        $this->assertGreaterThan(
            0,
            $crawler->filter('span.form-error-message:contains("' . $message . '")')->count()
        );
    }
}
