# Memio's SpecGen [![Travis CI](https://travis-ci.org/memio/spec-gen.png)](https://travis-ci.org/memio/spec-gen)

This extension for [phpspec](http://phpspec.net/) provides a powerful code generator:

* method generation:
    * it inserts method at the end of the class
    * it typehints arguments (uses interface when possible)
    * it names object arguments after their type (strips `Interface` suffix from names)
    * it names scalar arguments after a generic name (`argument`)
    * it adds number on names that could collide (e.g. `$argument1, $argument2`)
* constructor generation, same as method except:
    * it inserts constructor at the begining of the class
    * it inserts properties with initialization for each constructor arguments

> **Note**: Currently it is not possible to provide custom templates to SpecGen
> (it is not compatible with phpspec templates).

## Installation

First install it using [Composer](https://getcomposer.org/download):

    composer require --dev memio/spec-gen:^0.9

Then enable it in `phpspec.yml`:

```
extensions:
    Memio\SpecGen\MemioSpecGenExtension: ~
```

> **Version guide**:
>
> * using phpspec 5? Then use spec-gen v0.9
> * using phpspec 4? Then use spec-gen v0.8
> * using phpspec 3 and PHP 7? Then use spec-gen v0.7
> * using phpspec 3 and PHP 5.6? Then use spec-gen v0.6
> * using phpspec 2? Then use spec-gen v0.4

## Full example

Let's write the following specification:

```php
<?php

namespace spec\Vendor\Project;

use Vendor\Project\Service\Filesystem;
use Vendor\Project\File;
use PhpSpec\ObjectBehavior;

class TextEditorSpec extends ObjectBehavior
{
    const FILENAME = '/tmp/file.txt';
    const FORCE_FILE_CREATION = true;

    function let(Filesystem $filesystem)
    {
        $this->beConstructedWith($filesystem);
    }

    function it_creates_new_files(File $file, Filesystem $filesystem)
    {
        $filesystem->exists(self::FILENAME)->willReturn(false);
        $filesystem->create(self::FILENAME)->willReturn($file);

        $this->open(self::FILENAME, self::FORCE_FILE_CREATION)->shouldBe($file);
    }
}
```

Running the tests (`phpspec run`) will generate the following class:

```php
<?php

namespace Vendor\Project;

use Vendor\Project\Service\Filesystem;

class TextEditor
{
    private $filesystem;

    public function __construct(Filesytem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function open(string $argument1, bool $argument2)
    {
    }
}
```

Now we can start naming those generic arguments and write the code.

## Want to know more?

You can see the current and past versions using one of the following:

* the `git tag` command
* the [releases page on Github](https://github.com/memio/spec-gen/releases)
* the file listing the [changes between versions](CHANGELOG.md)

And finally some meta documentation:

* [copyright and MIT license](LICENSE)
* [versioning and branching models](VERSIONING.md)
* [contribution instructions](CONTRIBUTING.md)

## Roadmap

* return type hints
* method body (mirror of test method body)
* better argument naming (based on names used in test)
