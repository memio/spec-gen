<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('bin')
    ->exclude('doc')
    ->exclude('vendor')
    ->exclude('fixtures')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->fixers([
        '-visibility',
        '-multiple_use',
        'short_array_syntax',
    ])
    ->setUsingCache(true)
    ->finder($finder)
;
