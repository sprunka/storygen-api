<?php

namespace StoryGen\Scripts;

require_once __DIR__ . '/../../../vendor/autoload.php';

// Run the benchmark
$benchmark = new BenchmarkEndpoints();
$benchmark->run();
