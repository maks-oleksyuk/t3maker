<?php

declare(strict_types=1);

namespace Mirko\T3maker\Utility;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;

/**
 * @internal
 */
final class PrettyPrinter extends Standard
{
    /**
     * Overridden to fix indentation problem with tabs.
     *
     * If the original source code uses tabs, then the tokenizer
     * will see this as "1" indent level, and will indent new lines
     * with just 1 space. By changing 1 indent to 4, we effectively
     * "correct" this problem when printing.
     *
     * For code that is even further indented (e.g. 8 spaces),
     * the printer uses the first indentation (here corrected
     * from 1 space to 4) and already (without needing any other
     * changes) adds 4 spaces onto that. This is why we don't
     * also need to handle indent levels of 5, 9, etc: these
     * do not occur (at least in the code we generate);
     */
    protected function setIndentLevel(int $level): void
    {
        if ($level === 1) {
            $level = 4;
        }

        parent::setIndentLevel($level);
    }

    /**
     * Overridden to change coding standards.
     *
     * Before:
     *      public function getFoo() : string
     *
     * After
     *      public function getFoo(): string
     */
    protected function PStmtClassMethod(ClassMethod $node)
    {
        $classMethod = parent::pStmt_ClassMethod($node);

        if ($node->returnType) {
            $classMethod = str_replace(') :', '):', $classMethod);
        }

        return $classMethod;
    }
}
