<?php

namespace Yiisoft\Mailer\SwiftMailer;

use Yiisoft\Mailer\BaseMessage;
use Yiisoft\Mailer\MessageInterface;

/**
 * Message implements a message class based on SwiftMailer.
 *
 * @see http://swiftmailer.org/docs/messages.html
 * @see Mailer
 */
class Message extends BaseMessage
{
    /**
     * @var \Swift_Message Swift message instance.
     */
    private $swiftMessage;

    /**
     * @return \Swift_Message Swift message instance.
     */
    public function getSwiftMessage(): \Swift_Message
    {
        return $this->swiftMessage;
    }

    public function __construct()
    {
        $this->swiftMessage = new \Swift_Message();
    }

    /**
     * This method is called after the object is created by cloning an existing one.
     * It ensures [[swiftMessage]] is also cloned.
     */
    public function __clone()
    {
        if (is_object($this->swiftMessage)) {
            $this->swiftMessage = clone $this->swiftMessage;
        }
    }

    public function getCharset(): string
    {
        return $this->swiftMessage->getCharset();
    }

    public function setCharset(string $charset): MessageInterface
    {
        $this->swiftMessage->setCharset($charset);

        return $this;
    }

    public function getFrom()
    {
        return $this->swiftMessage->getFrom();
    }

    public function setFrom($from): MessageInterface
    {
        $this->swiftMessage->setFrom($from);

        return $this;
    }

    public function getReplyTo()
    {
        return $this->swiftMessage->getReplyTo();
    }

    public function setReplyTo($replyTo): MessageInterface
    {
        $this->swiftMessage->setReplyTo($replyTo);

        return $this;
    }

    public function getTo()
    {
        return $this->swiftMessage->getTo();
    }

    public function setTo($to): MessageInterface
    {
        $this->swiftMessage->setTo($to);

        return $this;
    }

    public function getCc()
    {
        return $this->swiftMessage->getCc();
    }

    public function setCc($cc): MessageInterface
    {
        $this->swiftMessage->setCc($cc);

        return $this;
    }

    public function getBcc()
    {
        return $this->swiftMessage->getBcc();
    }

    public function setBcc($bcc): MessageInterface
    {
        $this->swiftMessage->setBcc($bcc);

        return $this;
    }

    public function getSubject(): string
    {
        return $this->swiftMessage->getSubject();
    }

    public function setSubject(string $subject): MessageInterface
    {
        $this->swiftMessage->setSubject($subject);

        return $this;
    }

    public function getTextBody(): string
    {
        return $this->swiftMessage->getBody();
    }

    public function setTextBody(string $text): MessageInterface
    {
        $this->setBody($text, 'text/plain');

        return $this;
    }

    public function getHtmlBody(): string
    {
        return $this->swiftMessage->getBody();
    }

    public function setHtmlBody(string $html): MessageInterface
    {
        $this->setBody($html, 'text/html');

        return $this;
    }

    /**
     * Sets the message body.
     * If body is already set and its content type matches given one, it will
     * be overridden, if content type miss match the multipart message will be composed.
     *
     * @param string $body        body content.
     * @param string $contentType body content type.
     */
    protected function setBody(string $body, string $contentType): void
    {
        $message = $this->swiftMessage;
        $oldBody = $message->getBody();
        $charset = $message->getCharset();
        if (empty($oldBody)) {
            $parts = $message->getChildren();
            $partFound = false;
            foreach ($parts as $key => $part) {
                if (!($part instanceof \Swift_Mime_Attachment)) {
                    /* @var $part \Swift_Mime_MimePart */
                    if ($part->getContentType() == $contentType) {
                        $charset = $part->getCharset();
                        unset($parts[$key]);
                        $partFound = true;
                        break;
                    }
                }
            }
            if ($partFound) {
                reset($parts);
                $message->setChildren($parts);
                $message->addPart($body, $contentType, $charset);
            } else {
                $message->setBody($body, $contentType);
            }
        } else {
            $oldContentType = $message->getContentType();
            if ($oldContentType == $contentType) {
                $message->setBody($body, $contentType);
            } else {
                $message->setBody(null);
                $message->setContentType(null);
                $message->addPart($oldBody, $oldContentType, $charset);
                $message->addPart($body, $contentType, $charset);
            }
        }
    }

    public function attach(string $fileName, array $options = []): MessageInterface
    {
        $attachment = \Swift_Attachment::fromPath($fileName);
        if (!empty($options['fileName'])) {
            $attachment->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $attachment->setContentType($options['contentType']);
        }
        $this->swiftMessage->attach($attachment);

        return $this;
    }

    public function attachContent(string $content, array $options = []): MessageInterface
    {
        $attachment = new \Swift_Attachment($content);
        if (!empty($options['fileName'])) {
            $attachment->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $attachment->setContentType($options['contentType']);
        }
        $this->swiftMessage->attach($attachment);

        return $this;
    }

    public function embed(string $fileName, array $options = []): string
    {
        $embedFile = \Swift_EmbeddedFile::fromPath($fileName);
        if (!empty($options['fileName'])) {
            $embedFile->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $embedFile->setContentType($options['contentType']);
        }

        return $this->swiftMessage->embed($embedFile);
    }

    public function embedContent(string $content, array $options = []): string
    {
        $embedFile = new \Swift_EmbeddedFile($content);
        if (!empty($options['fileName'])) {
            $embedFile->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $embedFile->setContentType($options['contentType']);
        }

        return $this->swiftMessage->embed($embedFile);
    }

    public function toString(): string
    {
        return $this->swiftMessage->toString();
    }

    public function addHeader(string $name, string $value): MessageInterface
    {
        $this->swiftMessage->getHeaders()->addTextHeader($name, $value);

        return $this;
    }

    public function setHeader(string $name, $value): MessageInterface
    {
        $headerSet = $this->swiftMessage->getHeaders();

        if ($headerSet->has($name)) {
            $headerSet->remove($name);
        }

        foreach ((array)$value as $v) {
            $headerSet->addTextHeader($name, $v);
        }

        return $this;
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

    public function setHeaders(array $headers): MessageInterface
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     * Returns the return-path (the bounce address) of this message.
     *
     * @return string the bounce email address.
     */
    public function getReturnPath(): string
    {
        return $this->swiftMessage->getReturnPath();
    }

    /**
     * Set the return-path (the bounce address) of this message.
     *
     * @param string $address the bounce email address.
     *
     * @return $this self reference.
     */
    public function setReturnPath(string $address): MessageInterface
    {
        $this->swiftMessage->setReturnPath($address);

        return $this;
    }

    /**
     * Returns the priority of this message.
     *
     * @return int priority value as integer in range: `1..5`,
     * where 1 is the highest priority and 5 is the lowest.
     */
    public function getPriority(): int
    {
        return $this->swiftMessage->getPriority();
    }

    /**
     * Set the priority of this message.
     *
     * @param int $priority priority value, should be an integer in range: `1..5`,
     * where 1 is the highest priority and 5 is the lowest.
     *
     * @return $this self reference.
     */
    public function setPriority(int $priority): MessageInterface
    {
        $this->swiftMessage->setPriority($priority);

        return $this;
    }

    /**
     * Get the addresses to which a read-receipt will be sent.
     *
     * @return string|array receipt receive email addresses.
     */
    public function getReadReceiptTo()
    {
        return $this->swiftMessage->getReadReceiptTo();
    }

    /**
     * Sets the ask for a delivery receipt from the recipient to be sent to $addresses.
     *
     * @param string|array $addresses receipt receive email address(es).
     *
     * @return $this self reference.
     */
    public function setReadReceiptTo($addresses): MessageInterface
    {
        $this->swiftMessage->setReadReceiptTo($addresses);

        return $this;
    }

    /**
     * Attaches signers.
     *
     * @param \Swift_Signer[] $signers
     *
     * @return self
     */
    public function attachSigners(array $signers): self
    {
        foreach ($signers as $signer) {
            $this->swiftMessage->attachSigner($signer);
        }

        return $this;
    }
}
