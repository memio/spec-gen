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

use Gnugat\Redaktilo\File;
use Memio\Model\File as FileModel;
use Memio\Model\FullyQualifiedName as FullyQualifiedNameModel;
use Memio\Model\Method as MethodModel;
use Memio\Model\Objekt as ObjectModel;
use Memio\Model\Property as PropertyModel;
use Memio\SpecGen\CodeEditor\CodeEditor;
use Memio\SpecGen\CodeEditor\InsertConstructor;
use Memio\SpecGen\CodeEditor\InsertProperties;
use Memio\SpecGen\CodeEditor\InsertUseStatements;
use Memio\SpecGen\GenerateConstructor\GeneratedConstructor;
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
        File $file
    ) {
        $insertUseStatements = Argument::type(InsertUseStatements::class);
        $insertProperties = Argument::type(InsertProperties::class);
        $insertConstructor = Argument::type(InsertConstructor::class);

        $fullyQualifiedName = new FullyQualifiedNameModel('Vendor\Project\MyClass');
        $method = new MethodModel(self::METHOD_NAME);
        $property = new PropertyModel('myProperty');
        $object = (new ObjectModel('Vendor\Project\MyClass'))
            ->addMethod($method)
            ->addProperty($property);
        $fileModel = (new FileModel(self::FILE_NAME))
            ->addFullyQualifiedName($fullyQualifiedName)
            ->setStructure($object);

        $generatedConstructor = new GeneratedConstructor($fileModel);

        $codeEditor->open(self::FILE_NAME)->willReturn($file);
        $codeEditor->handle($insertUseStatements)->shouldBeCalled();
        $codeEditor->handle($insertProperties)->shouldBeCalled();
        $codeEditor->handle($insertConstructor)->shouldBeCalled();
        $codeEditor->save($file)->shouldBeCalled();

        $this->onGeneratedConstructor($generatedConstructor);
    }
}
