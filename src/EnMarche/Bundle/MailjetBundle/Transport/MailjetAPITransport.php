<?php

namespace EnMarche\Bundle\MailjetBundle\Transport;

use EnMarche\Bundle\MailjetBundle\Exception\MailjetException;
use EnMarche\Bundle\MailjetBundle\Template\MailjetTemplateEmail;
use GuzzleHttp\ClientInterface;

class MailjetApiTransport implements MailjetMessageTransportInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @param ClientInterface $httpClient
     * @param string          $publicKey
     * @param string          $privateKey
     */
    public function __construct(
        ClientInterface $httpClient,
        string $publicKey,
        string $privateKey
    ) {
        $this->httpClient = $httpClient;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * @inheritdoc
     */
    public function sendTemplateEmail(MailjetTemplateEmail $email)
    {
        $response = $this->httpClient->request('POST', 'send', [
            'auth' => [$this->publicKey, $this->privateKey],
            'body' => $email->getHttpRequestPayload(),
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new MailjetException('Unable to send email to recipients.');
        }

        $email->delivered((string) $response->getBody());
    }
}
