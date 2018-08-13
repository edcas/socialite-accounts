<?php

namespace EdCas\SocialiteProviders\Accounts;

use Illuminate\Http\Request;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'ACCOUNTS';

    /**
     * ACCOUNTS URL
     */
    protected $instanceURI;

    /**
     * @inheritdoc
     */
    protected $scopes = [];

    /**
     * @inheritdoc
     */
    protected $scopeSeparator = ' ';

    /**
     * @inheritDoc
     */
    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $guzzle = [])
    {
        $this->instanceURI = 'http://accounts.io:8000';
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     *
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->instanceURI . '/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return $this->instanceURI . '/oauth/token';
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string $code
     *
     * @return array
     */
    protected function getTokenFields($code)
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string $token
     *
     * @return array
     */
    protected function getUserByToken($token)
    {
        $userUrl = $this->instanceURI . '/api/user';

        $response = $this->getHttpClient()->get($userUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array $user
     *
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstanceUri()
    {
        return $this->getConfig('instance_uri', $this->instanceURI);
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['instance_uri'];
    }
}
