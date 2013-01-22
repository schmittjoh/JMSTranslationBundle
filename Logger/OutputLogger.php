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

namespace JMS\TranslationBundle\Logger;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class OutputLogger implements LoggerInterface
{
    const EMERG  = 1;
    const ALERT  = 2;
    const CRIT   = 4;
    const ERR    = 8;
    const WARN   = 16;
    const NOTICE = 32;
    const INFO   = 64;
    const DEBUG  = 128;
    const ALL    = 255;

    private $output;
    private $level = self::ALL;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function emergency($message, array $context = array())
    {
        return $this->emerg($message, $context);
    }

    public function emerg($message, array $context = array())
    {
        if (0 === ($this->level & self::EMERG)) {
            return;
        }

        $this->output->writeln('<error>'.$message.'</error>');
    }

    public function alert($message, array $context = array())
    {
        if (0 === ($this->level & self::ALERT)) {
            return;
        }

        $this->output->writeln('<error>'.$message.'</error>');
    }

    public function critical($message, array $context = array())
    {
        return $this->crit($message, $context);
    }

    public function crit($message, array $context = array())
    {
        if (0 === ($this->level & self::CRIT)) {
            return;
        }

        $this->output->writeln('<error>'.$message.'</error>');
    }

    public function error($message, array $context = array())
    {
        return $this->err($message, $context);
    }

    public function err($message, array $context = array())
    {
        if (0 === ($this->level & self::ERR)) {
            return;
        }

        $this->output->writeln('<error>'.$message.'</error>');
    }

    public function warning($message, array $context = array())
    {
        return $this->warn($message, $context);
    }

    public function warn($message, array $context = array())
    {
        if (0 === ($this->level & self::WARN)) {
            return;
        }

        $this->output->writeln($message);
    }

    public function notice($message, array $context = array())
    {
        if (0 === ($this->level & self::NOTICE)) {
            return;
        }

        $this->output->writeln($message);
    }

    public function info($message, array $context = array())
    {
        if (0 === ($this->level & self::INFO)) {
            return;
        }

        $this->output->writeln($message);
    }

    public function debug($message, array $context = array())
    {
        if (0 === ($this->level & self::DEBUG)) {
            return;
        }

        $this->output->writeln($message);
    }

    public function log($level, $message, array $context = array())
    {
        if (0 === ($this->level & $level)) {
            return;
        }

        $this->output->writeln($message);
    }
}