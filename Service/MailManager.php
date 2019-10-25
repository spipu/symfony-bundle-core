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

use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment as TwigEnvironment;
use Twig\Error\Error as TwigError;

class MailManager
{
    const MAIL_SEPARATOR = ',';

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TwigEnvironment
     */
    private $twig;

    /**
     * MailManager constructor.
     * @param MailerInterface $mailer
     * @param TwigEnvironment $twig
     */
    public function __construct(
        MailerInterface $mailer,
        TwigEnvironment $twig
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param string $subject
     * @param string $sender
     * @param mixed $receiver
     * @param string $twigTemplate
     * @param array $twigParameters
     * @return void
     * @throws TransportExceptionInterface
     * @throws TwigError
     */
    public function sendTwigMail(
        string $subject,
        string $sender,
        $receiver,
        string $twigTemplate,
        array $twigParameters = []
    ) : void {
        $body = $this->twig->render($twigTemplate, $twigParameters);

        $this->sendHtmlMail($subject, $sender, $receiver, $body);
    }

    /**
     * @param string $subject
     * @param string $sender
     * @param mixed $receiver
     * @param string $body
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendHtmlMail(string $subject, string $sender, $receiver, string $body): void
    {
        $receiverList = $this->prepareEmailAddress($receiver);

        $message = (new Email())
            ->from($sender)
            ->to(...$receiverList)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text(strip_tags($body))
            ->html($body);

        $this->mailer->send($message);
    }

    /**
     * @param mixed $values
     * @return Address[]
     */
    public function prepareEmailAddress($values): array
    {
        $list = $this->convertAddressToArray($values);

        foreach ($list as $key => $value) {
            if (is_string($value)) {
                $list[$key] = new Address($value);
                continue;
            }

            if (!is_object($value) || !($value instanceof Address)) {
                throw new InvalidArgumentException('The provided value is not a valid address');
            }
        }

        return $list;
    }

    /**
     * @param mixed $values
     * @return array
     */
    private function convertAddressToArray($values): array
    {
        if (is_string($values)) {
            return explode(static::MAIL_SEPARATOR, $values);
        }

        if (is_object($values) && $values instanceof Address) {
            return [$values];
        }

        if (is_array($values)) {
            return $values;
        }

        throw new InvalidArgumentException('The provided value is not a valid address');
    }
}
