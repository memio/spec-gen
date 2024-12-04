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
use Memio\SpecGen\CodeEditor\InsertProperties;
use Memio\SpecGen\CodeEditor\InsertProperty;
use Memio\SpecGen\CodeEditor\InsertPropertyHandler;
use Memio\SpecGen\CommandBus\CommandHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertPropertiesHandlerSpec extends ObjectBehavior
{
    function let(
        InsertPropertyHandler $insertPropertyHandler
    ) {
        $this->beConstructedWith($insertPropertyHandler);
    }

    function it_is_a_command_handler()
    {
        $this->shouldImplement(CommandHandler::class);
    }

    function it_supports_insert_properties_command(
        InsertProperties $insertProperties
    ) {
        $this->supports($insertProperties)->shouldBe(true);
    }

    function it_inserts_properties(
        InsertPropertyHandler $insertPropertyHandler
    ) {
        $redaktiloFile = Redaktilo\File::fromString(<<<'FILE'
<?php

namespace Vendor\OtherProject;

class MyClass
{
}
FILE);
        $modelProperties = [
            new Model\Property('dependency'),
            new Model\Property('filename'),
        ];
        $insertProperties = new InsertProperties($redaktiloFile, $modelProperties);

        $insertProperty = Argument::Type(InsertProperty::class);
        $insertPropertyHandler->handle($insertProperty)->shouldBeCalled();

        $this->handle($insertProperties);
    }
}
