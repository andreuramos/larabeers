<?php

namespace CodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ExceptionClassMustHaveExceptionSuffixSniff implements Sniff
{

    public function register()
    {
        return [T_EXTENDS];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $parentClassName = $tokens[$phpcsFile->findNext([T_STRING], $stackPtr)]['content'];
        if (!$this->endsWithException($parentClassName)) {
            return;
        }

        $className = $tokens[$phpcsFile->findPrevious([T_STRING], $stackPtr)]['content'];
        if (!$this->endsWithException($className)) {
            $phpcsFile->addFixableError(
                'Exception class must end with \'Exception\' suffix',
                $stackPtr - 1,
                self::class
            );
        }
    }

    private function endsWithException($content): bool
    {
        return substr($content, -strlen('Exception')) === 'Exception';
    }
}
