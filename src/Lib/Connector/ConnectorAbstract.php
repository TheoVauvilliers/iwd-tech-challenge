<?php

namespace App\Lib\Connector;

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
     * Used to make an API call
     * @param string $method HTTP Method
     * @param string $endPoint (Do not insert / at the beginning of your endpoint)
     * @param array $body of your request
     * @param string $dataType of your request
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function call(string $method, string $endPoint, array $body = [], string $dataType = 'body'): array
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

    /**
     * Get header for connector
     * @return array
     */
    abstract protected function getHeader(): array;

    /**
     * Get base url for connector
     * @return string
     */
    abstract protected function getBaseUrl(): string;

    /**
     * Allows you to parse the API response before rendering it
     * @param array $response API Response
     * @return array
     */
    abstract protected function parseCallResponse(array $response): array;
}
