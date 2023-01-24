<?php

namespace App\Service\Connector;

use App\Lib\Connector\ConnectorAbstract;

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
    protected function getHeader(): array
    {
        if (empty($_ENV['API_SHORTCUT_TOKEN'])) {
            throw new \RuntimeException('The token must be defined in the .env file');
        }

        $token = $_ENV['API_SHORTCUT_TOKEN'];

        return [
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            "Shortcut-Token: $token",
        ];
    }

    /**
     * @param array $response
     * @return array|bool
     */
    protected function parseCallResponse(array $response): array|bool
    {
        return $response;
    }
}
