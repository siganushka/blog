<?php

namespace App\OAuth;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Github
{
    public const AUTHORIZE_URL = 'https://github.com/login/oauth/authorize';
    public const ACCESS_TOKEN_URL = 'https://github.com/login/oauth/access_token';
    public const USER_URL = 'https://api.github.com/user';

    private $client;
    private $clientId;
    private $clientSecret;

    public function __construct(HttpClientInterface $client, string $clientId, string $clientSecret)
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getAuthorizeUrl(string $redirectUri, string $state = null): string
    {
        $query = [
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ];

        return self::AUTHORIZE_URL.'?'.http_build_query($query);
    }

    public function getAccessToken(string $code): array
    {
        $options = [
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $response = $this->client->request('POST', self::ACCESS_TOKEN_URL, $options);

        $data = $response->toArray();
        if (isset($data['error'])) {
            throw new \RuntimeException(sprintf('%s (%s)', $data['error_description'], $data['error']));
        }

        return $data;
    }

    public function getUser(string $accessToken)
    {
        $options = [
            'headers' => [
                'Authorization' => sprintf('token %s', $accessToken),
            ],
        ];

        $response = $this->client->request('GET', self::USER_URL, $options);

        $data = $response->toArray();
        if (isset($data['error'])) {
            throw new \RuntimeException(sprintf('%s (%s)', $data['error_description'], $data['error']));
        }

        return $data;
    }
}
