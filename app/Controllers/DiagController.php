<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Temporary diagnostic controller.
 */
final class DiagController extends Controller
{
    public function index(): ResponseInterface
    {
        $tests = [];
        
        // Test 1: Basic response
        $tests['basic'] = 'ok';
        
        // Test 2: Check env
        $tests['env'] = [
            'TOTEM_API_URL' => env('TOTEM_API_URL') ?: 'not set',
            'TOTEM_API_KEY' => env('TOTEM_API_KEY') ? 'set' : 'not set',
        ];
        
        // Test 3: Try to load Totem config
        try {
            $config = config('Totem');
            $tests['config'] = $config !== null ? 'loaded' : 'null';
        } catch (\Exception $e) {
            $tests['config'] = 'error: ' . $e->getMessage();
        }
        
        // Test 4: Try to create service
        try {
            $api = service('totemApi');
            $tests['service'] = $api !== null ? 'created' : 'null';
            $tests['service_class'] = get_class($api);
        } catch (\Exception $e) {
            $tests['service'] = 'error: ' . $e->getMessage();
        }
        
        // Test 5: Try to call courses
        try {
            $api = service('totemApi');
            $courses = $api->courses();
            $tests['courses'] = 'ok, count=' . count($courses);
        } catch (\Exception $e) {
            $tests['courses'] = 'error: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($tests);
    }
}
