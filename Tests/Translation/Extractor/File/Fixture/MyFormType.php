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

class MyFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array(
                'label' => 'form.label.firstname',
            ))
            ->add('lastname', 'text', array(
                'label' => /** @Desc("Lastname") */ 'form.label.lastname',
            ))
            ->add('states', 'choice', array(
                'choices' => array('foo' => 'bar'),
                'empty_value' => /** @Desc("Please select a state") */ 'form.states.empty_value',
            ))
            ->add('countries', 'choice', array('empty_value' => false))
            ->add('password', 'repeated', array(
                'first_options' => array(
                  'label' => 'form.label.password'
                ),
                'second_options' => array(
                  'label' => /** @Desc("Repeat password") */ 'form.label.password_repeated'
                ),
                'invalid_message' => /** @Desc("The entered passwords do not match") */ 'form.error.password_mismatch'
            ))
            ->add('street', 'text', array(
                'label' => /** @Desc("Street") */ 'form.label.street',
                'translation_domain' => 'address'
            ))
            ->add('zip', 'text', array(
                /** @Desc("ZIP") */
                'label' => 'form.label.zip',
                'translation_domain' => 'address'
            ))
            ->add('field_with_placeholder', 'text', array(
                'label' => 'field.with.placeholder',
                'attr' => array('placeholder' => /** @Desc("Field with a placeholder value") */ 'form.placeholder.text')
            ))
            ->add('field_without_label', 'text', array(
                'label' => false,
                'attr' => array('placeholder' => /** @Desc("Field with a placeholder but no label") */ 'form.placeholder.text.but.no.label')
            ))
            ->add('field_with_choice_as_values', 'choice', array(
                'choices' => array(
                    'form.choice.choice_as_values.label.foo' => 'form.choice.choice_as_values.value.foo',
                    'form.choice.choice_as_values.label.bar' => 'form.choice.choice_as_values.value.bar'
                ),
                'choices_as_values' => true,
            ))
        ;
        $child = $builder->create('created', 'text', array(
                  'label' => 'form.label.created'
              ))
        ;
        $builder->add('dueDate', 'date', array(
                'empty_value' => array('year' => 'form.dueDate.empty.year', 'month' => 'form.dueDate.empty.month', 'day'=>'form.dueDate.empty.day')
        ));
    }
}
