<?php
namespace AsseticAdditions\Scssphp\Formatter;

/*
 * Copyright (c) 2013 Daniel Corn
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */



/**
 * Format the scssphp output without @media blocks
 */
class DebugFormatter extends \scss_formatter_nested {
	/**
	 * Number of the current selector
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
	 * @param  string $name  	Property name
	 * @param  mixed $value 	Property value
	 * @return string
	 */
	public function property($name, $value) {
		if ($name[0] === '*') {
			return '/* Skipping: ' . parent::property($name, $value) . ' */';
		} else if (strpos($value, '\9') !== FALSE) {
			return '/* Skipping: ' . parent::property($name, $value) . ' */';
		} else if ($name === 'filter') {
			return '/* Skipping: ' . parent::property($name, $value) . ' */';
		// } else if (substr($value, 7) === 'progid:') {
		// 	return '/* Skipping: ' . parent::property($name, $value) . ' */';
		}

		// Remove "!default" if found
		if (substr($value, -8) === '!default') {
			$value = substr($value, 0, -8);
		}
		return parent::property($name, $value);
		// return parent::property($name, $value) . ' /* Selector Nr:' . ++$this->selectorNumber . ' */';
	}
}