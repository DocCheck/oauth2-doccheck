<?php

declare(strict_types=1);

namespace Doccheck\OAuth2\Client\Test\Provider;

use Doccheck\OAuth2\Client\Provider\Doccheck;
use Doccheck\OAuth2\Client\Utils\Language;
use Doccheck\OAuth2\Client\Utils\Version;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2025 DocCheck Community GmbH
 */
class DoccheckTest extends TestCase
{
    private Doccheck $provider;

    protected function setUp(): void
    {
        $this->provider = new Doccheck([
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
        $provider = new Doccheck([
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
        $provider = new Doccheck([
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

    public function testDefaultHeaders(): void
    {
        $provider = new Doccheck([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'authorizationLanguage' => Language::EN,
            'stateless' => true,
            'legacy' => true
        ]);

        $headers = $provider->getHeaders();

        $this->assertArrayHasKey('User-Agent', $headers);

        $expectedUserAgent = sprintf(
            '%s/%s (%s) PHP/%s',
            'OAuth2DocCheck',
            Version::getVersion(),
            php_uname('s'), // operating system
            phpversion()
        );

        $this->assertEquals($expectedUserAgent, $headers['User-Agent']);
    }

    #[DataProvider('errorResponseProvider')]
    public function testCheckResponseThrowsExceptionOnErrors(int $status, array $data, string $expectedMessage): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($status);
        $response->method('getReasonPhrase')->willReturn('Internal Server Error');

        $provider = new class([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]) extends Doccheck {
            public function checkResponse(ResponseInterface $response, $data)
            {
                parent::checkResponse($response, $data);
            }
        };

        $this->expectException(IdentityProviderException::class);
        $this->expectExceptionMessage($expectedMessage);
        $this->expectExceptionCode($status);

        $provider->checkResponse($response, $data);
    }

    public static function errorResponseProvider(): array
    {
        return [
            'error and description' => [
                400,
                ['error' => 'foo', 'error_description' => 'bar'],
                'foo: bar'
            ],
            'error, description and hint' => [
                401,
                ['error' => 'foo', 'error_description' => 'bar', 'hint' => 'baz'],
                'foo: bar Hint: "baz".'
            ],
            'only hint' => [
                403,
                ['hint' => 'some hint'],
                'Hint: "some hint".'
            ],
            'only error' => [
                400,
                ['error' => 'error_code'],
                'error_code:'
            ],
            'no data' => [
                500,
                [],
                'Internal Server Error'
            ]
        ];
    }
}
