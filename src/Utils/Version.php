<?php

declare(strict_types=1);

namespace Doccheck\OAuth2\Client\Utils;


use Composer\InstalledVersions;

use function class_exists;

/**
 * @author  Magnus Reiß <magnus.reiss@doccheck.com>
 * @license 2026 DocCheck Community GmbH
 */
final class Version
{
    public static function getVersion(): string
    {
        $packageName = 'doccheck/oauth2-doccheck';

        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled($packageName)) {
            $version = InstalledVersions::getPrettyVersion($packageName);
            if ($version === null) {
                $version = '0';
            }

            $reference = InstalledVersions::getReference($packageName);
            if ($reference === null) {
                $reference = '00000000';
            }

            return $version.'@'.$reference;
        }

        return '0@'.$packageName;
    }
}
