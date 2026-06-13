<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class LocaleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $config = config('App');
        $supported = $config->supportedLocales;
        $locale = $request->getCookie('totem_lang');

        if (is_string($locale) && in_array($locale, $supported, true)) {
            $this->applyLocale($locale);

            return null;
        }

        $this->applyLocale($config->defaultLocale);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function applyLocale(string $locale): void
    {
        service('request')->setLocale($locale);
        Services::language()->setLocale($locale);
    }
}
