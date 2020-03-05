<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File\Fixture;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

class MyFormSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data === null) {
            return;
        }

        $form
            ->add($this->factory->createNamed('password', 'repeated', null, [
                'first_options' => ['label' => 'form.label.password'],
                'second_options' => ['label' => /** @Desc("Repeat password") */ 'form.label.password_repeated'],
                'invalid_message' => /** @Desc("The entered passwords do not match") */ 'form.error.password_mismatch',
            ]));
    }
}
