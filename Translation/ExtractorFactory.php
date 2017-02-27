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

use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\FormExtractorTwig2;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractorTwig2;
use JMS\TranslationBundle\Translation\Extractor\FileExtractorTwig2;

/**
 * Class ExtractorFactory
 *
 * Create TwigFileExtractor and FormExtractor for the Major Versions of Twig
 *
 * @package JMS\TranslationBundle\Translation
 */
class ExtractorFactory
{

    protected $twig;
    protected $docParser;
    protected $fileSourceFactory;

    /**
     * ExtractorFactory constructor.
     *
     * @param \Twig_Environment $twig
     * @param DocParser         $docParser
     * @param FileSourceFactory $fileSourceFactory
     */
    public function __construct(\Twig_Environment $twig, DocParser $docParser, FileSourceFactory $fileSourceFactory)
    {
        $this->twig = $twig;
        $this->docParser = $docParser;
        $this->fileSourceFactory = $fileSourceFactory;
    }

    /**
     * Create FormExtractor for the relevent Twig version.
     *
     * @return FormExtractor|FormExtractorTwig2
     */
    public function createFormExtractor()
    {

        switch(\Twig_Environment::MAJOR_VERSION){
            case 1:
                return new FormExtractor($this->docParser, $this->fileSourceFactory);
            default:
                return new FormExtractorTwig2($this->docParser, $this->fileSourceFactory);
        }


    }

    /**
     * Create TwigFileExtractor for the relevent Twig version
     * @return TwigFileExtractor|TwigFileExtractorTwig2
     */
    public function createTwigFileExtractor()
    {
        switch(\Twig_Environment::MAJOR_VERSION){
            case 1:
                return new TwigFileExtractor($this->twig, $this->fileSourceFactory);
            default:
                return new TwigFileExtractorTwig2($this->twig, $this->fileSourceFactory);
        }

    }

}
