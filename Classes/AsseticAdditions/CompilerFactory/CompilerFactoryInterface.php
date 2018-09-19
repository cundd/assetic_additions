<?php

namespace AsseticAdditions\CompilerFactory;

use AsseticAdditions\CompilerInterface;

/**
 * Factory for compiler implementations
 */
interface CompilerFactoryInterface
{
    /**
     * Returns the compiler
     *
     * @return CompilerInterface
     */
    public function createCompiler();
}
