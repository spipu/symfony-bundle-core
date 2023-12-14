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

namespace Spipu\CoreBundle\Service;

use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment as TwigEnvironment;

class MailManager
{
    public const MAIL_SEPARATOR = ',';

    private MailerInterface $mailer;
    private TwigEnvironment $twig;

    public function __construct(
        MailerInterface $mailer,
        TwigEnvironment $twig
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendTwigMail(
        string $subject,
        string $sender,
        mixed $receiver,
        string $twigTemplate,
        array $twigParameters = []
    ): void {
        $body = $this->twig->render($twigTemplate, $twigParameters);

        $this->sendHtmlMail($subject, $sender, $receiver, $body);
    }

    public function sendHtmlMail(string $subject, string $sender, mixed $receiver, string $body): void
    {
        $message = $this->prepareHtmlMailMessage($sender, $receiver, $subject, $body);

        $this->sendMail($message);
    }

    public function sendMail(Email $message): void
    {
        $this->mailer->send($message);
    }

    public function prepareHtmlMailMessage(string $sender, mixed $receiver, string $subject, string $body): Email
    {
        $senderAddress = $this->prepareEmailAddress($sender);
        $receiverAddresses = $this->prepareEmailAddresses($receiver);

        return (new Email())
            ->from($senderAddress)
            ->to(...$receiverAddresses)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text(strip_tags($body))
            ->html($body);
    }

    public function prepareEmailAddresses(mixed $values): array
    {
        $list = $this->convertAddressToArray($values);

        foreach ($list as $key => $value) {
            $list[$key] = $this->prepareEmailAddress($value);
        }

        return $list;
    }

    /**
     * @param mixed $value
     * @return Address
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function prepareEmailAddress(mixed $value): Address
    {
        if (is_string($value)) {
            $value = Address::create($value);
        }

        if (!is_object($value) || !($value instanceof Address)) {
            throw new InvalidArgumentException('The provided value is not a valid address');
        }

        return $value;
    }

    private function convertAddressToArray(mixed $values): array
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
