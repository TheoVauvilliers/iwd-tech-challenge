<?php

namespace App\Service\Helper;

use App\Service\Connector\ShortcutConnector;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShortcutHelper
{
    protected ?ShortcutConnector $connector = null;

    public function __construct()
    {
        $this->connector = new ShortcutConnector();
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getProjects(): array
    {
        return $this->connector->call('GET', 'projects');
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getWorkflows(): array
    {
        return $this->connector->call('GET', 'workflows');
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getEpics(): array
    {
        return $this->connector->call('GET', 'epics');
    }

    /**
     * @param array $data
     * @return array
     */
    public function dataToAssocArray(array $data): array
    {
        return array_reduce($data, function ($data, $row) {
            $data[$row['name']] = $row['id'];
            return $data;
        }, []);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @param string $dataType
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function create(string $endpoint, array $data, string $dataType = 'json'): array
    {
        return $this->connector->call('POST', $endpoint, $data, $dataType);
    }
}
