<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\MailManager;
use Swift_Mime_SimpleMessage;
use Twig_Environment;

class MailManagerTest extends TestCase
{
    public function testSendHtml()
    {
        $twig = $this->createMock(Twig_Environment::class);
        $twig
            ->expects($this->never())
            ->method('render');

        $mailer = $this->createMock(\Swift_Mailer::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->will(
                $this->returnCallback(
                    function (Swift_Mime_SimpleMessage $message, &$failedRecipients = null) {
                        $this->assertSame(['from@test.fr' => null], $message->getFrom());
                        $this->assertSame(['to_1@test.fr' => null, 'to_2@test.fr' => null], $message->getTo());
                        $this->assertSame('Subject', $message->getSubject());
                        $this->assertSame('text/html', $message->getContentType());
                        $this->assertSame('Message', $message->getBody());
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
        $twig = $this->createMock(Twig_Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('template.html.twig', [])
            ->willReturn('From template');

        $mailer = $this->createMock(\Swift_Mailer::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->will(
                $this->returnCallback(
                    function (Swift_Mime_SimpleMessage $message, &$failedRecipients = null) {
                        $this->assertSame(['from@test.fr' => null], $message->getFrom());
                        $this->assertSame(['to_1@test.fr' => null, 'to_2@test.fr' => null], $message->getTo());
                        $this->assertSame('Subject', $message->getSubject());
                        $this->assertSame('text/html', $message->getContentType());
                        $this->assertSame('From template', $message->getBody());
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
