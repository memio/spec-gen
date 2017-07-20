<?php

/*
 * This file is part of the Memio project.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\Memio\SpecGen;

use PHPUnit\Framework\TestCase;

class GeneratorTestCase extends TestCase
{
    protected function getFixtureFilename(): string
    {
        $path = $this->getPath();
        $method = $this->getMethod();
        $skeletonFilename = __DIR__.'/../../../fixtures/skeleton/'.$path.'/'.$method.'.txt';
        $filename = __DIR__.'/../../../fixtures/actual/'.$path.'/'.$method.'.txt';
        if (file_exists($filename)) {
            unlink($filename);
        }
        copy($skeletonFilename, $filename);

        return $filename;
    }

    protected function assertExpectedCode(string $actualFilename)
    {
        $expectedFilename = __DIR__.'/../../../fixtures/expected/'.$this->getPath().'/'.$this->getMethod().'.txt';

        $this->assertFileEquals($expectedFilename, $actualFilename);
    }

    private function getPath(): string
    {
        $trace = debug_backtrace();
        $testFqcn = $trace[2]['class'];
        $type = substr($testFqcn, strlen('Memio\SpecGen\Tests\Generator\\'));

        return str_replace('\\', '/', $type);
    }

    private function getMethod(): string
    {
        $trace = debug_backtrace();

        return $trace[2]['function'];
    }
}
