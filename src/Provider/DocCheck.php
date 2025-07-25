<?php

declare(strict_types=1);

namespace DocCheck\OAuth2\Client\Provider;

use DocCheck\OAuth2\Client\Utils\Language;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use League\OAuth2\Client\Tool\QueryBuilderTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2025 DocCheck Community GmbH
 */
class DocCheck extends AbstractProvider
{
    use BearerAuthorizationTrait;
    use QueryBuilderTrait;

    protected const BASE_URL = 'https://auth.doccheck.com/';
    protected string $baseAuthUrl = self::BASE_URL;
    protected bool $stateless = false;
    protected Language $authorizationLanguage = Language::EN;

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->getUrl($this->authorizationLanguage->value . '/authorize');
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getUrl('token');
    }

    protected function getAuthorizationParameters(array $options)
    {
        $options = parent::getAuthorizationParameters($options);
        if ($this->stateless) {
            $this->state = '';
            unset($options['state']);
        }

        return $options;
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getUrl('api/users/data');
    }

    protected function getDefaultScopes()
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            $description = [];
            if (isset($data['error'])) {
                $description[] = $data['error'] . ':';
            }
            if (isset($data['error_description'])) {
                $description[] = $data['error_description'] . ':';
            }

            throw new IdentityProviderException(
                $description !== [] ? implode(' ', $description) : $response->getReasonPhrase(),
                $response->getStatusCode(),
                $data
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new DocCheckResourceOwner($response);
    }

    private function getUrl(string $uri): string
    {
        return $this->baseAuthUrl . $uri;
    }
}