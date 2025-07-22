<?php

declare(strict_types=1);

namespace DocCheck\OAuth2\Client\Utils;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2025 DocCheck Community GmbH
 */
enum Language: string
{
    case DE = 'de';
    case EN = 'en';
    case FR = 'fr';
    case NL = 'nl';
    case ES = 'es';
    case IT = 'it';
}