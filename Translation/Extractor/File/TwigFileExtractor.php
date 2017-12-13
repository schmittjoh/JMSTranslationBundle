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

namespace JMS\TranslationBundle\Translation\Extractor\File;

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Translation\FileSourceFactory;
use Symfony\Bridge\Twig\Node\TransNode;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;

class TwigFileExtractor extends \Twig_BaseNodeVisitor implements FileVisitorInterface
{
    /**
     * @var FileSourceFactory
     */
    private $fileSourceFactory;

    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @var MessageCatalogue
     */
    private $catalogue;

    /**
     * @var \Twig_NodeTraverser
     */
    private $traverser;

    /**
     * @var array
     */
    private $stack = array();

    /**
     * TwigFileExtractor constructor.
     * @param \Twig_Environment $env
     * @param FileSourceFactory $fileSourceFactory
     */
    public function __construct(\Twig_Environment $env, FileSourceFactory $fileSourceFactory)
    {
        $this->fileSourceFactory = $fileSourceFactory;
        $this->traverser = new \Twig_NodeTraverser($env, array($this));
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node
     */
    protected function doEnterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        $this->stack[] = $node;

        if ($node instanceof TransNode) {
            $id = $node->getNode('body')->getAttribute('data');
            $domain = 'messages';
            // Older version of Symfony are storing null in the node instead of omitting it
            if ($node->hasNode('domain') && null !== $domainNode = $node->getNode('domain')) {
                $domain = $domainNode->getAttribute('value');
            }

            $message = new Message($id, $domain);
            $message->addSource($this->fileSourceFactory->create($this->file, $node->getTemplateLine()));
            $this->catalogue->add($message);
        } elseif ($node instanceof \Twig_Node_Expression_Filter) {
            $name = $node->getNode('filter')->getAttribute('value');

            if ('trans' === $name || 'transchoice' === $name) {
                $idNode = $node->getNode('node');
                if (!$idNode instanceof \Twig_Node_Expression_Constant) {
                    return $node;
                    // FIXME: see below
//                     throw new \RuntimeException(sprintf('Cannot infer translation id from node "%s". Please refactor to only translate constants.', get_class($idNode)));
                }
                $id = $idNode->getAttribute('value');

                $index = 'trans' === $name ? 1 : 2;
                $domain = 'messages';
                $arguments = $node->getNode('arguments');
                if ($arguments->hasNode($index)) {
                    $argument = $arguments->getNode($index);
                    if (!$argument instanceof \Twig_Node_Expression_Constant) {
                        return $node;
                        // FIXME: Throw exception if there is some way for the user to turn this off
                        //        on a case-by-case basis, similar to @Ignore in PHP
                    }

                    $domain = $argument->getAttribute('value');
                }

                $message = new Message($id, $domain);
                $message->addSource($this->fileSourceFactory->create($this->file, $node->getTemplateLine()));

                for ($i=count($this->stack)-2; $i>=0; $i-=1) {
                    if (!$this->stack[$i] instanceof \Twig_Node_Expression_Filter) {
                        break;
                    }

                    $name = $this->stack[$i]->getNode('filter')->getAttribute('value');
                    if ('desc' === $name || 'meaning' === $name) {
                        $arguments = $this->stack[$i]->getNode('arguments');
                        if (!$arguments->hasNode(0)) {
                            throw new RuntimeException(sprintf('The "%s" filter requires exactly one argument, the description text.', $name));
                        }

                        $text = $arguments->getNode(0);
                        if (!$text instanceof \Twig_Node_Expression_Constant) {
                            throw new RuntimeException(sprintf('The first argument of the "%s" filter must be a constant expression, such as a string.', $name));
                        }

                        $message->{'set'.$name}($text->getAttribute('value'));
                    } elseif ('trans' === $name) {
                        break;
                    }
                }

                $this->catalogue->add($message);
            }
        }

        return $node;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param \Twig_Node $ast
     */
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
        $this->traverseEmbeddedTemplates($ast);
    }

    /**
     * If the current Twig Node has embedded templates, we want to travese these templates
     * in the same manner as we do the main twig template to ensure all translations are
     * caught.
     *
     * @param \Twig_Node $node
     */
    private function traverseEmbeddedTemplates(\Twig_Node $node)
    {
        $templates = $node->getAttribute('embedded_templates');

        foreach ($templates as $template) {
            $this->traverser->traverse($template);
            if ($template->hasAttribute('embedded_templates')) {
                $this->traverseEmbeddedTemplates($template);
            }
        }
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node
     */
    protected function doLeaveNode(\Twig_Node $node, \Twig_Environment $env)
    {
        array_pop($this->stack);

        return $node;
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     */
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue)
    {
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param array $ast
     */
    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
    }
}
