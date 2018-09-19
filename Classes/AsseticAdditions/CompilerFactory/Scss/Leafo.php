<?php

namespace AsseticAdditions\CompilerFactory\Scss;

use AsseticAdditions\CompilerFactory\CompilerFactoryInterface;
use AsseticAdditions\CompilerInterface;

/**
 * Factory for Leafo SCSS implementations
 */
class Leafo implements CompilerFactoryInterface
{
    /**
     * Returns the compiler
     *
     * @return CompilerInterface
     */
    public function createCompiler()
    {
        if (class_exists('scssc')) {
            $implementation = 'scssc';
        } elseif (class_exists('Leafo\\ScssPhp\\Compiler')) {
            $implementation = 'Leafo\\ScssPhp\\Compiler';
        } else {
            throw new \LogicException('Could not find a matching compiler from Leafo');
        }

        return new $implementation();
    }
}
