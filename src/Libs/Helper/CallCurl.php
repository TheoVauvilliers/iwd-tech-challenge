<?php

namespace App\Libs\Helper;

use Exception;

class CallCurl
{
    /**
     * @param array $opts
     * @return array|true[]
     */
    protected function getCurlOpts(array $opts = []): array
    {
        $default = [
            CURLOPT_RETURNTRANSFER => true,
        ];

        return $opts + $default;
    }

    /**
     * @param $url
     * @param $postArgs
     * @param array $curlOpts
     * @return bool|string
     * @throws Exception
     */
    public function callPost($url, $postArgs, array $curlOpts = []): bool|string
    {
        $curl = \curl_init($url);

        // cURL post options
        $postOpts = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postArgs,
        );

        // Merge defaults, override and post options together
        $curlOpts = $postOpts + $this->getCurlOpts($curlOpts);
        \curl_setopt_array($curl, $curlOpts);

        // Perform cURL call
        $response = \curl_exec($curl);

        if ($response === false) {
            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            throw new Exception("HTTP client: cURL call failed for $url : error $curl_errno : $curl_error");
        }

        // Close
        \curl_close($curl);

        return $response;
    }

    /**
     * @param $url
     * @param array $curlOpts
     * @return bool|string
     * @throws Exception
     */
    public function callGet($url, array $curlOpts = []): bool|string
    {
        $curl = curl_init($url);

        // Merge defaults and override together
        $curlOpts = $this->getCurlOpts($curlOpts);
        \curl_setopt_array($curl, $curlOpts);

        // Perform cURL call
        $response = \curl_exec($curl);

        // Handle error
        if ($response === false) {
            $curl_errno = \curl_errno($curl);
            $curl_error = \curl_error($curl);
            throw new Exception("HTTP client: cURL call failed for $url : error $curl_errno : $curl_error");
        }

        // Close
        \curl_close($curl);

        return $response;
    }
}
