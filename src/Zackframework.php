<?php
/*
#############################################################################
#  
#  Developed & Published by:
#  Copyright (c) 2008 by ZULMD DOT COM (IP0445886-X). All right reserved.
#  Hakcipta Terpelihara (c) 2008 oleh ZULMD DOT COM (IP0445886-X)
#   
#  Website : http://www.zulmd.com
#  E-mail : enquiry@zulmd.com
#  Phone : +6013 500 9007 (Zulkifli Mohamed)
#
############################################################################
*/
namespace Zackframework;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Zackframework\Exceptions\ZackframeworkSDKException;

class Zackframework
{
    const SDK_VERSION = '1.0';
    const DEFAULT_API_VERSION = 'v1';
    const API_KEY_ENV_NAME = 'ZACKFRAMEWORK_API_KEY';
    const API_SECRET_ENV_NAME = 'ZACKFRAMEWORK_API_SECRET';
    const API_URL_ENV_NAME = 'ZACKFRAMEWORK_API_URL';

    protected $api_key;
    protected $api_secret;
    protected $api_url;
    protected $client;
    protected $defaultApiVersion;
    protected $accessToken;

    public function __construct(array $config = [])
    {
        $config = array_merge([
            'api_key' => getenv(static::API_KEY_ENV_NAME),
            'api_secret' => getenv(static::API_SECRET_ENV_NAME),
            'api_url' => getenv(static::API_URL_ENV_NAME),
            'api_version' => static::DEFAULT_API_VERSION
        ], $config);

        if (!$config['api_key']) {
            throw new ZackframeworkSDKException('Required "api_key" key not supplied in config and could not find fallback environment variable "' . static::API_KEY_ENV_NAME . '"');
        }
        if (!$config['api_secret']) {
            throw new ZackframeworkSDKException('Required "api_secret" key not supplied in config and could not find fallback environment variable "' . static::API_SECRET_ENV_NAME . '"');
        }
        if (!$config['api_url']) {
            throw new ZackframeworkSDKException('Required "api_url" key not supplied in config and could not find fallback environment variable "' . static::API_URL_ENV_NAME . '"');
        }

        if (!is_string($config['api_key']) || strlen($config['api_key']) != 32) {
            throw new ZackframeworkSDKException('The "api_key" must be formatted as a string and must be 32 characters long.');
        }
        if (!is_string($config['api_secret']) || strlen($config['api_secret']) != 16) {
            throw new ZackframeworkSDKException('The "api_secret" must be formatted as a string and must be 16 characters long.');
        }

        $this->api_key = (string) $config['api_key'];
        $this->api_secret = (string) $config['api_secret'];
        $this->api_url = rtrim($config['api_url'], '/');        
        $this->defaultApiVersion = $config['api_version'];
        $this->accessToken = $this->api_key . ':' . $this->api_secret;

        $this->client = new Client();
    }


    public function getApiKey() {
        return $this->api_key;
    }
    public function getApiSecret() {
        return $this->api_secret;
    }
    public function getApiUrl() {
        return $this->api_url . '/' . $this->defaultApiVersion;
    }
    public function getClient() {
        return $this->client;
    }
    public function getApiVersion() {
        return $this->defaultApiVersion;
    }


    public function get($endpoint, $apiVersion = null) {
        return $this->sendRequest('GET', $endpoint, $params = [], $apiVersion);
    }
    public function post($endpoint, array $params = [], $apiVersion = null) {
        return $this->sendRequest('POST', $endpoint, $params, $apiVersion);
    }
    public function delete($endpoint, array $params = [], $apiVersion = null) {
        return $this->sendRequest('DELETE', $endpoint, $params, $apiVersion);
    }


    public function sendRequest($method, $endpoint, array $params = [], $apiVersion = null) {
        $apiVersion = $apiVersion ?: $this->defaultApiVersion;
        
        $url = $this->getApiUrl() . $endpoint;
        $headers = array('Authorization' => 'Bearer ' . $this->accessToken);

        //$response = $this->client->get($url, array(‘headers’ => $header));
        return $url;
    }
}
