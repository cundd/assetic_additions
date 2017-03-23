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
use Assetic\Filter\DependencyExtractorInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Loads SCSS files using Wellington (https://getwt.io/)
 */
class WtFilter extends AbstractLibSassFilter implements DependencyExtractorInterface, LibSassFilterInterface
{
    /**
     * WtFilter constructor.
     *
     * @param string $binaryPath
     */
    public function __construct($binaryPath = '/usr/bin/wt')
    {
        parent::__construct($binaryPath);
    }

    /**
     * @inheritDoc
     */
    protected function configureProcess(AssetInterface $asset, ProcessBuilder $processBuilder)
    {
        $processBuilder->add('-I')->add(implode(':', $this->getIncludePaths($asset)));

        if ($this->style) {
            $processBuilder->add('-s')->add($this->style);
        }
        if ($this->emitSourceMap) {
            $processBuilder->add('--source-map');
        }

        $processBuilder->add('compile');
        $processBuilder->add($asset->getSourceRoot() . '/' . $asset->getSourcePath());
    }
}
