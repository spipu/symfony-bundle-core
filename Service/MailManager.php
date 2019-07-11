<?php
/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Spipu\CoreBundle\Service;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Twig_Environment;

class MailManager
{
    const MAIL_SEPARATOR = ',';

    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $twig;

    /**
     * MailManager constructor.
     * @param  \Swift_Mailer $mailer
     * @param Twig_Environment $twig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        Twig_Environment $twig
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param string $subject
     * @param string $sender
     * @param string $receiver
     * @param string $twigTemplate
     * @param array $twigParameters
     * @return void
     * @throws \Exception
     */
    public function sendTwigMail(
        string $subject,
        string $sender,
        string $receiver,
        string $twigTemplate,
        array $twigParameters = []
    ) : void {
        $body = $this->twig->render($twigTemplate, $twigParameters);

        $this->sendHtmlMail($subject, $sender, $receiver, $body);
    }

    /**
     * @param string $subject
     * @param string $sender
     * @param string $receiver
     * @param string $body
     * @return void
     */
    public function sendHtmlMail(string $subject, string $sender, string $receiver, string $body): void
    {
        $receivers = explode(static::MAIL_SEPARATOR, $receiver);

        $message = (new \Swift_Message($subject))
            ->setFrom($sender)
            ->setTo($receivers)
            ->setBody(
                $body,
                'text/html'
            );

        $this->mailer->send($message);
    }
}
