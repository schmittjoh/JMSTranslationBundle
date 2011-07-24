<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\TranslationBundle\Translation\Extractor;

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use JMS\TranslationBundle\Twig\RemovingNodeVisitor;

use JMS\TranslationBundle\Translation\ExtractorInterface;
use JMS\TranslationBundle\Model\MessageCatalogue;
use Symfony\Component\Finder\Finder;

/**
 * File-based extractor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FileExtractor implements ExtractorInterface
{
    private $twig;
    private $visitors;
    private $phpParser;
    private $pattern;
    private $directory;
    private $removingTwigVisitor;
    private $excludedNames = array();
    private $excludedDirs = array();
    private $logger;

    public function __construct(\Twig_Environment $twig, LoggerInterface $logger, array $visitors)
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->visitors = $visitors;
        $this->phpParser = new \PHPParser_Parser();

        foreach ($this->twig->getNodeVisitors() as $visitor) {
            if ($visitor instanceof RemovingNodeVisitor) {
                $this->removingTwigVisitor = $visitor;
                break;
            }
        }
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $directory));
        }

        $this->directory = $directory;
    }

    public function setExcludedDirs(array $dirs)
    {
        $this->excludedDirs = $dirs;
    }

    public function setExcludedNames(array $names)
    {
        $this->excludedNames = $names;
    }

    public function setPattern(array $pattern)
    {
        $this->pattern = $pattern;
    }

    public function extract()
    {
        if (!empty($this->removingTwigVisitor)) {
            $this->removingTwigVisitor->setEnabled(false);
        }

        $finder = Finder::create()->in($this->directory)->exclude($this->excludedDirs);
        $catalogue = new MessageCatalogue();

        foreach ($this->excludedNames as $name) {
            $finder->notName($name);
        }

        if ($this->pattern) {
            $finder->name($this->pattern);
        }

        foreach ($finder as $file) {
            $visitingMethod = 'visitFile';
            $visitingArgs = array($file, $catalogue);

            if (false !== $pos = strrpos($file, '.')) {
                $extension = substr($file, $pos + 1);

                if ('php' === $extension) {
                    $visitingMethod = 'visitPhpFile';
                    $lexer = new \PHPParser_Lexer(file_get_contents($file));
                    $ast = $this->phpParser->parse($lexer);
                    $visitingArgs[] = $ast;
                } else if ('twig' === $extension) {
                    $visitingMethod = 'visitTwigFile';
                    $visitingArgs[] = $this->twig->parse($this->twig->tokenize(file_get_contents($file), $file));
                }
            }

            $this->logger->debug(sprintf('Parsing file "%s"', $file));

            foreach ($this->visitors as $visitor) {
                call_user_func_array(array($visitor, $visitingMethod), $visitingArgs);
            }
        }

        if (!empty($this->removingTwigVisitor)) {
            $this->removingTwigVisitor->setEnabled(true);
        }

        return $catalogue;
    }
}