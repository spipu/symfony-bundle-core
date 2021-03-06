<?php
namespace Spipu\CoreBundle\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Fixture\ListFixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

trait WebTestCaseTrait
{
    protected static $dataPrimerInitialized = false;

    /**
     * @param KernelInterface $kernel
     * @param ContainerInterface $container
     */
    protected function prepareDataPrimer(KernelInterface $kernel, ContainerInterface $container): void
    {
        if (!self::$dataPrimerInitialized) {
            self::$dataPrimerInitialized = true;

            DatabasePrimer::prime($kernel);

            $this->loadFixtures($container);
        }
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    protected function loadFixtures(ContainerInterface $container)
    {
        /** @var ListFixture $listFixture */
        $listFixture = $container->get('Spipu\CoreBundle\Fixture\ListFixture');
        $this->setupFixtures($listFixture);

        /** @var TestCase $this */
        $output = SymfonyMock::getConsoleOutput($this);
        $listFixture->load($output);
    }

    /**
     * @param ListFixture $listFixture
     * @return void
     */
    protected function setupFixtures(ListFixture $listFixture)
    {
        try {
            $listFixture->get('sample-user')->setMaxSteps(2);
        } catch (Exception $e) {
            // Do Nothing, the fixture does not exists...
        }
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

    /**
     * @return void
     */
    protected function assertHasNoEmail(): void
    {
        $this->assertEmailCount(0);
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string|null $bodyContains
     * @return string
     */
    protected function assertHasEmail(
        string $from,
        string $to,
        string $subject,
        string $bodyContains = null
    ): String {
        $this->assertEmailCount(1);

        $email = $this->getMailerMessage(0);

        $this->assertEmailHeaderSame($email, 'From', $from);
        $this->assertEmailHeaderSame($email, 'To', $to);
        $this->assertEmailHeaderSame($email, 'Subject', $subject);

        if ($bodyContains !== null) {
            $this->assertEmailTextBodyContains($email, $bodyContains);
        }

        return $email->getHtmlBody();
    }
}
