<?php

namespace Tests\Unit\Services;

use App\Services\FileCachedTotemApiService;
use App\Services\TotemApiInterface;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Mock implementation for testing file cache.
 *
 * @internal
 */
final class MockTotemApiService implements TotemApiInterface
{
    public int $callCount = 0;

    /** @var list<array<string, mixed>> */
    public array $coursesData = [
        ['id' => 1, 'name' => 'Course 1'],
        ['id' => 2, 'name' => 'Course 2'],
    ];

    public function shows(): array
    {
        ++$this->callCount;

        return [];
    }

    public function techniques(): array
    {
        ++$this->callCount;

        return [];
    }

    public function technique(int $id): array
    {
        ++$this->callCount;

        return [];
    }

    public function courses(): array
    {
        ++$this->callCount;

        return $this->coursesData;
    }

    public function museum(): array
    {
        ++$this->callCount;

        return [];
    }

    public function museumHistory(string $slug): array
    {
        ++$this->callCount;

        return [];
    }

    public function collection(): array
    {
        ++$this->callCount;

        return [];
    }

    public function collectionItem(int $id): array
    {
        ++$this->callCount;

        return [];
    }
}

/**
 * @internal
 */
final class FileCachedTotemApiServiceTest extends CIUnitTestCase
{
    private string $cachePath;
    private MockTotemApiService $mockInner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cachePath = WRITEPATH . 'cache/test-totem/';
        $this->cleanCacheDirectory();
        $this->mockInner = new MockTotemApiService();
    }

    protected function tearDown(): void
    {
        $this->cleanCacheDirectory();
        parent::tearDown();
    }

    private function cleanCacheDirectory(): void
    {
        if (!is_dir($this->cachePath)) {
            return;
        }

        $files = glob($this->cachePath . '*.cache');
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        if (is_dir($this->cachePath)) {
            rmdir($this->cachePath);
        }
    }

    public function testCachesDataFromInnerService(): void
    {
        $service = new FileCachedTotemApiService($this->mockInner, $this->cachePath, 60);

        // First call should hit inner service
        $result1 = $service->courses();
        $this->assertSame(2, count($result1));
        $this->assertSame(1, $this->mockInner->callCount);

        // Second call should use cache
        $result2 = $service->courses();
        $this->assertSame(2, count($result2));
        $this->assertSame(1, $this->mockInner->callCount); // Still 1, not 2
    }

    public function testCreatesCacheDirectoryIfNotExists(): void
    {
        $this->cleanCacheDirectory();
        $this->assertDirectoryDoesNotExist($this->cachePath);

        new FileCachedTotemApiService($this->mockInner, $this->cachePath, 60);

        $this->assertDirectoryExists($this->cachePath);
    }

    public function testClearCacheRemovesAllCacheFiles(): void
    {
        $service = new FileCachedTotemApiService($this->mockInner, $this->cachePath, 60);

        // Populate cache
        $service->courses();
        $service->shows();

        $this->assertNotEmpty(glob($this->cachePath . '*.cache'));

        // Clear cache
        $service->clearCache();

        $this->assertEmpty(glob($this->cachePath . '*.cache') ?: []);
    }

    public function testDifferentKeysForDifferentMethods(): void
    {
        $service = new FileCachedTotemApiService($this->mockInner, $this->cachePath, 60);

        // Call different methods
        $service->courses();
        $service->shows();
        $service->techniques();

        // Should have called inner service 3 times
        $this->assertSame(3, $this->mockInner->callCount);

        // Should have 3 cache files
        $cacheFiles = glob($this->cachePath . '*.cache');
        $this->assertIsArray($cacheFiles);
        $this->assertSame(3, count($cacheFiles));
    }

    public function testTechniqueWithDifferentIdsCreatesSeparateCache(): void
    {
        $service = new FileCachedTotemApiService($this->mockInner, $this->cachePath, 60);

        $service->technique(1);
        $service->technique(2);
        $service->technique(1); // Should use cache

        // Should have called inner service 2 times (for ids 1 and 2)
        $this->assertSame(2, $this->mockInner->callCount);
    }
}
