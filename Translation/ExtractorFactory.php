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

namespace JMS\TranslationBundle\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;

class ExtractorFactory
{

    protected $twig;
    protected $logger;
    protected $visitors;
    protected $docParser;
    protected $env;
    protected $fileSourceFactory;

    /**
     * ExtractorFactory constructor.
     *
     *
     *
     * @param string $kernelRoot
     */
    public function __construct($twig, $logger, $visitors, $docParser, $env, $fileSourceFactory)
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->visitors= $visitors;
        $this->docParser = $docParser;
        $this->env = $env;
        $this->fileSourceFactory = $fileSourceFactory;


    }

    /**
     * Generate a new FileSource with a relative path.
     *
     * @param \SplFileInfo $file
     * @param null|int     $line
     * @param null|int     $column
     *
     * @return FileSource
     */
    public function createFileExtractor()
    {
        switch(\Twig_Environment::MAJOR_VERSION){
            case 1:
                return new FileExtractor($this->twig, $this->logger, $this->visitors);
            default:
                return new FileExtractorTwig2($this->twig, $this->logger, $this->visitors);
        }
    }

    public function createFormExtractor()
    {

        switch(\Twig_Environment::MAJOR_VERSION){
            case 1:
                return new FormExtractor($this->docParser, $this->fileSourceFactory);
            default:
                return new FormExtractorTwig2($this->docParser, $this->fileSourceFactory);
        }


    }

    public function createTwigFileFileExtractor()
    {
        switch(\Twig_Environment::MAJOR_VERSION){
            case 1:
                return new TwigFileExtractor($this->env, $this->fileSourceFactory);
            default:
                return new FormExtractorTwig2($this->env, $this->fileSourceFactory);
        }

    }


}
