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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File\Fixture;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class MyFormTypeWithSubscriberAndListener extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $formFactory = $builder->getFormFactory();
        $builder
            ->add('firstname', 'text', array(
                'label' => 'form.label.firstname',
            ))
            ->add('lastname', 'text', array(
                'label' => /** @Desc("Lastname") */ 'form.label.lastname',
            ))

            ->addEventSubscriber(new MyFormSubscriber($formFactory))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formFactory) {
                $data = $event->getData();
                $form = $event->getForm();

                if (null === $data) {
                    return;
                }

                $form
                    ->add($formFactory->createNamed('zip', 'text', null, array(
                        /** @Desc("ZIP") */
                        'label' => 'form.label.zip',
                        'translation_domain' => 'address'
                    )))
                ;
            })
        ;
    }
    
    public function getName()
    {
        return 'my_form_with_subscriber_and_listener';
    }
}
