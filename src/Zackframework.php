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
use GuzzleHttp\Psr7\Response;

use Zackframework\Exceptions\ZackSDKException;

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
            throw new ZackSDKException('Required "api_key" key not supplied in config and could not find fallback environment variable "' . static::API_KEY_ENV_NAME . '"');
        }
        if (!$config['api_secret']) {
            throw new ZackSDKException('Required "api_secret" key not supplied in config and could not find fallback environment variable "' . static::API_SECRET_ENV_NAME . '"');
        }
        if (!$config['api_url']) {
            throw new ZackSDKException('Required "api_url" key not supplied in config and could not find fallback environment variable "' . static::API_URL_ENV_NAME . '"');
        }

        if (!is_string($config['api_key']) || strlen($config['api_key']) != 32) {
            throw new ZackSDKException('The "api_key" must be formatted as a string and must be 32 characters long.');
        }
        if (!is_string($config['api_secret']) || strlen($config['api_secret']) != 16) {
            throw new ZackSDKException('The "api_secret" must be formatted as a string and must be 16 characters long.');
        }

        $this->api_key = (string) $config['api_key'];
        $this->api_secret = (string) $config['api_secret'];
        $this->api_url = rtrim($config['api_url'], '/');        
        $this->defaultApiVersion = $config['api_version'];
        $this->accessToken = $this->api_key . ':' . $this->api_secret;

        $this->client = new Client([
            'verify' => false
        ]);
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
    public function getVersion() {
        return static::SDK_VERSION;
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
        
        // Setting API Url
        $url = $this->getApiUrl() . rtrim($endpoint, '/');
        
        // Setting up headers
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'User-Agent'    => 'Zackframework API Wrapper/'.$this->getVersion(),
            'Accept'        => 'application/json'
        ];

        // Setup options
        $options['headers'] = $headers;
        if (is_array($params)) {
            $options['body'] = json_encode($params);
        }
        
        // Making Request
        try
        {
            $response = $this->client->request($method, $url, $options);
            $data = json_decode($response->getBody());

            if (isset($data->error)) {
                throw new ZackSDKException('E' . $data->error->code .' : '. $data->error->message);                
            }
        }
        catch (RequestException $e)
        {
            throw new ZackSDKException($e->getMessage());
        }
    }
}
