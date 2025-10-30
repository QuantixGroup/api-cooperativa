<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Foundation\Testing\RefreshDatabase;

$projectRoot = dirname(__DIR__);
$testsMigrations = $projectRoot . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'migrations';
$dest = $projectRoot . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';

$trackingFile = $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'testing_migrations.lst';
@unlink($trackingFile);

if (is_dir($testsMigrations)) {
    foreach (glob($testsMigrations . '/*.php') as $file) {
        $basename = basename($file);
        $target = $dest . DIRECTORY_SEPARATOR . $basename;
        if (!file_exists($target)) {
            copy($file, $target);
            file_put_contents($trackingFile, $target . PHP_EOL, FILE_APPEND);
        }
    }
}

afterAll(function () {
    $list = storage_path('framework/testing_migrations.lst');
    if (file_exists($list)) {
        $lines = file($list, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $f) {
            if (file_exists($f)) {
                @unlink($f);
            }
        }
        @unlink($list);
    }
});

uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
