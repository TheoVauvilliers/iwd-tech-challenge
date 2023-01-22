<?php

namespace App\Libs\Connector;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class ConnectorAbstract
{
    protected ?HttpClientInterface $client = null;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    /**
     * @param string $method
     * @param string $endPoint
     * @param array $body
     * @param string $dataType
     * @return bool|array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function call(string $method, string $endPoint, array $body = [], string $dataType = 'body'): bool|array
    {
        $header = $this->getHeader();
        $baseUrl = $this->getBaseUrl();

        $options = [
            'headers' => $header,
        ];

        if (!empty($body)) {
            $options[$dataType] = $body;
        }

        $response = $this->client->request($method, $baseUrl . $endPoint, $options);

        return $this->parseCallResponse($response->toArray());
    }

    abstract protected function getHeader(): array;

    abstract protected function getBaseUrl(): string;

    abstract protected function parseCallResponse(array $response): array|bool;
}
