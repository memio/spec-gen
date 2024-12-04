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
use Memio\SpecGen\CodeEditor\InsertUseStatements;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertUseStatementsHandlerSpec extends ObjectBehavior
{
    function let(
        Redaktilo\Editor $redaktiloEditor,
        InsertUseStatementHandler $insertUseStatementHandler
    ) {
        $this->beConstructedWith($redaktiloEditor, $insertUseStatementHandler);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_insert_use_statements_command(InsertUseStatements $insertUseStatements)
    {
        $this->supports($insertUseStatements)->shouldBe(true);
    }

    function it_inserts_use_statements(
        Redaktilo\Editor $redaktiloEditor,
        InsertUseStatementHandler $insertUseStatementHandler
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
}
FILE);
        $modelFullyQualifiedNames = [
            new Model\FullyQualifiedName('Vendor\Project\Dependency'),
        ];
        $insertUseStatements = new InsertUseStatements($redaktiloFile, $modelFullyQualifiedNames);

        $useStatementPattern = '/^use Vendor\\\\Project\\\\Dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
use Vendor\Project\Dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $useStatementPattern, 0)->willReturn(false);
        $insertUseStatement = Argument::Type(InsertUseStatement::class);
        $insertUseStatementHandler->handle($insertUseStatement)->shouldBeCalled();

        $this->handle($insertUseStatements);
    }

    function it_does_not_insert_the_same_use_statement_twice(
        Redaktilo\Editor $redaktiloEditor,
        InsertUseStatementHandler $insertUseStatementHandler
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

use Vendor\Project\Dependency;

class MyClass
{
}
FILE);
        $modelFullyQualifiedNames = [
            new Model\FullyQualifiedName('Vendor\Project\Dependency'),
        ];
        $insertUseStatements = new InsertUseStatements($redaktiloFile, $modelFullyQualifiedNames);

        $useStatementPattern = '/^use Vendor\\\\Project\\\\Dependency;$/';
        $generatedCode =<<<'GENERATED_CODE'
use Vendor\Project\Dependency;
GENERATED_CODE;

        $redaktiloEditor->hasBelow($redaktiloFile, $useStatementPattern, 0)->willReturn(true);
        $insertUseStatement = Argument::Type(InsertUseStatement::class);
        $insertUseStatementHandler->handle($insertUseStatement)->shouldNotBeCalled();

        $this->handle($insertUseStatements);
    }
}
