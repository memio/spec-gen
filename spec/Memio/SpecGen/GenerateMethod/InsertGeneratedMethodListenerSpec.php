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

use Memio\Model\File as FileModel;
use Memio\Model\FullyQualifiedName as FullyQualifiedNameModel;
use Memio\Model\Method as MethodModel;
use Memio\Model\Objekt as ObjectModel;
use Memio\SpecGen\CodeEditor\CodeEditor;
use Memio\SpecGen\CodeEditor\InsertMethod;
use Memio\SpecGen\CodeEditor\InsertUseStatements;
use Memio\SpecGen\GenerateMethod\GeneratedMethod;
use Gnugat\Redaktilo\File;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertGeneratedMethodListenerSpec extends ObjectBehavior
{
    const FILE_NAME = 'src/Vendor/Project/MyClass.php';
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = 'myMethod';

    function let(CodeEditor $codeEditor)
    {
        $this->beConstructedWith($codeEditor);
    }

    function it_inserts_the_generated_method(
        CodeEditor $codeEditor,
        File $file,
        FileModel $fileModel,
        FullyQualifiedNameModel $fullyQualifiedNameModel,
        MethodModel $methodModel,
        ObjectModel $objectModel
    ) {
        $insertUseStatements = Argument::type(InsertUseStatements::class);
        $insertMethod = Argument::type(InsertMethod::class);

        $generatedMethod = new GeneratedMethod($fileModel->getWrappedObject());
        $fileModel->allFullyQualifiedNames()->willReturn([$fullyQualifiedNameModel]);
        $fileModel->getFilename()->willReturn(self::FILE_NAME);
        $fileModel->getStructure()->willReturn($objectModel);
        $objectModel->allMethods()->willReturn([$methodModel]);

        $codeEditor->open(self::FILE_NAME)->willReturn($file);
        $codeEditor->handle($insertUseStatements)->shouldBeCalled();
        $codeEditor->handle($insertMethod)->shouldBeCalled();
        $codeEditor->save($file)->shouldBeCalled();

        $this->onGeneratedMethod($generatedMethod);
    }
}
