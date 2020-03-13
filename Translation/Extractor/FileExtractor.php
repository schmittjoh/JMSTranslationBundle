<?php

declare(strict_types=1);

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
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;
use JMS\TranslationBundle\Twig\DefaultApplyingNodeVisitor;
use JMS\TranslationBundle\Twig\RemovingNodeVisitor;
use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\Source;

/**
 * File-based extractor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FileExtractor implements ExtractorInterface, LoggerAwareInterface
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $visitors;

    /**
     * @var Parser
     */
    private $phpParser;

    /**
     * @var array
     */
    private $pattern;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var RemovingNodeVisitor|NodeVisitorInterface
     */
    private $removingTwigVisitor;

    /**
     * @var DefaultApplyingNodeVisitor|RemovingNodeVisitor|NodeVisitorInterface
     */
    private $defaultApplyingTwigVisitor;

    /**
     * @var array
     */
    private $excludedNames = [];

    /**
     * @var array
     */
    private $excludedDirs = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Environment $twig
     * @param LoggerInterface $logger
     * @param array $visitors
     */
    public function __construct(Environment $twig, LoggerInterface $logger, array $visitors)
    {
        $this->twig = $twig;
        $this->visitors = $visitors;
        $this->setLogger($logger);
        $lexer = new Lexer();
        if (class_exists(ParserFactory::class)) {
            $factory = new ParserFactory();
            $this->phpParser = $factory->create(ParserFactory::PREFER_PHP7, $lexer);
        } else {
            $this->phpParser = new Parser($lexer);
        }

        foreach ($this->twig->getNodeVisitors() as $visitor) {
            if ($visitor instanceof RemovingNodeVisitor) {
                $this->removingTwigVisitor = $visitor;
            }
            if ($visitor instanceof DefaultApplyingNodeVisitor) {
                $this->defaultApplyingTwigVisitor = $visitor;
            }
        }
    }

    public function reset()
    {
        $this->excludedNames = [];
        $this->excludedDirs  = [];
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        foreach ($this->visitors as $visitor) {
            if (!$visitor instanceof LoggerAwareInterface) {
                continue;
            }

            $visitor->setLogger($logger);
        }
    }

    public function setDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $directory));
        }

        $this->directory = $directory;
    }

    /**
     * @param array $dirs
     */
    public function setExcludedDirs(array $dirs)
    {
        $this->excludedDirs = $dirs;
    }

    /**
     * @param array $names
     */
    public function setExcludedNames(array $names)
    {
        $this->excludedNames = $names;
    }

    /**
     * @param array $pattern
     */
    public function setPattern(array $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return MessageCatalogue
     *
     * @throws \Exception
     */
    public function extract()
    {
        if (!empty($this->removingTwigVisitor)) {
            $this->removingTwigVisitor->setEnabled(false);
        }
        if (!empty($this->defaultApplyingTwigVisitor)) {
            $this->defaultApplyingTwigVisitor->setEnabled(false);
        }

        $finder = Finder::create()->in($this->directory);

        foreach ($this->excludedDirs as $dir) {
            $finder->exclude($dir);
        }

        foreach ($this->excludedNames as $name) {
            $finder->notName($name);
        }

        if ($this->pattern) {
            $finder->name($this->pattern);
        }

        $curTwigLoader = $this->twig->getLoader();
        $this->twig->setLoader(new ArrayLoader([]));

        try {
            $catalogue = new MessageCatalogue();
            foreach ($finder as $file) {
                $visitingMethod = 'visitFile';
                $visitingArgs = [$file, $catalogue];

                $this->logger->debug(sprintf('Parsing file "%s"', $file));

                if (false !== $pos = strrpos((string) $file, '.')) {
                    $extension = substr((string) $file, $pos + 1);

                    if ('php' === $extension) {
                        try {
                            $ast = $this->phpParser->parse(file_get_contents((string) $file));
                        } catch (Error $ex) {
                            throw new \RuntimeException(sprintf('Could not parse "%s": %s', $file, $ex->getMessage()), $ex->getCode(), $ex);
                        }

                        $visitingMethod = 'visitPhpFile';
                        $visitingArgs[] = $ast;
                    } elseif ('twig' === $extension) {
                        $visitingMethod = 'visitTwigFile';
                        $visitingArgs[] = $this->twig->parse($this->twig->tokenize(new Source(file_get_contents((string) $file), (string) $file)));
                    }
                }

                foreach ($this->visitors as $visitor) {
                    call_user_func_array([$visitor, $visitingMethod], $visitingArgs);
                }
            }

            if (null !== $curTwigLoader) {
                $this->twig->setLoader($curTwigLoader);
            }

            if (!empty($this->removingTwigVisitor)) {
                $this->removingTwigVisitor->setEnabled(true);
            }
            if (!empty($this->defaultApplyingTwigVisitor)) {
                $this->defaultApplyingTwigVisitor->setEnabled(true);
            }

            return $catalogue;
        } catch (\Throwable $ex) {
            if (null !== $curTwigLoader) {
                $this->twig->setLoader($curTwigLoader);
            }

            throw $ex;
        }
    }
}
