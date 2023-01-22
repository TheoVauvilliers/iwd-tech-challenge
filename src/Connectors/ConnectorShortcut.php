<?php

namespace App\Connectors;

use App\Libs\Connector\ConnectorAbstract;

class ConnectorShortcut extends ConnectorAbstract
{
    protected const API_BASE_URL = 'https://api.app.shortcut.com/api/v3/';

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return !empty($_ENV['API_SHORTCUT_BASE_URL']) ? $_ENV['API_SHORTCUT_BASE_URL'] : ConnectorShortcut::API_BASE_URL;
    }

    /**
     * @return array[]
     */
    protected function getCurlOpt(): array
    {
        if (empty($_ENV['API_SHORTCUT_TOKEN'])) {
            throw new \RuntimeException('The token must be defined in the .env file');
        }

        $token = $_ENV['API_SHORTCUT_TOKEN'];

        return [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Shortcut-Token: $token",
            ],
        ];
    }

    /**
     * @param array $response
     * @return array|bool
     */
    protected function parseCallResponse(array $response): array|bool
    {
        if (isset($response['message']) && $response['message'] === 'Page not Found') {
            return false;
        }

        return $response;
    }
}
