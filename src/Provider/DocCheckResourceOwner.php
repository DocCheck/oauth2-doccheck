<?php

declare(strict_types=1);

namespace DocCheck\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2025 DocCheck Community GmbH
 */
class DocCheckResourceOwner implements ResourceOwnerInterface
{
    protected $response = [];

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId(): ?string
    {
        return $this->response['uniquekey'];
    }

    public function getEmail(): ?string
    {
        return $this->response['email'] ?: null;
    }

    public function toArray(): array
    {
        return $this->response;
    }
}