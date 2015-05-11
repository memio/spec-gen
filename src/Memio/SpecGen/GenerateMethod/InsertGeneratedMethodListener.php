<?php

/*
 * This file is part of the memio/spec-gen package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Memio\SpecGen\GenerateMethod;

use Memio\PrettyPrinter\PrettyPrinter;
use Gnugat\Redaktilo\Editor;

/**
 * As a developer using phpspec, I want generated methods to be saved in my source code
 *
 * Given a class I'm specifying
 * And a new method in it
 * When it has been generated
 * Then it should be inserted at the end of the file
 */
class InsertGeneratedMethodListener
{
    const START_FO_CLASS = '/^{$/';
    const END_OF_CLASS = '/^}$/';

    /**
     * @var Editor
     */
    private $editor;

    /**
     * @var PrettyPrinter
     */
    private $prettyPrinter;

    /**
     * @param Editor        $editor
     * @param PrettyPrinter $prettyPrinter
     */
    public function __construct(Editor $editor, PrettyPrinter $prettyPrinter)
    {
        $this->editor = $editor;
        $this->prettyPrinter = $prettyPrinter;
    }

    /**
     * @param GeneratedMethod $generatedMethod
     */
    public function onGeneratedMethod(GeneratedMethod $generatedMethod)
    {
        $fileName = $generatedMethod->file->getFilename();
        $method = array_shift($generatedMethod->file->getStructure()->allMethods()); // $object should contain only one method, the generated one.

        $generatedCode = $this->prettyPrinter->generateCode($method);
        $file = $this->editor->open($fileName);
        $this->editor->jumpBelow($file, self::END_OF_CLASS);
        $this->editor->insertAbove($file, $generatedCode);
        $above = $file->getCurrentLineNumber() - 1;
        if (0 === preg_match(self::START_FO_CLASS, $file->getLine($above))) {
            $this->editor->insertAbove($file, '');
        }
        $this->editor->save($file);
    }
}
