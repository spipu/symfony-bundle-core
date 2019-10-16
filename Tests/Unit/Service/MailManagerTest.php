<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\MailManager;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\SmtpEnvelope;
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
                    function (Email $email, SmtpEnvelope $envelope = null) {
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
                    function (Email $email, SmtpEnvelope $envelope = null) {
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
}
