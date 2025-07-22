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
    /**
     * @var DocCheck
     */
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new DocCheck([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'https://www.doccheck.com',
            'legacy' => true
        ]);
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertStringStartsWith('https://login.doccheck.com/code/', $url);;
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
    }

    public function testAuthorizationUrlWithForeignLanguage(): void
    {
        $provider = new DocCheck([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'authorizationLanguage' => Language::ES,
            'legacy' => true
        ]);

        $url = $provider->getAuthorizationUrl();

        $this->assertStringStartsWith('https://login.doccheck.com/code/?dc_language=es', $url);
    }

    public function testStatelessAuthorizationUrl(): void
    {
        $provider = new DocCheck([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'authorizationLanguage' => Language::EN,
            'stateless' => true,
            'legacy' => true
        ]);

        $url = $provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayNotHasKey('state', $query);
    }
}