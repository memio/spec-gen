<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateConstructor;

use Memio\Model\File as FileModel;
use Memio\Model\FullyQualifiedName as FullyQualifiedNameModel;
use Memio\Model\Method as MethodModel;
use Memio\Model\Object as ObjectModel;
use Memio\Model\Property as PropertyModel;
use Memio\SpecGen\CodeEditor\CodeEditor;
use Memio\SpecGen\GenerateConstructor\GeneratedConstructor;
use Gnugat\Redaktilo\File;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertGeneratedConstructorListenerSpec extends ObjectBehavior
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
        ObjectModel $objectModel,
        PropertyModel $propertyModel
    ) {
        $insertUseStatements = Argument::type('Memio\SpecGen\CodeEditor\InsertUseStatements');
        $insertProperties = Argument::type('Memio\SpecGen\CodeEditor\InsertProperties');
        $insertConstructor = Argument::type('Memio\SpecGen\CodeEditor\InsertConstructor');

        $generatedConstructor = new GeneratedConstructor($fileModel->getWrappedObject());
        $fileModel->allFullyQualifiedNames()->willReturn(array($fullyQualifiedNameModel));
        $fileModel->getFilename()->willReturn(self::FILE_NAME);
        $fileModel->getStructure()->willReturn($objectModel);
        $objectModel->allProperties()->willReturn(array($propertyModel));
        $objectModel->allMethods()->willReturn(array($methodModel));

        $codeEditor->open(self::FILE_NAME)->willReturn($file);
        $codeEditor->handle($insertUseStatements)->shouldBeCalled();
        $codeEditor->handle($insertProperties)->shouldBeCalled();
        $codeEditor->handle($insertConstructor)->shouldBeCalled();
        $codeEditor->save($file)->shouldBeCalled();

        $this->onGeneratedConstructor($generatedConstructor);
    }
}
