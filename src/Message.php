<?php

declare(strict_types=1);

namespace Yiisoft\Mailer\SwiftMailer;

use Swift_Attachment;
use Swift_EmbeddedFile;
use Swift_Message;
use Swift_Mime_MimePart;
use Swift_Signer;
use Throwable;
use Yiisoft\Mailer\MessageInterface;

use function reset;

/**
 * Message implements a message class based on SwiftMailer.
 *
 * @see https://swiftmailer.symfony.com/docs/messages.html
 * @see Mailer
 */
final class Message implements MessageInterface
{
    private Swift_Message $swiftMessage;
    private ?Throwable $error = null;

    public function __construct()
    {
        $this->swiftMessage = new Swift_Message();
    }

    public function __clone()
    {
        $this->swiftMessage = clone $this->swiftMessage;
    }

    /**
     * @return Swift_Message Swift message instance.
     */
    public function getSwiftMessage(): Swift_Message
    {
        return $this->swiftMessage;
    }

    public function getCharset(): string
    {
        return $this->swiftMessage->getCharset();
    }

    public function withCharset(string $charset): self
    {
        $new = clone $this;
        $new->swiftMessage->setCharset($charset);
        return $new;
    }

    public function getFrom()
    {
        return $this->swiftMessage->getFrom() ?? '';
    }

    public function withFrom($from): self
    {
        $new = clone $this;
        $new->swiftMessage->setFrom($from);
        return $new;
    }

    public function getTo()
    {
        return $this->swiftMessage->getTo() ?? '';
    }

    public function withTo($to): self
    {
        $new = clone $this;
        $new->swiftMessage->setTo($to);
        return $new;
    }

    public function getReplyTo()
    {
        return $this->swiftMessage->getReplyTo() ?? '';
    }

    public function withReplyTo($replyTo): self
    {
        $new = clone $this;
        $new->swiftMessage->setReplyTo($replyTo);
        return $new;
    }

    public function getCc()
    {
        return $this->swiftMessage->getCc() ?? '';
    }

    public function withCc($cc): self
    {
        $new = clone $this;
        $new->swiftMessage->setCc($cc);
        return $new;
    }

    public function getBcc()
    {
        return $this->swiftMessage->getBcc() ?? '';
    }

    public function withBcc($bcc): self
    {
        $new = clone $this;
        $new->swiftMessage->setBcc($bcc);
        return $new;
    }

    public function getSubject(): string
    {
        return (string) $this->swiftMessage->getSubject() ?? '';
    }

    public function withSubject(string $subject): self
    {
        $new = clone $this;
        $new->swiftMessage->setSubject($subject);
        return $new;
    }

    public function getTextBody(): string
    {
        return $this->swiftMessage->getBody() ?? '';
    }

    public function withTextBody(string $text): self
    {
        $new = clone $this;
        $new->setBody($text, 'text/plain');
        return $new;
    }

    public function getHtmlBody(): string
    {
        return $this->swiftMessage->getBody() ?? '';
    }

    public function withHtmlBody(string $html): self
    {
        $new = clone $this;
        $new->setBody($html, 'text/html');
        return $new;
    }

    public function withAttached(string $fileName, array $options = []): self
    {
        $attachment = Swift_Attachment::fromPath($fileName);

        if (!empty($options['fileName'])) {
            $attachment->setFilename($options['fileName']);
        }

        if (!empty($options['contentType'])) {
            $attachment->setContentType($options['contentType']);
        }

        $new = clone $this;
        $new->swiftMessage->attach($attachment);
        return $new;
    }

    public function withAttachedContent(string $content, array $options = []): self
    {
        $attachment = new Swift_Attachment($content);

        if (!empty($options['fileName'])) {
            $attachment->setFilename($options['fileName']);
        }

        if (!empty($options['contentType'])) {
            $attachment->setContentType($options['contentType']);
        }

        $new = clone $this;
        $new->swiftMessage->attach($attachment);
        return $new;
    }

    public function embed(string $fileName, array $options = []): string
    {
        $embedFile = Swift_EmbeddedFile::fromPath($fileName);

        if (!empty($options['fileName'])) {
            $embedFile->setFilename($options['fileName']);
        }

        if (!empty($options['contentType'])) {
            $embedFile->setContentType($options['contentType']);
        }

        $this->swiftMessage = clone $this->swiftMessage;
        return $this->swiftMessage->embed($embedFile);
    }

    public function embedContent(string $content, array $options = []): string
    {
        $embedFile = new Swift_EmbeddedFile($content);

        if (!empty($options['fileName'])) {
            $embedFile->setFilename($options['fileName']);
        }

        if (!empty($options['contentType'])) {
            $embedFile->setContentType($options['contentType']);
        }

        $this->swiftMessage = clone $this->swiftMessage;
        return $this->swiftMessage->embed($embedFile);
    }

    public function getHeader(string $name): array
    {
        $headerSet = $this->swiftMessage->getHeaders();

        if (!$headerSet->has($name)) {
            return [];
        }

        $headers = [];

        foreach ($headerSet->getAll($name) as $header) {
            $headers[] = $header->getValue();
        }

        return $headers;
    }

    public function withAddedHeader(string $name, string $value): self
    {
        $new = clone $this;
        $new->swiftMessage->getHeaders()->addTextHeader($name, $value);
        return $new;
    }

    public function withHeader(string $name, $value): self
    {
        $new = clone $this;
        $headerSet = $new->swiftMessage->getHeaders();

        if ($headerSet->has($name)) {
            $headerSet->remove($name);
        }

        foreach ((array) $value as $v) {
            $headerSet->addTextHeader($name, $v);
        }

        return $new;
    }

    public function withHeaders(array $headers): self
    {
        $new = clone $this;

        foreach ($headers as $name => $value) {
            $new = $new->withHeader($name, $value);
        }

        return $new;
    }

    public function getError(): ?Throwable
    {
        return $this->error;
    }

    public function withError(Throwable $e): self
    {
        $new = clone $this;
        $new->error = $e;
        return $new;
    }

    public function __toString(): string
    {
        return $this->swiftMessage->toString();
    }

    /**
     * Returns the return-path (the bounce address) of this message.
     *
     * @return string The bounce email address.
     */
    public function getReturnPath(): string
    {
        return $this->swiftMessage->getReturnPath() ?? '';
    }

    /**
     * Set the return-path (the bounce address) of this message.
     *
     * @param string $address The bounce email address.
     *
     * @return self
     */
    public function withReturnPath(string $address): self
    {
        $new = clone $this;
        $new->swiftMessage->setReturnPath($address);
        return $new;
    }

    /**
     * Returns the priority of this message.
     *
     * @return int The priority value as integer in range: `1..5`,
     * where 1 is the highest priority and 5 is the lowest.
     */
    public function getPriority(): int
    {
        return (int) $this->swiftMessage->getPriority();
    }

    /**
     * Set the priority of this message.
     *
     * @param int $priority The priority value, should be an integer in range: `1..5`,
     * where 1 is the highest priority and 5 is the lowest.
     *
     * @return self
     */
    public function withPriority(int $priority): self
    {
        $new = clone $this;
        $new->swiftMessage->setPriority($priority);
        return $new;
    }

    /**
     * Get the addresses to which a read-receipt will be sent.
     *
     * @return array|string The receipt receive email addresses.
     */
    public function getReadReceiptTo()
    {
        return $this->swiftMessage->getReadReceiptTo() ?? '';
    }

    /**
     * Sets the ask for a delivery receipt from the recipient to be sent to $addresses.
     *
     * @param array|string $addresses The receipt receive email address(es).
     *
     * @return self
     */
    public function withReadReceiptTo($addresses): self
    {
        $new = clone $this;
        $new->swiftMessage->setReadReceiptTo((array) $addresses);
        return $new;
    }

    /**
     * Attaches signers.
     *
     * @param Swift_Signer[] $signers
     *
     * @return self
     */
    public function withAttachedSigners(array $signers): self
    {
        $new = clone $this;

        foreach ($signers as $signer) {
            $new->swiftMessage->attachSigner($signer);
        }

        return $new;
    }

    /**
     * Sets the message body.
     *
     * If body is already set and its content type matches given one, it will
     * be overridden, if content type miss match the multipart message will be composed.
     *
     * @param string $body The body content.
     * @param string $contentType The body content type.
     */
    private function setBody(string $body, string $contentType): void
    {
        $oldBody = $this->swiftMessage->getBody();
        $charset = $this->swiftMessage->getCharset();

        if (!empty($oldBody)) {
            $oldContentType = $this->swiftMessage->getContentType();

            if ($oldContentType === $contentType) {
                $this->swiftMessage->setBody($body, $contentType);
                return;
            }

            $this->swiftMessage->setBody(null);
            $this->swiftMessage->setContentType('');
            $this->swiftMessage->addPart($oldBody, $oldContentType, $charset);
            $this->swiftMessage->addPart($body, $contentType, $charset);
            return;
        }

        $parts = $this->swiftMessage->getChildren();
        $partFound = false;

        foreach ($parts as $key => $part) {
            if ($part instanceof Swift_Mime_MimePart) {
                if ($part->getContentType() === $contentType) {
                    $charset = $part->getCharset();
                    unset($parts[$key]);
                    $partFound = true;
                    break;
                }
            }
        }

        if (!$partFound) {
            $this->swiftMessage->setBody($body, $contentType);
            return;
        }

        reset($parts);
        $this->swiftMessage->setChildren($parts);
        $this->swiftMessage->addPart($body, $contentType, $charset);
    }
}
