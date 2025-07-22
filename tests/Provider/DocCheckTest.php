<?php

declare(strict_types=1);

namespace DocCheck\OAuth2\Client\Test\Provider;

use DocCheck\OAuth2\Client\Provider\DocCheck;
use DocCheck\OAuth2\Client\Utils\Language;
use PHPUnit\Framework\TestCase;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2025 DocCheck Community GmbH
 */
class DocCheckTest extends TestCase
{
    private DocCheck $provider;

    protected function setUp(): void
    {
        $this->provider = new DocCheck([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'baseAuthUrl' => 'http://auth.doccheck.example/',
        ]);
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
    }

    public function testAuthorizationUrlWithForeignLanguage(): void
    {
        $provider = new DocCheck([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'baseAuthUrl' => 'http://auth.doccheck.example/',
            'authorizationLanguage' => Language::ES
        ]);

        $url = $provider->getAuthorizationUrl();

        $this->assertStringStartsWith('http://auth.doccheck.example/es/authorize', $url);
    }

    public function testStatelessAuthorizationUrl(): void
    {
        $provider = new DocCheck([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'authorizationLanguage' => Language::EN,
            'stateless' => true
        ]);

        $url = $provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayNotHasKey('state', $query);
    }
}