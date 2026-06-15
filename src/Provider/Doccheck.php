<?php

declare(strict_types=1);

namespace Doccheck\OAuth2\Client\Provider;

use Composer\InstalledVersions;
use Doccheck\OAuth2\Client\Utils\Language;
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
class Doccheck extends AbstractProvider
{
    use BearerAuthorizationTrait;
    use QueryBuilderTrait;

    protected const BASE_URL = 'https://login.doccheck.com/';
    protected $baseAuthUrl = self::BASE_URL;
    protected $stateless = false;
    protected $authorizationLanguage = Language::EN;

    public function __construct(array $options = [], array $collaborators = [])
    {
        @trigger_error(
            'Version 1 of doccheck/oauth2-doccheck is deprecated and will no longer be maintained. ' .
            'Please upgrade to version 2: composer require doccheck/oauth2-doccheck:^2.0',
            E_USER_DEPRECATED
        );
        parent::__construct($options, $collaborators);
    }

    public function getBaseAuthorizationUrl()
    {
        $data = [
            'dc_language' => $this->authorizationLanguage,
            'dc_template' => 'fullscreen_dc',
        ];

        return $this->getUrl('code/?'.$this->buildQueryString($data));
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getUrl('service/oauth/access_token/');
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
        return $this->getUrl('service/oauth/user_data/v2/');
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
                $description[] = $data['error'].':';
            }
            if (isset($data['error_description'])) {
                $description[] = $data['error_description'].':';
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
        return new DoccheckResourceOwner($response);
    }

    private function getUrl(string $uri): string
    {
        return $this->baseAuthUrl.$uri;
    }

    /**
     * @return array<string, string>
     */
    protected function getDefaultHeaders(): array
    {
        $userAgent = sprintf(
            '%s/%s (%s) PHP/%s',
            'OAuth2DocCheck',
            InstalledVersions::getPrettyVersion('doccheck/oauth2-doccheck'),
            php_uname('s'), // operating system
            phpversion()
        );

        return array_merge(
            parent::getDefaultHeaders(),
            [
                'User-Agent' => $userAgent
            ]
        );
    }
}
