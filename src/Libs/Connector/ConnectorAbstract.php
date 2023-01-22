<?php

namespace App\Libs\Connector;

use App\Libs\Helper\CallCurl;
use Exception;

abstract class ConnectorAbstract
{
    protected ?CallCurl $client = null;

    public function __construct()
    {
        $this->client = new CallCurl();
    }

    /**
     * @param string $method
     * @param string $endPoint
     * @param array $postArgs
     * @return array|bool
     * @throws Exception
     */
    public function call(string $method, string $endPoint, array $postArgs = []): bool|array
    {
        $curlOpts = $this->getCurlOpt();
        $postData = $this->buildPost($postArgs);
        $baseUrl = $this->getBaseUrl();

        $response = match ($method) {
            'POST' => json_decode(
                $this->client->callPost($baseUrl . $endPoint, $postData, $curlOpts),
                true
            ) ?? [],
            'GET' => json_decode(
                $this->client->callGet($baseUrl . $endPoint, $curlOpts),
                true
            ) ?? [],
            default => [],
        };

        return $this->parseCallResponse($response) ?? [];
    }

    /**
     * @param array $postArray
     * @return string
     */
    protected function buildPost(array $postArray): string
    {
        return http_build_query($postArray);
    }

    abstract protected function getCurlOpt(): array;

    abstract protected function getBaseUrl(): string;

    abstract protected function parseCallResponse(array $response): array|bool;
}
