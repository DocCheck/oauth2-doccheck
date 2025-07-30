<?php

declare(strict_types=1);

namespace Doccheck\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2025 DocCheck Community GmbH
 */
class DoccheckResourceOwner implements ResourceOwnerInterface
{
    public function __construct(protected array $response)
    {
    }

    public function getId(): ?string
    {
        return $this->response['unique_id'] ?: null;
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