<?php

namespace StoryGen\Scripts;

/**
 * A simple benchmarking tool for API endpoints
 *
 * Usage: php src/StoryGen/Scripts/BenchmarkEndpoints.php
 */
class BenchmarkEndpoints
{
    /**
     * List of endpoints to benchmark
     *
     * @var array
     */
    private array $endpoints = [
        '/api/prompt' => 'GET',
        '/api/prompt?age_group=children' => 'GET',
        '/api/prompt?age_group=teen' => 'GET',
        '/api/prompt?age_group=adult' => 'GET',
        '/api/prompt?count=5' => 'GET',
        '/api/data' => 'GET',
    ];

    /**
     * Number of requests to make for each endpoint
     *
     * @var int
     */
    private int $iterations = 10;

    /**
     * Base URL for the API
     *
     * @var string
     */
    private string $baseUrl = 'http://localhost:8080';

    /**
     * Run the benchmark
     *
     * @return void
     */
    public function run(): void
    {
        echo "Starting API Endpoint Benchmarks\n";
        echo "================================\n\n";

        foreach ($this->endpoints as $endpoint => $method) {
            $this->benchmarkEndpoint($endpoint, $method);
        }

        echo "\nBenchmark completed.\n";
    }

    /**
     * Benchmark a specific endpoint
     *
     * @param string $endpoint The endpoint to benchmark
     * @param string $method The HTTP method to use
     * @return void
     */
    private function benchmarkEndpoint(string $endpoint, string $method): void
    {
        echo "Benchmarking $method $endpoint\n";

        $times = [];
        $totalSize = 0;

        for ($i = 0; $i < $this->iterations; $i++) {
            $start = microtime(true);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            $end = microtime(true);
            $time = ($end - $start) * 1000; // Convert to milliseconds

            $times[] = $time;
            $totalSize += strlen($response);
        }

        $avgTime = array_sum($times) / count($times);
        $minTime = min($times);
        $maxTime = max($times);
        $avgSize = $totalSize / $this->iterations;

        echo "  Average response time: " . number_format($avgTime, 2) . " ms\n";
        echo "  Min response time: " . number_format($minTime, 2) . " ms\n";
        echo "  Max response time: " . number_format($maxTime, 2) . " ms\n";
        echo "  Average response size: " . number_format($avgSize, 0) . " bytes\n";
        echo "\n";
    }
}
