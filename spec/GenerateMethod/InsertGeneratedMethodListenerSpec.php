<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateMethod;

use Gnugat\Redaktilo;
use Memio\Model;
use Memio\SpecGen\CodeEditor\CodeEditor;
use Memio\SpecGen\CodeEditor\InsertMethod;
use Memio\SpecGen\CodeEditor\InsertUseStatements;
use Memio\SpecGen\GenerateMethod\GeneratedMethod;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertGeneratedMethodListenerSpec extends ObjectBehavior
{
    const FILE_NAME = 'src/MyClass.php';
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = 'myMethod';
    const ARGUMENT_1_TYPE = 'Vendor\Project\Strategy';
    const ARGUMENT_1_NAME = 'strategy1';
    const ARGUMENT_2_TYPE = 'int';
    const ARGUMENT_2_NAME = 'argument1';

    function let(CodeEditor $codeEditor)
    {
        $this->beConstructedWith($codeEditor);
    }

    function it_inserts_the_generated_method(
        CodeEditor $codeEditor,
    ) {
        $modelFile = new Model\File(self::FILE_NAME);
        $modelFile->structure = (new Model\Objekt(self::CLASS_NAME))
            ->addMethod((new Model\Method(self::METHOD_NAME))
                ->addArgument(new Model\Argument(self::ARGUMENT_1_TYPE, self::ARGUMENT_1_NAME))
                ->addArgument(new Model\Argument(self::ARGUMENT_2_TYPE, self::ARGUMENT_2_NAME))
            )
        ;
        $generatedMethod = new GeneratedMethod($modelFile);

        $redaktiloFile = Redaktilo\File::fromString('');

        $insertUseStatements = Argument::type(InsertUseStatements::class);
        $insertMethod = Argument::type(InsertMethod::class);

        $codeEditor->open(self::FILE_NAME)->willReturn($redaktiloFile);
        $codeEditor->handle($insertUseStatements)->shouldBeCalled();
        $codeEditor->handle($insertMethod)->shouldBeCalled();
        $codeEditor->save($redaktiloFile)->shouldBeCalled();

        $this->onGeneratedMethod($generatedMethod);
    }
}
