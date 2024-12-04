<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateConstructor;

use Gnugat\Redaktilo;
use Memio\Model;
use Memio\SpecGen\CodeEditor\CodeEditor;
use Memio\SpecGen\CodeEditor\InsertConstructor;
use Memio\SpecGen\CodeEditor\InsertProperties;
use Memio\SpecGen\CodeEditor\InsertUseStatements;
use Memio\SpecGen\GenerateConstructor\GeneratedConstructor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertGeneratedConstructorListenerSpec extends ObjectBehavior
{
    const FILE_NAME = 'src/MyClass.php';
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = '__construct';
    const ARGUMENT_1_TYPE = 'Vendor\Prophecy\Dependency';
    const ARGUMENT_1_NAME = 'dependency';
    const ARGUMENT_2_TYPE = 'string';
    const ARGUMENT_2_NAME = 'rootDir';

    function let(CodeEditor $codeEditor)
    {
        $this->beConstructedWith($codeEditor);
    }

    function it_inserts_the_generated_method(
        CodeEditor $codeEditor,
    ) {
        $modelFile = new Model\File(self::FILE_NAME);
        $modelFile->addFullyQualifiedName(new Model\FullyQualifiedName(self::ARGUMENT_1_TYPE));
        $modelFile->structure = (new Model\Objekt(self::CLASS_NAME))
            ->addProperty((new Model\Property(self::ARGUMENT_1_NAME))
                ->makePrivate()
            )
            ->addProperty((new Model\Property(self::ARGUMENT_2_NAME))
                ->makePrivate()
            )
            ->addMethod((new Model\Method(self::METHOD_NAME))
                ->addArgument(new Model\Argument(self::ARGUMENT_1_TYPE, self::ARGUMENT_1_NAME))
                ->addArgument(new Model\Argument(self::ARGUMENT_2_TYPE, self::ARGUMENT_2_NAME))
            )
        ;
        $generatedConstructor = new GeneratedConstructor($modelFile);

        $redaktiloFile = Redaktilo\File::fromString('');

        $insertUseStatements = Argument::type(InsertUseStatements::class);
        $insertProperties = Argument::type(InsertProperties::class);
        $insertConstructor = Argument::type(InsertConstructor::class);

        $codeEditor->open(self::FILE_NAME)->willReturn($redaktiloFile);
        $codeEditor->handle($insertUseStatements)->shouldBeCalled();
        $codeEditor->handle($insertProperties)->shouldBeCalled();
        $codeEditor->handle($insertConstructor)->shouldBeCalled();
        $codeEditor->save($redaktiloFile)->shouldBeCalled();

        $this->onGeneratedConstructor($generatedConstructor);
    }
}
