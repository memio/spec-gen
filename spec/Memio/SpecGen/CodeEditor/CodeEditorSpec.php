<?php

namespace spec\Memio\SpecGen\Editor;

use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;
use Memio\SpecGen\CommandBus\Command;
use Memio\SpecGen\CommandBus\CommandBus;
use PhpSpec\ObjectBehavior;

class CodeEditorSpec extends ObjectBehavior
{
    const FILENAME = 'src/Vendor/Project/MyClass.php';

    function let(CommandBus $commandBus, Editor $editor)
    {
        $this->beConstructedWith($commandBus, $editor);
    }

    function it_can_open_existing_files(File $file, Editor $editor)
    {
        $editor->open(self::FILENAME)->willReturn($file);

        $this->open(self::FILENAME)->shouldBe($file);
    }

    function it_can_save_files(File $file, Editor $editor)
    {
        $editor->save($file)->shouldBeCalled();

        $this->save($file);
    }

    function it_handles_commands(Command $command, CommandBus $commandBus)
    {
        $commandBus->handle($command)->shouldBeCalled();

        $this->handle($command);
    }
}
