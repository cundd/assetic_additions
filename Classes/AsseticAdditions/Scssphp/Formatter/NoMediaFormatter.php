<?php

namespace AsseticAdditions\Scssphp\Formatter;

/**
 * Format the scssphp output without @media blocks
 *
 * @deprecated
 */
class NoMediaFormatter extends \scss_formatter_nested
{
    /**
     * The entry point of the formatting
     *
     * @param  object $block
     * @return void
     */
    public function block($block)
    {
        if ($block->type == 'root') {
            // Remove all @media blocks
            foreach ($block->children as $index => &$child) {
                if ($child->selectors && substr('' . implode('', $child->selectors), 0, 6) === '@media') {
                    $child = null;
                }
            }
        }
        parent::block($block);
    }
}