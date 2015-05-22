# Memio's SpecGen [![SensioLabsInsight](https://insight.sensiolabs.com/projects/7cea8bf7-2f9f-4d34-a7e8-55fabeed867f/mini.png)](https://insight.sensiolabs.com/projects/7cea8bf7-2f9f-4d34-a7e8-55fabeed867f) [![Travis CI](https://travis-ci.org/memio/spec-gen.png)](https://travis-ci.org/memio/spec-gen)

This extension for [phpspec](http://phpspec.net/) provides a powerful code generator:

* it typehints object, array and callable arguments
* it names object arguments after their type
* it names scalar arguments after a generic name (`argument`)
* it adds number on names that could collide (e.g. `$argument1, $argument2`)

## Installation

First install it using [Composer](https://getcomposer.org/download):

    composer require --dev memio/spec-gen:~0.1

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

use PhpSpec\ObjectBehavior;
use Vendor\Project\Message\Request;

class RequestHandlerSpec extends ObjectBehavior
{
    const MASTER_REQUEST = 1;
    const CATCH_EXCEPTIONS = true;

    function it_catches_exceptions_from_master_request(Request $request)
    {
        $this->handle($request, self::MASTER_REQUEST, self::CATCH_EXCEPTIONS);
    }
}
```

Running the tests (`phpspec run`) will generate the following class:

```php
<?php

namespace Vendor\Project;

use Vendor\Project\Message\Request;

class RequestHandler
{
    public function handle(Request $request, $argument1, $argument2)
    {
    }
}
```

Now we can start naming those generic arguments, and write the code.

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

* Generating constructors
* Generating method PHPdoc
* Generating License header (based on `composer.json` data)
