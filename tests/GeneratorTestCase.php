<?php

/*
 * This file is part of the Memio project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\Tests;

use Memio\Memio\Config\Build;
use PHPUnit_Framework_TestCase;

class GeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    protected function getFixtureFilename()
    {
        $path = $this->getPath();
        $method = $this->getMethod();
        $skeletonFilename = __DIR__.'/../fixtures/skeleton/'.$path.'/'.$method.'.txt';
        $filename = __DIR__.'/../fixtures/actual/'.$path.'/'.$method.'.txt';
        if (file_exists($filename)) {
            unlink($filename);
        }
        copy($skeletonFilename, $filename);

        return $filename;
    }

    /**
     * @param string $filename
     */
    protected function assertExpectedCode($actualFilename)
    {
        $expectedFilename = __DIR__.'/../fixtures/expected/'.$this->getPath().'/'.$this->getMethod().'.txt';

        $this->assertFileEquals($expectedFilename, $actualFilename);
    }

    /**
     * @return string
     */
    private function getPath()
    {
        $trace = debug_backtrace();
        $testFqcn = $trace[2]['class'];
        $type = substr($testFqcn, strlen('Memio\SpecGen\Tests\Generator\\'));

        return str_replace('\\', '/', $type);
    }

    /**
     * @return string
     */
    private function getMethod()
    {
        $trace = debug_backtrace();

        return $trace[2]['function'];
    }
}
