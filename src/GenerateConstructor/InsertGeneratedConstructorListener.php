<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateConstructor;

use Memio\SpecGen\CodeEditor\CodeEditor;
use Memio\SpecGen\CodeEditor\InsertConstructor;
use Memio\SpecGen\CodeEditor\InsertProperties;
use Memio\SpecGen\CodeEditor\InsertUseStatements;

/**
 * As a developer using phpspec, I want generated cosntructors to be saved in my source code.
 *
 * Given a class I'm specifying
 * And a new constructor in it
 * When it has been generated
 * Then it should be inserted at the begining of the file
 * And use statements should be inserted when necessary
 * And properties for the constructor's arguments should be inserted
 */
class InsertGeneratedConstructorListener
{
    private $codeEditor;

    public function __construct(CodeEditor $codeEditor)
    {
        $this->codeEditor = $codeEditor;
    }

    public function onGeneratedConstructor(
        GeneratedConstructor $generatedConstructor
    ): void {
        // GeneratedMethod only contains one single method (the generated one).
        $file = $this->codeEditor->open($generatedConstructor->file->filename);
        $this->codeEditor->handle(new InsertUseStatements(
            $file,
            $generatedConstructor->file->fullyQualifiedNames,
        ));
        $this->codeEditor->handle(new InsertProperties(
            $file,
            $generatedConstructor->file->structure->properties,
        ));
        $this->codeEditor->handle(new InsertConstructor(
            $file,
            $generatedConstructor->file->structure->methods[0],
        ));
        $this->codeEditor->save($file);
    }
}
