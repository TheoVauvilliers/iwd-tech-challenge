<?php

namespace App\Service\Connector;

use App\Service\Abstract\Connector\ConnectorAbstract;

class ShortcutConnector extends ConnectorAbstract
{
    protected const API_BASE_URL = 'https://api.app.shortcut.com/api/v3/';

    /**
     * @inheritDoc
     */
    protected function getBaseUrl(): string
    {
        return !empty($_ENV['API_SHORTCUT_BASE_URL']) ? $_ENV['API_SHORTCUT_BASE_URL'] : self::API_BASE_URL;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    protected function parseCallResponse(array $response): array
    {
        return $response;
    }
}
