<?php
declare(strict_types=1);

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
    public function compile(string $content): string;
}
