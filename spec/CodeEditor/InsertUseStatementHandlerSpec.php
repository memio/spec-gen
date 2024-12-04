<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Faugeron <faugeron.loic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Memio\SpecGen\CodeEditor;

use Gnugat\Redaktilo;
use Memio\Model;
use Memio\SpecGen\CodeEditor\InsertUseStatement;
use Memio\SpecGen\CodeEditor\InsertUseStatementHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;

class InsertUseStatementHandlerSpec extends ObjectBehavior
{
    function let(Redaktilo\Editor $redaktiloEditor)
    {
        $this->beConstructedWith($redaktiloEditor);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_insert_use_statement_command(InsertUseStatement $insertUseStatement)
    {
        $this->supports($insertUseStatement)->shouldBe(true);
    }

    function it_does_not_insert_use_statement_in_same_namespace(
        Redaktilo\Editor $redaktiloEditor
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\Project;

class MyClass
{
}
FILE);
        $modelFullyQualifiedName = new Model\FullyQualifiedName('Vendor\Project\Dependency');
        $insertUseStatement = new InsertUseStatement($redaktiloFile, $modelFullyQualifiedName);

        $nameSpacePattern = '/^namespace Vendor\\\\Project;$/';
        $generatedCode =<<<'GENERATED_CODE'
use Vendor\Project\Dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $nameSpacePattern, 0)->willReturn(true);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldNotBeCalled();

        $this->handle($insertUseStatement);
    }

    function it_does_not_insert_use_statement_twice(
        Redaktilo\Editor $redaktiloEditor
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

use Vendor\Project\Dependency;

class MyClass
{
}
FILE);
        $modelFullyQualifiedName = new Model\FullyQualifiedName('Vendor\Project\Dependency');
        $insertUseStatement = new InsertUseStatement($redaktiloFile, $modelFullyQualifiedName);

        $nameSpacePattern = '/^namespace Vendor\\\\Project;$/';
        $useStatementPattern = '/^use Vendor\\\\Project\\\\Dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
use Vendor\Project\Dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $nameSpacePattern, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, $useStatementPattern, 0)->willReturn(true);
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldNotBeCalled();

        $this->handle($insertUseStatement);
    }

    function it_inserts_first_use_statement(
        Redaktilo\Editor $redaktiloEditor
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
}
FILE);
        $modelFullyQualifiedName = new Model\FullyQualifiedName('Vendor\Project\Dependency');
        $insertUseStatement = new InsertUseStatement($redaktiloFile, $modelFullyQualifiedName);

        $nameSpacePattern = '/^namespace Vendor\\\\Project;$/';
        $useStatementPattern = '/^use Vendor\\\\Project\\\\Dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
use Vendor\Project\Dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $nameSpacePattern, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, $useStatementPattern, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertUseStatementHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloEditor->hasAbove($redaktiloFile, InsertUseStatementHandler::USE_STATEMENT)->willReturn(false);
        $redaktiloEditor->jumpAbove($redaktiloFile, InsertUseStatementHandler::NAME_SPACE)->shouldBeCalled();
        $redaktiloEditor->insertBelow($redaktiloFile, '')->shouldBeCalled();
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertUseStatement);
    }

    function it_inserts_use_statement_at_the_end_of_use_statement_block(
        Redaktilo\Editor $redaktiloEditor
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

use Vendor\DifferentProject\OtherClass;

class MyClass
{
}
FILE);
        $modelFullyQualifiedName = new Model\FullyQualifiedName('Vendor\Project\Dependency');
        $insertUseStatement = new InsertUseStatement($redaktiloFile, $modelFullyQualifiedName);

        $nameSpacePattern = '/^namespace Vendor\\\\Project;$/';
        $useStatementPattern = '/^use Vendor\\\\Project\\\\Dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
use Vendor\Project\Dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $nameSpacePattern, 0)->willReturn(false);
        $redaktiloEditor->hasBelow($redaktiloFile, $useStatementPattern, 0)->willReturn(false);
        $redaktiloEditor->jumpBelow($redaktiloFile, InsertUseStatementHandler::CLASS_ENDING, 0)->shouldBeCalled();
        $redaktiloEditor->hasAbove($redaktiloFile, InsertUseStatementHandler::USE_STATEMENT)->willReturn(true);
        $redaktiloEditor->jumpAbove($redaktiloFile, InsertUseStatementHandler::USE_STATEMENT)->shouldBeCalled();
        $redaktiloEditor->insertBelow($redaktiloFile, $generatedCode)->shouldBeCalled();

        $this->handle($insertUseStatement);
    }
}
