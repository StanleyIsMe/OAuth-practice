<?php

namespace App\Services;

use GuzzleHttp\Client;

/**
 * Class FacebookService
 * @package App\Services
 */
class FacebookService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * FacebookService constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * get facebook access token
     * @param string $redirect_uri
     * @param string $code
     * @return mixed
     */
    public function getAccessToken($redirect_uri, $code)
    {
        /**
         * 回傳
         * "access_token": {access-token},
         * "token_type": {type},
         * "expires_in":  {seconds-til-expiration}
         */
        $response = $this->client->get('https://graph.facebook.com/v3.2/oauth/access_token', [
            'query' => [
                'client_id' => env('FB_APP_ID'),
                'redirect_uri' => $redirect_uri,
                'client_secret' => env('FB_SECRET_KEY'),
                'code' => $code
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * check access token is valid
     * @param string $access_token
     * @return mixed
     */
    public function verifyAccessToken($access_token)
    {
        $response = $this->client->get('https://graph.facebook.com/debug_token', [
            'query' => [
                'input_token' => $access_token,
                'access_token' => env('FB_APP_ID').'|'.env('FB_SECRET_KEY')
            ]
        ]);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * get facebook member information
     * @param string $field_name
     * @param string $access_token
     * @return mixed
     */
    public function getFieldByToken($field_name, $access_token)
    {
        $response = $this->client->get("https://graph.facebook.com/me", [
            'query' => [
                'fields' => $field_name,
                'access_token' => $access_token
            ]
        ]);
        return json_decode($response->getBody()->getContents());
    }
}