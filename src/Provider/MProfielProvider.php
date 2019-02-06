<?php

namespace Tactics\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class MProfielProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const MPROFIEL_SERVICE = 'astad.mprofiel.v1';

    /** @var string */
    private $environment;

    /** @var string[][] */
    private static $environmentUrlsMap = [
        'dev' => [
            'baseAuthorizationUrl' => 'https://api-oauth2-o.antwerpen.be/v1/authorize',
            'baseAccessTokenUrl' => 'https://api-gw-o.antwerpen.be/astad/mprofiel/v1/oauth2/token',
            'resourceOwnerDetailsUrl' => 'https://api-gw-o.antwerpen.be/astad/mprofiel/v1/v1/me'
        ],
        'acc' => [
            'baseAuthorizationUrl' => 'https://api-oauth2-a.antwerpen.be/v1/authorize',
            'baseAccessTokenUrl' => 'https://api-gw-a.antwerpen.be/astad/mprofiel/v1/oauth2/token',
            'resourceOwnerDetailsUrl' => 'https://api-gw-a.antwerpen.be/astad/mprofiel/v1/v1/me'
        ],
        'prod' => [
            'baseAuthorizationUrl' => 'https://api-oauth2.antwerpen.be/v1/authorize',
            'baseAccessTokenUrl' => 'https://api-gw-p.antwerpen.be/astad/mprofiel/v1/oauth2/token',
            'resourceOwnerDetailsUrl' => 'https://api-gw-p.antwerpen.be/astad/mprofiel/v1/v1/me'
        ]
    ];

  /**
   * MProfielProvider constructor.
   * @param array $options
   * @param array $collaborators
   */
    public function __construct(array $options = [], array $collaborators = [])
    {
      $environment = 'dev';
      if (isset($options['environment'])){
        $environment = $options['environment'];
        if (!isset(self::$environmentUrlsMap[$environment])) {
          throw new \InvalidArgumentException('Invalid environment');
        }
        unset($options['environment']);
      }

      $this->environment = $environment;

      parent::__construct($options, $collaborators);
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->getUrl('baseAuthorizationUrl');
    }

    /**
     * Returns authorization parameters based on provided options.
     *
     * @param  array $options
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options)
    {
        $options = parent::getAuthorizationParameters($options);

        // approval_prompt not supported
        unset($options['approval_prompt']);

        // service param required
        $options += [
            'service' => self::MPROFIEL_SERVICE
        ];

        return $options;
    }

    /**
     * @return string
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getUrl('baseAccessTokenUrl');
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getUrl('resourceOwnerDetailsUrl');
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [
            'astad.mprofiel.v1.all'
        ];
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException(sprintf("%s: %s", $data['error'], $data['error_description']), $response->getStatusCode(), $response->getBody());
        }
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param  AccessToken $token
     * @return MProfielResourceOwner
     */
    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);

        return $this->createResourceOwner($response, $token);
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return MProfielResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new MProfielResourceOwner($response);
    }

    /**
     * @param string $typeOfUrl
     * @return mixed
     */
    private function getUrl($typeOfUrl)
    {
        return self::$environmentUrlsMap[$this->environment][$typeOfUrl];
    }
}