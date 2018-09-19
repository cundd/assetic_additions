<?php

namespace AsseticAdditions\Scssphp\Formatter;

/**
 * Format the scssphp output without @media blocks
 *
 * @deprecated
 */
class DebugFormatter extends \scss_formatter_nested
{
    /**
     * Number of the current selector
     *
     * @var integer
     */
    protected $selectorNumber = 0;


    // /**
    //  * The entry point of the formatting
    //  * @param  object $block
    //  * @return void
    //  */
    // public function block($block) {
    // 	if ($block->type == 'root') {
    // 		// Remove all @media blocks
    // 		foreach ($block->children as $index => &$child) {
    // 			if ($child->selectors && substr('' . implode('', $child->selectors), 0, 6) === '@media') {
    // 				$child = NULL;
    // 			}
    // 		}
    // 	}
    // 	parent::block($block);
    // }

    /**
     * Render a property
     *
     * @param  string $name  Property name
     * @param  mixed  $value Property value
     * @return string
     */
    public function property($name, $value)
    {
        if ($name[0] === '*') {
            return '/* Skipping: ' . parent::property($name, $value) . ' */';
        } else {
            if (strpos($value, '\9') !== false) {
                return '/* Skipping: ' . parent::property($name, $value) . ' */';
            } else {
                if ($name === 'filter') {
                    return '/* Skipping: ' . parent::property($name, $value) . ' */';
                    // } else if (substr($value, 7) === 'progid:') {
                    // 	return '/* Skipping: ' . parent::property($name, $value) . ' */';
                }
            }
        }

        // Remove "!default" if found
        if (substr($value, -8) === '!default') {
            $value = substr($value, 0, -8);
        }

        return parent::property($name, $value);
        // return parent::property($name, $value) . ' /* Selector Nr:' . ++$this->selectorNumber . ' */';
    }
}