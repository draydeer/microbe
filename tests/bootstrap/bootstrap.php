<?php

ini_set('memory_limit', '512M');

require_once(__DIR__ . '/../../vendor/autoload.php');

$autoload = new Composer\Autoload\ClassLoader();

$autoload->addPsr4(
    'Microbe\\',
    __DIR__ . '/../../src/'
);
$autoload->add(
    'Mocks\\',
    __DIR__ . '/Classes/'
);
$autoload->add(
    'Stubs\\',
    __DIR__ . '/Classes/'
);
$autoload->add(
    'Test\\',
    __DIR__ . '/Classes/'
);
$autoload->add(
    'TestMocks\\',
    __DIR__ . '/Classes/'
);

$autoload->register();

function benchmark($iterations = 0)
{
    static $t = null;

    if ($t === null) {
        $t = microtime(1);

        if (is_string($iterations)) {
            message('Benchmarking: ' . $iterations . '...');
        }
    } else {
        $u = microtime(1);

        if ($iterations > 0) {
            message('Benchmark took: ' . ($u - $t) . ' sec. for ' . $iterations . ' iteration(s) with ' . ($u - $t) / $iterations . ' sec. per one, estimating ' . 1 / (($u - $t) / $iterations) . ' in 1 sec.');
        } else {
            message('Benchmark took: ' . ($u - $t) . ' sec.');
        }

        $t = null;
    }
}

function debug($value)
{
    fwrite(STDERR, print_r($value, true));
}

function message($value)
{
    fwrite(STDOUT, $value . PHP_EOL);
}
