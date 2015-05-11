<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\GenerateMethod;

use Memio\Model\File as FileModel;
use Memio\Model\Method as MethodModel;
use Memio\Model\Object as ObjectModel;
use Memio\PrettyPrinter\PrettyPrinter;
use Memio\SpecGen\Fixtures\Repository;
use Memio\SpecGen\GenerateMethod\GeneratedMethod;
use Memio\SpecGen\GenerateMethod\InsertGeneratedMethodListener;
use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;
use PhpSpec\ObjectBehavior;

class InsertGeneratedMethodListenerSpec extends ObjectBehavior
{
    const FILE_NAME = 'src/Vendor/Project/MyClass.php';
    const CLASS_NAME = 'MyClass';
    const METHOD_NAME = 'myMethod';

    function let(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->beConstructedWith($editor, $prettyPrinter);
    }

    function it_inserts_the_generated_method(
        Editor $editor,
        File $file,
        FileModel $fileModel,
        MethodModel $methodModel,
        ObjectModel $objectModel,
        PrettyPrinter $prettyPrinter
    ) {
        $generatedCode = Repository::find('generated_method');
        $generatedMethod = new GeneratedMethod($fileModel->getWrappedObject());
        $fileModel->getFilename()->willReturn(self::FILE_NAME);
        $fileModel->getStructure()->willReturn($objectModel);
        $objectModel->allMethods()->willReturn(array($methodModel));

        $prettyPrinter->generateCode($methodModel)->willReturn($generatedCode);
        $editor->open(self::FILE_NAME)->willReturn($file);
        $editor->jumpBelow($file, InsertGeneratedMethodListener::END_OF_CLASS)->shouldBeCalled();
        $editor->insertAbove($file, $generatedCode)->shouldBeCalled();
        $file->getCurrentLineNumber()->willReturn(42);
        $file->getLine(41)->willReturn('{');
        $editor->save($file)->shouldBeCalled();

        $this->onGeneratedMethod($generatedMethod);
    }

    function it_also_inserts_an_empty_line_above_if_the_class_is_not_empty(
        Editor $editor,
        File $file,
        FileModel $fileModel,
        MethodModel $methodModel,
        ObjectModel $objectModel,
        PrettyPrinter $prettyPrinter
    ) {
        $generatedCode = Repository::find('generated_method');
        $generatedMethod = new GeneratedMethod($fileModel->getWrappedObject());
        $fileModel->getFilename()->willReturn(self::FILE_NAME);
        $fileModel->getStructure()->willReturn($objectModel);
        $objectModel->allMethods()->willReturn(array($methodModel));

        $prettyPrinter->generateCode($methodModel)->willReturn($generatedCode);
        $editor->open(self::FILE_NAME)->willReturn($file);
        $editor->jumpBelow($file, InsertGeneratedMethodListener::END_OF_CLASS)->shouldBeCalled();
        $editor->insertAbove($file, $generatedCode)->shouldBeCalled();
        $file->getCurrentLineNumber()->willReturn(42);
        $file->getLine(41)->willReturn('    }');
        $editor->insertAbove($file, '')->shouldBeCalled();
        $editor->save($file)->shouldBeCalled();

        $this->onGeneratedMethod($generatedMethod);
    }
}
