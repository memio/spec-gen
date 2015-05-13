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

use Memio\Model\Method;
use Memio\PrettyPrinter\PrettyPrinter;
use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;

/**
 * As a developer using phpspec, I want generated methods to be saved in my source code.
 *
 * Given a class I'm specifying
 * And a new method in it
 * When it has been generated
 * Then it should be inserted at the end of the file
 */
class InsertGeneratedMethodListener
{
    const START_OF_CLASS = '/^{$/';
    const END_OF_CLASS = '/^}$/';
    const NAME_SPACE = '/^namespace /';
    const USE_STATEMENT = '/^use /';

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
        $fullyQualifiedNames = $generatedMethod->file->allFullyQualifiedNames();
        $method = array_shift($generatedMethod->file->getStructure()->allMethods()); // $object should contain only one method, the generated one.

        $file = $this->editor->open($fileName);
        $this->insertUseStatements($file, $fullyQualifiedNames);
        $this->insertMethod($file, $method);
        $this->editor->save($file);
    }

    /**
     * @param File  $file
     * @param array $fullyQualifiedNames
     */
    private function insertUseStatements(File $file, array $fullyQualifiedNames)
    {
        foreach ($fullyQualifiedNames as $fullyQualifiedName) {
            $fullyQualifiedClassName = $fullyQualifiedName->getFullyQualifiedName();
            if (!$this->editor->hasBelow($file, "/^use $fullyQualifiedClassName;$/", 0)) {
                $this->insertUseStatement($file, $fullyQualifiedClassName);
            }
        }
    }

    /**
     * @param File   $file
     * @param string $fullyQualifiedClassName
     */
    private function insertUseStatement(File $file, $fullyQualifiedClassName)
    {
        if (!$this->editor->hasBelow($file, self::USE_STATEMENT, 0)) {
            $this->editor->jumpBelow($file, self::NAME_SPACE, 0);
            $this->editor->insertBelow($file, '');
        } else {
            $lastLineNumber = $file->getCurrentLineNumber() - 1;
            $file->setCurrentLineNumber($lastLineNumber);
            $this->editor->jumpAbove($file, self::USE_STATEMENT);
        }
        $this->editor->insertBelow($file, "use $fullyQualifiedClassName;");
    }

    /**
     * @param File   $file
     * @param Method $method
     */
    private function insertMethod(File $file, Method $method)
    {
        $generatedCode = $this->prettyPrinter->generateCode($method);
        $this->editor->jumpBelow($file, self::END_OF_CLASS, 0);
        $this->editor->insertAbove($file, $generatedCode);
        $above = $file->getCurrentLineNumber() - 1;
        if (0 === preg_match(self::START_OF_CLASS, $file->getLine($above))) {
            $this->editor->insertAbove($file, '');
        }
    }
}
