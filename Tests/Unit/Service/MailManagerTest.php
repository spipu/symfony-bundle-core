<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\MailManager;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment as TwigEnvironment;

class MailManagerTest extends TestCase
{
    private function validateAddresses($expected, $addresses)
    {
        $this->assertSame(count($expected), count($addresses));

        /** @var Address[] $addresses */
        foreach ($addresses as $key => $address) {
            $this->assertSame($expected[$key], $address->getAddress());
        }
    }

    public function testSendHtml()
    {
        $twig = $this->createMock(TwigEnvironment::class);
        $twig
            ->expects($this->never())
            ->method('render');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->will(
                $this->returnCallback(
                    function (Email $email, Envelope $envelope = null) {
                        $this->validateAddresses(['from@test.fr'], $email->getFrom());
                        $this->validateAddresses(['to_1@test.fr', 'to_2@test.fr'], $email->getTo());
                        $this->assertSame('Subject', $email->getSubject());
                        $this->assertSame('Message', $email->getHtmlBody());
                    }
                )
            );

        $service = new MailManager($mailer, $twig);
        $service->sendHtmlMail(
            'Subject',
            'from@test.fr',
            'to_1@test.fr,to_2@test.fr',
            'Message'
        );
    }

    public function testSendTwig()
    {
        $twig = $this->createMock(TwigEnvironment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('template.html.twig', [])
            ->willReturn('From template');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->will(
                $this->returnCallback(
                    function (Email $email, Envelope $envelope = null) {
                        $this->validateAddresses(['from@test.fr'], $email->getFrom());
                        $this->validateAddresses(['to_1@test.fr', 'to_2@test.fr'], $email->getTo());
                        $this->assertSame('Subject', $email->getSubject());
                        $this->assertSame('From template', $email->getHtmlBody());
                    }
                )
            );

        $service = new MailManager($mailer, $twig);
        $service->sendTwigMail(
            'Subject',
            'from@test.fr',
            'to_1@test.fr,to_2@test.fr',
            'template.html.twig'
        );
    }

    public function testPrepareAddressesOk()
    {
        $address1 = new Address('test1@test.fr');
        $address2 = new Address('test2@test.fr');

        $twig   = $this->createMock(TwigEnvironment::class);
        $mailer = $this->createMock(MailerInterface::class);

        $service = new MailManager($mailer, $twig);

        $result = $service->prepareEmailAddresses($address1->getAddress());
        $this->assertIsArray($result);
        $this->assertSame(1, count($result));
        $this->assertSame($address1->getAddress(), $result[0]->getAddress());

        $result = $service->prepareEmailAddresses($address1->getAddress() . ',' . $address2->getAddress());
        $this->assertIsArray($result);
        $this->assertSame(2, count($result));
        $this->assertSame($address1->getAddress(), $result[0]->getAddress());
        $this->assertSame($address2->getAddress(), $result[1]->getAddress());

        $result = $service->prepareEmailAddresses($address1);
        $this->assertIsArray($result);
        $this->assertSame(1, count($result));
        $this->assertSame($address1, $result[0]);

        $result = $service->prepareEmailAddresses([$address1, $address2]);
        $this->assertIsArray($result);
        $this->assertSame(2, count($result));
        $this->assertSame($address1, $result[0]);
        $this->assertSame($address2, $result[1]);

        $result = $service->prepareEmailAddresses([$address1, $address2->getAddress()]);
        $this->assertIsArray($result);
        $this->assertSame(2, count($result));
        $this->assertSame($address1, $result[0]);
        $this->assertSame($address2->getAddress(), $result[1]->getAddress());
    }

    public function testPrepareAddressesErrorBadTypeObject()
    {
        $address = new \StdClass();

        $twig   = $this->createMock(TwigEnvironment::class);
        $mailer = $this->createMock(MailerInterface::class);

        $service = new MailManager($mailer, $twig);

        $this->expectException(InvalidArgumentException::class);
        $service->prepareEmailAddresses($address);
    }

    public function testPrepareAddressesErrorBadTypeInt()
    {
        $address = 10;

        $twig   = $this->createMock(TwigEnvironment::class);
        $mailer = $this->createMock(MailerInterface::class);

        $service = new MailManager($mailer, $twig);

        $this->expectException(InvalidArgumentException::class);
        $service->prepareEmailAddresses($address);
    }

    public function testPrepareAddressesErrorBadTypeArray()
    {
        $addresses = ['test@test.fr', new \StdClass()];

        $twig   = $this->createMock(TwigEnvironment::class);
        $mailer = $this->createMock(MailerInterface::class);

        $service = new MailManager($mailer, $twig);

        $this->expectException(InvalidArgumentException::class);
        $service->prepareEmailAddresses($addresses);
    }
}
