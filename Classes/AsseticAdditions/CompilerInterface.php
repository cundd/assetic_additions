<?php

namespace AsseticAdditions;

/**
 * Description of a compiler
 */
interface CompilerInterface
{
    /**
     * @param string $content
     * @return string
     */
    public function compile($content);
}
