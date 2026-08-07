<?php

namespace Tests\Unit\Services;

use App\Services\TotemApiService;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\Response;
use PHPUnit\Framework\TestCase;

class TotemApiServiceTest extends TestCase
{
    private function createMockClient(Response $response): CURLRequest
    {
        $client = $this->createMock(CURLRequest::class);
        $client->method('get')->willReturn($response);

        return $client;
    }

    /**
     * @param array<mixed> $data
     */
    private function json(array $data): string
    {
        $encoded = json_encode($data);
        $this->assertIsString($encoded);

        return $encoded;
    }

    private function createResponse(int $statusCode, string $body): Response
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getBody')->willReturn($body);

        return $response;
    }

    public function testShowsReturnsDataFromSuccessfulResponse(): void
    {
        $response = $this->createResponse(200, $this->json(['data' => [['id' => 1, 'title' => 'Show']]]));
        $service = new TotemApiService($this->createMockClient($response));

        $result = $service->shows();

        $this->assertSame([['id' => 1, 'title' => 'Show']], $result);
    }

    public function testShowsReturnsEmptyArrayOnNon200Response(): void
    {
        $response = $this->createResponse(500, $this->json(['error' => 'Internal Server Error']));
        $service = new TotemApiService($this->createMockClient($response));

        $result = $service->shows();

        $this->assertSame([], $result);
    }

    public function testShowsReturnsEmptyArrayOnNetworkException(): void
    {
        $client = $this->createMock(CURLRequest::class);
        $client->method('get')->willThrowException(new \Exception('Connection refused'));

        $service = new TotemApiService($client);
        $result = $service->shows();

        $this->assertSame([], $result);
    }

    public function testShowsReturnsBodyDirectlyWhenDataKeyIsMissing(): void
    {
        $response = $this->createResponse(200, $this->json([['id' => 2, 'title' => 'Another']]));
        $service = new TotemApiService($this->createMockClient($response));

        $result = $service->shows();

        $this->assertSame([['id' => 2, 'title' => 'Another']], $result);
    }

    public function testShowsReturnsEmptyArrayOnInvalidJson(): void
    {
        $response = $this->createResponse(200, 'not valid json');
        $service = new TotemApiService($this->createMockClient($response));

        $result = $service->shows();

        $this->assertSame([], $result);
    }

    public function testMuseumHistoryCallsCorrectEndpoint(): void
    {
        $response = $this->createResponse(200, $this->json(['data' => ['slug' => 'chapter-1']]));
        $client = $this->createMock(CURLRequest::class);
        $client->expects($this->once())
            ->method('get')
            ->with('museum-history/chapter-1', $this->anything())
            ->willReturn($response);

        $service = new TotemApiService($client);
        $result = $service->museumHistory('chapter-1');

        $this->assertSame(['slug' => 'chapter-1'], $result);
    }
}
