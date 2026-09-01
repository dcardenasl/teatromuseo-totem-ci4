<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;
use Config\Services;

class LocaleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $config = config(App::class);
        if ($config === null) {
            return null;
        }

        $supported = $config->supportedLocales;
        $locale = $request instanceof IncomingRequest
            ? $request->getCookie('totem_lang')
            : null;

        if (is_string($locale) && $locale !== '' && in_array($locale, $supported, true)) {
            $this->applyLocale($locale);

            return null;
        }

        $defaultLocale = $config->defaultLocale;
        if ($defaultLocale !== '') {
            $this->applyLocale($defaultLocale);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    /**
     * @param non-empty-string $locale
     */
    private function applyLocale(string $locale): void
    {
        $req = Services::request();
        if ($req instanceof IncomingRequest) {
            $req->setLocale($locale);
        }

        Services::language()->setLocale($locale);
    }
}
