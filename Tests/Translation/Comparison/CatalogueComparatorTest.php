<?php

namespace JMS\TranslationBundle\Tests\Translation\Comparison;

use JMS\TranslationBundle\Translation\Comparison\CatalogueComparator;
use JMS\TranslationBundle\Translation\Comparison\ChangeSet;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogue as SymfonyMessageCatalogue;

class CatalogueComparatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCompareWithMultipleDomains()
    {
        $current = new SymfonyMessageCatalogue('en');
        $current->add(array('foo' => 'bar'));
        $current->add(array('bar' => 'baz'), 'routes');

        $new = new MessageCatalogue();
        $new->add(new Message('foo'));
        $new->add(new Message('bar'));

        $expected = new ChangeSet(array(), array());
        $comparator = new CatalogueComparator();

        $this->assertEquals($expected, $comparator->compare($current, $new));
    }
}