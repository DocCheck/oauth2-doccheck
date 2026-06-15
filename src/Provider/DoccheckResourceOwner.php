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
        return $this->response['unique_id'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->response['email'] ? strtolower($this->response['email']) : null;
    }

    public function getFirstName(): ?string
    {
        return $this->response['first_name'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->response['last_name'] ?? null;
    }

    public function getProfessionId(): ?int
    {
        return isset($this->response['profession_id']) ? (int)$this->response['profession_id'] : null;
    }

    public function getDisciplineId(): ?int
    {
        return isset($this->response['discipline_id']) ? (int)$this->response['discipline_id'] : null;
    }

    public function getActivityId(): ?int
    {
        return isset($this->response['activity_id']) ? (int)$this->response['activity_id'] : null;
    }

    public function getAreaCode(): ?string
    {
        return $this->response['area_code'] ?? null;
    }

    public function getStreet(): ?string
    {
        return $this->response['street'] ?? null;
    }

    public function getCity(): ?string
    {
        return $this->response['city'] ?? null;
    }

    public function getCountryIsoCode(): ?string
    {
        return $this->response['country_iso_code'] ?? null;
    }

    public function getCountryId(): ?int
    {
        return isset($this->response['country_id']) ? (int)$this->response['country_id'] : null;
    }

    public function toArray(): array
    {
        return $this->response;
    }
}