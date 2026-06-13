<?php

namespace Config;

use App\Services\CachedTotemApiService;
use App\Services\FileCachedTotemApiService;
use App\Services\TotemApiInterface;
use App\Services\TotemApiService;
use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    public static function totemApi(bool $getShared = true): TotemApiInterface
    {
        if ($getShared) {
            static $instance;

            if ($instance === null) {
                $instance = static::totemApi(false);
            }

            return $instance;
        }

        // Base service with request-scoped memoization
        $service = new CachedTotemApiService(new TotemApiService());

        // Add file-based cache layer if enabled (safely check env var)
        $enableFileCache = getenv('TOTEM_ENABLE_FILE_CACHE');
        if ($enableFileCache !== false && strtolower($enableFileCache) !== 'false') {
            $cacheTtl = getenv('TOTEM_CACHE_TTL_SECONDS');
            $ttl = is_numeric($cacheTtl) ? (int) $cacheTtl : 60;
            $cachePath = WRITEPATH . 'cache/totem/';

            try {
                return new FileCachedTotemApiService($service, $cachePath, $ttl);
            } catch (\Exception $e) {
                // If file cache fails, continue with memory cache only
            }
        }

        return $service;
    }
}
