<?php
namespace Spipu\CoreBundle\Tests;

use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class WebTestCase extends BaseWebTestCase
{
    protected static $dataPrimerInitialized = false;

    /**
     * @var KernelBrowser
     */
    protected static $clientCache;

    public function setUp(): void
    {
        self::$clientCache = parent::createClient();

        if (!self::$dataPrimerInitialized) {
            self::$dataPrimerInitialized = true;

            DatabasePrimer::prime(self::$kernel);
            $output = SymfonyMock::getConsoleOutput($this);

            $listFixture = self::$container->get('Spipu\CoreBundle\Fixture\ListFixture');
            try {
                $listFixture->get('sample-user')->setMaxSteps(2);
            } catch (Exception $e) {
                // Do Nothing, the fixture does not exists...
            }
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

    /**
     * @param KernelBrowser $client
     * @return void
     */
    protected function assertHasNoEmail(KernelBrowser $client): void
    {
        $this->assertEmailCount(0);
    }

    /**
     * @param KernelBrowser $client
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string|null $bodyContains
     * @return string
     */
    protected function assertHasEmail(
        KernelBrowser $client,
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

    /**
     * @param array $options
     * @param array $server
     * @return KernelBrowser
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        self::$clientCache->restart();

        return self::$clientCache;
    }
}
