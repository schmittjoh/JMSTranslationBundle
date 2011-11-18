<?php

namespace JMS\TranslationBundle\Translation\Dumper;

use Symfony\Component\HttpKernel\Util\Filesystem;
use Symfony\Component\Translation\MessageCatalogue as SymfonyCatalogue;
use Symfony\Component\Translation\Dumper\DumperInterface as SymfonyDumper;

use JMS\TranslationBundle\Model\MessageDomain;

/**
 * Adapter for Symfony's dumpers.
 *
 * For these dumpers, the same restrictions apply. Namely, using them will
 * cause valuable information to be lost.
 *
 * Also note that only file-based dumpers are compatible.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class SymfonyDumperAdapter implements DumperInterface
{
    private $dumper;
    private $format;

    public function __construct(SymfonyDumper $dumper, $format)
    {
        $this->dumper = $dumper;
        $this->format = $format;
    }

    public function dump(MessageDomain $domain)
    {
        $symfonyCatalogue = new SymfonyCatalogue($domain->getLocale());

        foreach ($domain->all() as $id => $message) {
            $symfonyCatalogue->add(
                array($id => $message->getLocaleString()),
                $domain->getName()
            );
        }

        $tmpPath = sys_get_temp_dir().'/'.uniqid('translation', false);
        if (!is_dir($tmpPath) && false === @mkdir($tmpPath, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create temporary directory "%s".', $tmpPath));
        }

        $this->dumper->dump($symfonyCatalogue, array(
            'path' => $tmpPath,
        ));

        if (!is_file($tmpFile = $tmpPath.'/'.$domain->getName().'.'.$domain->getLocale().'.'.$this->format)) {
            throw new \RuntimeException(sprintf('Could not find dumped translation file "%s".', $tmpFile));
        }

        $contents = file_get_contents($tmpFile);
        $fs = new Filesystem();
        $fs->remove($tmpPath);

        if ('' === $contents) {
            throw new \RuntimeException(sprintf('Could not dump message catalogue using dumper "%s". It could be that it is not compatible.', get_class($this->dumper)));
        }

        return $contents;
    }
}