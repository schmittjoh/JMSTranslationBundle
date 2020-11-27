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

namespace JMS\TranslationBundle\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OutputLogger implements LoggerInterface
{
    public const EMERG  = 1;
    public const ALERT  = 2;
    public const CRIT   = 4;
    public const ERR    = 8;
    public const WARN   = 16;
    public const NOTICE = 32;
    public const INFO   = 64;
    public const DEBUG  = 128;
    public const ALL    = 255;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var int
     */
    private $level = self::ALL;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->emerg($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emerg($message, array $context = [])
    {
        if (0 === ($this->level & self::EMERG)) {
            return;
        }

        $this->output->writeln('<error>' . $message . '</error>');
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = [])
    {
        if (0 === ($this->level & self::ALERT)) {
            return;
        }

        $this->output->writeln('<error>' . $message . '</error>');
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->crit($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function crit($message, array $context = [])
    {
        if (0 === ($this->level & self::CRIT)) {
            return;
        }

        $this->output->writeln('<error>' . $message . '</error>');
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->err($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function err($message, array $context = [])
    {
        if (0 === ($this->level & self::ERR)) {
            return;
        }

        $this->output->writeln('<error>' . $message . '</error>');
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->warn($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warn($message, array $context = [])
    {
        if (0 === ($this->level & self::WARN)) {
            return;
        }

        $this->output->writeln($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = [])
    {
        if (0 === ($this->level & self::NOTICE)) {
            return;
        }

        $this->output->writeln($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        if (0 === ($this->level & self::INFO)) {
            return;
        }

        $this->output->writeln($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = [])
    {
        if (0 === ($this->level & self::DEBUG)) {
            return;
        }

        $this->output->writeln($message);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (0 === ($this->level & $level)) {
            return;
        }

        $this->output->writeln($message);
    }
}
