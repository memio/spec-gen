<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateMethod;

use Memio\SpecGen\CodeEditor\CodeEditor;
use Memio\SpecGen\CodeEditor\InsertMethod;
use Memio\SpecGen\CodeEditor\InsertUseStatements;

/**
 * As a developer using phpspec, I want generated methods to be saved in my source code.
 *
 * Given a class I'm specifying
 * And a new method in it
 * When it has been generated
 * Then it should be inserted at the end of the file
 * And use statements should be inserted when necessary
 */
class InsertGeneratedMethodListener
{
    private $codeEditor;

    public function __construct(CodeEditor $codeEditor)
    {
        $this->codeEditor = $codeEditor;
    }

    public function onGeneratedMethod(GeneratedMethod $generatedMethod): void
    {
        $fileName = $generatedMethod->file->getFilename();
        $fullyQualifiedNames = $generatedMethod->file->allFullyQualifiedNames();
        $allMethods = $generatedMethod->file->getStructure()->allMethods();
        $method = array_shift($allMethods); // $object should contain only one method, the generated one.

        $file = $this->codeEditor->open($fileName);
        $this->codeEditor->handle(new InsertUseStatements($file, $fullyQualifiedNames));
        $this->codeEditor->handle(new InsertMethod($file, $method));
        $this->codeEditor->save($file);
    }
}
