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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File\Fixture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', ['label' => 'form.label.firstname'])
            ->add('lastname', 'text', ['label' => /** @Desc("Lastname") */ 'form.label.lastname'])
            ->add('states', 'choice', [
                'choices' => ['foo' => 'bar'],
                'empty_value' => /** @Desc("Please select a state") */ 'form.states.empty_value',
            ])
            ->add('countries', 'choice', ['empty_value' => false])
            ->add('password', 'repeated', [
                'first_options' => ['label' => 'form.label.password'],
                'second_options' => ['label' => /** @Desc("Repeat password") */ 'form.label.password_repeated'],
                'invalid_message' => /** @Desc("The entered passwords do not match") */ 'form.error.password_mismatch',
            ])
            ->add('street', 'text', [
                'label' => /** @Desc("Street") */ 'form.label.street',
                'translation_domain' => 'address',
                'constraints' => [
                    new NotBlank(['message' => /** @Desc("You should fill in the street") */ 'form.street.empty_value']),
                    new Length(['max' => 100]), // https://github.com/schmittjoh/JMSTranslationBundle/issues/553
                ],
            ])
            ->add('zip', 'text', [
                /** @Desc("ZIP") */
                'label' => 'form.label.zip',
                'translation_domain' => 'address',
            ])
            ->add('field_with_placeholder', 'text', [
                'label' => 'field.with.placeholder',
                'attr' => ['placeholder' => /** @Desc("Field with a placeholder value") */ 'form.placeholder.text'],
            ])
            ->add('field_without_label', 'text', [
                'label' => false,
                'attr' => ['placeholder' => /** @Desc("Field with a placeholder but no label") */ 'form.placeholder.text.but.no.label'],
            ]);
        $child = $builder->create('created', 'text', ['label' => 'form.label.created']);
        $builder->add('dueDate', 'date', [
            'empty_value' => ['year' => 'form.dueDate.empty.year', 'month' => 'form.dueDate.empty.month', 'day' => 'form.dueDate.empty.day'],
        ]);
    }

    public const CHOICES = ['choices' => [null]];
}
