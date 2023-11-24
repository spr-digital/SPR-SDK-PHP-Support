<?php

namespace SprDigitalLab\SprSdkPhpSupport;

use Exception;

class RemotePackage
{
    private $remotePackageUrl;

    public function __construct($remotePackageUrl)
    {
        $this->remotePackageUrl = $remotePackageUrl;
    }

    public function __call($action, $arguments)
    {
        $data = ['_action' => $action, '_data' => $arguments];

        $ch = curl_init($this->remotePackageUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!$response = curl_exec($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            return ["action" => false, "reason" => "NETWORK_ERROR"];
        }

        curl_close($ch);

        $responseData = json_decode($response, true);
        if ($responseData === null) {
            return ["action" => false, "reason" => "NETWORK_ERROR"];
        }

        return $responseData;
    }
}
