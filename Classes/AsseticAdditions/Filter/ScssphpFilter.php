<?php
namespace AsseticAdditions\Filter;

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

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
#use AsseticAdditions\Filter\AbstractFilter;



/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 */
class ScssphpFilter extends AbstractFilter implements FilterInterface {
	/**
	 * Indicates if compass should be used
	 * @var boolean
	 */
	protected $compass = FALSE;

	/**
	 * The class name of the formatter to use
	 * @var string
	 */
	protected $formatter = 'scss_formatter_nested';

	/**
	 * The import paths for the compiler to use
	 * @var array
	 */
	protected $importPaths = array();

	/**
	 * Compile/filter the asset
	 * @param  AssetInterface $asset
	 * @return void
	 */
	public function filterLoad(AssetInterface $asset) {
		$content = '';
		$root = $asset->getSourceRoot();
		$path = $asset->getSourcePath();

		$lc = new \scssc();
		if ($this->compass) {
			new \scss_compass($lc);
		}

		// Enable strict file imports, if supported
		if (method_exists($lc, 'setThrowExceptionIfImportFileNotFound')) {
			$lc->setThrowExceptionIfImportFileNotFound(TRUE);
		}

		// Set the formatter
		$lc->setFormatter($this->formatter);

		if ($root && $path) {
			$lc->addImportPath(dirname($root . '/' . $path));
		}
		foreach ($this->importPaths as $path) {
			$lc->addImportPath($path);
		}

		try {
			$content = $lc->compile($asset->getContent());
		} catch (\Exception_ScssException $exception) {
			throw $exception;
		}
		$asset->setContent($content);
	}

	/**
	 * Returns the class name of the formatter to use
	 *
	 * @return string
	 */
	public function getFormatter() {
		 return $this->formatter;
	}

	/**
	 * Sets the class name of the formatter to use
	 *
	 * @param string $newformatter
	 */
	public function setFormatter($formatter) {
		 $this->formatter = $formatter;
		 return $this;
	}

	/**
	 * Enable/disable compass for the filter
	 * @param  boolean $enable
	 * @return void
	 */
	public function enableCompass($enable = TRUE) {
		 $this->compass = (bool) $enable;
	}

	/**
	 * Returns if compass is enabled
	 * @return boolean
	 */
	public function isCompassEnabled() {
		 return $this->compass;
	}

	/**
	 * Sets the import paths for the compiler to use
	 * @param array $paths Array of directory paths
	 */
	public function setImportPaths(array $paths) {
		 $this->importPaths = $paths;
	}

	/**
	 * Add an import path for the compiler to use
	 * @param string $path
	 */
	public function addImportPath($path) {
		 $this->importPaths[] = $path;
	}

	public function filterDump(AssetInterface $asset) {}
}