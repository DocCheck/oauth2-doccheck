<?php

declare(strict_types=1);

namespace Doccheck\OAuth2\Client\Test\Provider;

use Doccheck\OAuth2\Client\Provider\DoccheckResourceOwner;
use PHPUnit\Framework\TestCase;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2026 DocCheck Community GmbH
 */
class DoccheckResourceOwnerTest extends TestCase
{
    public function testGetters(): void
    {
        $response = [
            'unique_id' => '12345',
            'email' => 'TEST@EXAMPLE.COM',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'profession_id' => '1',
            'discipline_id' => '2',
            'activity_id' => '3',
            'area_code' => '50667',
            'street' => 'Vogelsanger Str. 66',
            'city' => 'Köln',
            'country_iso_code' => 'DE',
            'country_id' => '4',
        ];

        $resourceOwner = new DoccheckResourceOwner($response);

        $this->assertEquals('12345', $resourceOwner->getId());
        $this->assertEquals('test@example.com', $resourceOwner->getEmail());
        $this->assertEquals('Max', $resourceOwner->getFirstName());
        $this->assertEquals('Mustermann', $resourceOwner->getLastName());
        $this->assertEquals(1, $resourceOwner->getProfessionId());
        $this->assertEquals(2, $resourceOwner->getDisciplineId());
        $this->assertEquals(3, $resourceOwner->getActivityId());
        $this->assertEquals('50667', $resourceOwner->getAreaCode());
        $this->assertEquals('Vogelsanger Str. 66', $resourceOwner->getStreet());
        $this->assertEquals('Köln', $resourceOwner->getCity());
        $this->assertEquals('DE', $resourceOwner->getCountryIsoCode());
        $this->assertEquals(4, $resourceOwner->getCountryId());
    }

    public function testGettersReturnNullWhenNotSet(): void
    {
        $resourceOwner = new DoccheckResourceOwner([]);

        $this->assertNull($resourceOwner->getId());
        $this->assertNull($resourceOwner->getEmail());
        $this->assertNull($resourceOwner->getFirstName());
        $this->assertNull($resourceOwner->getLastName());
        $this->assertNull($resourceOwner->getProfessionId());
        $this->assertNull($resourceOwner->getDisciplineId());
        $this->assertNull($resourceOwner->getActivityId());
        $this->assertNull($resourceOwner->getAreaCode());
        $this->assertNull($resourceOwner->getStreet());
        $this->assertNull($resourceOwner->getCity());
        $this->assertNull($resourceOwner->getCountryIsoCode());
        $this->assertNull($resourceOwner->getCountryId());
    }
}
