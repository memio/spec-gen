# Memio's SpecGen [![SensioLabsInsight](https://insight.sensiolabs.com/projects/7cea8bf7-2f9f-4d34-a7e8-55fabeed867f/mini.png)](https://insight.sensiolabs.com/projects/7cea8bf7-2f9f-4d34-a7e8-55fabeed867f) [![Travis CI](https://travis-ci.org/memio/spec-gen.png)](https://travis-ci.org/memio/spec-gen)

This extension for [phpspec](http://phpspec.net/) provides a powerful code generator:

* method generation:
    * it inserts method at the end of the class
    * it typehints object (uses interface when possible), array and callable arguments
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

    composer require --dev memio/spec-gen:^0.6

Then enable it in `phpspec.yml`:

```
extensions:
  - Memio\SpecGen\MemioSpecGenExtension
```

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
    }

    public function open($argument1, $argument2)
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

* Generating method/property PHPdoc
* Generating License header (based on `composer.json` data)
* Sorting "use statements" by aphabetical order
* Accepting custom templates
