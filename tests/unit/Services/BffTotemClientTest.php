<?php

namespace Tests\Unit\Services;

use App\Services\BffTotemClient;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;
use ReflectionClass;
use Tests\Support\FakeBffCurlRequest;

/**
 * Guards against the exact bug this class shipped with once already:
 * `Config\Services::curlrequest()` reads `$options['baseURI']` (camelCase)
 * to build its base URL — passing `base_URI` (the underscored form used
 * throughout the rest of this app's CURLRequest-adjacent code) is silently
 * accepted and produces an *empty* base, so every subsequent relative
 * `get('public-read/...')` call gets misresolved as if "public-read" were
 * the hostname (curl error 6, "Could not resolve host"). Every request
 * failed this way, indistinguishable from the BFF being genuinely down,
 * until this was caught by hand against a live BFF.
 *
 * @internal
 */
final class BffTotemClientTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Services::cache()->clean();
        putenv('TOTEM_BFF_BASE_URL=http://bff.example.test:8188');
        $_ENV['TOTEM_BFF_BASE_URL'] = 'http://bff.example.test:8188';
        $_SERVER['TOTEM_BFF_BASE_URL'] = 'http://bff.example.test:8188';
    }

    protected function tearDown(): void
    {
        putenv('TOTEM_BFF_BASE_URL');
        putenv('TOTEM_BFF_API_KEY');
        unset($_ENV['TOTEM_BFF_BASE_URL'], $_SERVER['TOTEM_BFF_BASE_URL']);
        unset($_ENV['TOTEM_BFF_API_KEY'], $_SERVER['TOTEM_BFF_API_KEY']);
        Services::cache()->clean();
        parent::tearDown();
    }

    public function testTheRealHttpClientIsConfiguredWithTheConfiguredBffBaseUrl(): void
    {
        $client = new BffTotemClient(Services::cache());

        $reflectedClient = new ReflectionClass($client);
        $getClient = $reflectedClient->getMethod('getClient');
        $getClient->setAccessible(true);
        /** @var CURLRequest $curlRequest */
        $curlRequest = $getClient->invoke($client);

        self::assertNotNull($curlRequest, 'getClient() returned null — CURLRequest construction failed.');

        $reflectedCurl = new ReflectionClass($curlRequest);
        $baseUriProperty = $reflectedCurl->getProperty('baseURI');
        $baseUriProperty->setAccessible(true);
        $baseUri = (string) $baseUriProperty->getValue($curlRequest);

        self::assertSame('http://bff.example.test:8188/api/v1/', $baseUri);
    }

    public function testSuccessfulResponsesAreReusedFromTheFreshCache(): void
    {
        $fake = new FakeBffCurlRequest([
            'public-read/es/events' => FakeBffCurlRequest::envelope([
                ['id' => 1, 'title' => 'Cached show'],
            ]),
        ]);
        $client = new BffTotemClient(Services::cache(), $fake);

        $first = $client->shows('es');
        $second = $client->shows('es');

        self::assertSame('fresh', $first->state);
        self::assertSame('fresh', $second->state);
        self::assertSame([['id' => 1, 'title' => 'Cached show']], $second->list());
        self::assertSame(1, $fake->callCount('public-read/es/events'));
    }

    public function testTheLastSuccessfulResponseIsServedAsStaleAfterTransportFailure(): void
    {
        $path = 'public-read/es/events';
        $query = [
            'sort' => 'agenda',
            'per_page' => 100,
            'fields' => 'id,title,localized,cover_image,slug,next_occurrence_at,last_occurrence_at',
        ];

        $successfulClient = new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            $path => FakeBffCurlRequest::envelope([['id' => 2, 'title' => 'Stale show']]),
        ]));
        $successfulClient->shows('es');

        $keyReflection = new ReflectionClass($successfulClient);
        $cacheKeyMethod = $keyReflection->getMethod('cacheKey');
        $cacheKeyMethod->setAccessible(true);
        $freshKey = $cacheKeyMethod->invoke($successfulClient, $path, $query);
        Services::cache()->delete($freshKey);

        $failingClient = new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true));
        $result = $failingClient->shows('es');

        self::assertSame('stale', $result->state);
        self::assertSame([['id' => 2, 'title' => 'Stale show']], $result->list());
    }

    public function testServerErrorsAreRetriedBeforeReturningARealResponse(): void
    {
        $path = 'public-read/es/events';
        $fake = new FakeBffCurlRequest(sequences: [
            $path => [
                ['status' => 503, 'body' => ['error' => 'temporary']],
                FakeBffCurlRequest::envelope([['id' => 3, 'title' => 'Recovered show']]),
            ],
        ]);
        $client = new BffTotemClient(Services::cache(), $fake);

        $result = $client->shows('es');

        self::assertSame('fresh', $result->state);
        self::assertSame([['id' => 3, 'title' => 'Recovered show']], $result->list());
        self::assertSame(2, $fake->callCount($path));
    }

    public function testRequestsCarryTheBffKeyAndExpectedQuery(): void
    {
        putenv('TOTEM_BFF_API_KEY=test-totem-key');
        $_ENV['TOTEM_BFF_API_KEY'] = 'test-totem-key';
        $_SERVER['TOTEM_BFF_API_KEY'] = 'test-totem-key';

        $fake = new FakeBffCurlRequest([
            'public-read/es/events' => FakeBffCurlRequest::envelope([]),
        ]);
        $client = new BffTotemClient(Services::cache(), $fake);

        $client->shows('es');
        $request = $fake->requests()[0];

        self::assertSame('public-read/es/events', $request['url']);
        self::assertSame('test-totem-key', $request['options']['headers']['X-App-Key']);
        self::assertSame('agenda', $request['options']['query']['sort']);
        self::assertSame(100, $request['options']['query']['per_page']);

        putenv('TOTEM_BFF_API_KEY');
        unset($_ENV['TOTEM_BFF_API_KEY'], $_SERVER['TOTEM_BFF_API_KEY']);
    }
}
