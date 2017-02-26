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

class MyPlaceholderFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('field_with_attr_placeholder', 'text', array(
                'label' => 'field.with.placeholder',
                'attr' => array('placeholder' => /** @Desc("Field with a placeholder value") */ 'form.placeholder.text')
            ))
            ->add('field_without_label_with_attr_placeholder', 'text', array(
                'label' => false,
                'attr' => array('placeholder' => /** @Desc("Field with a placeholder but no label") */ 'form.placeholder.text.but.no.label')
            ))
            ->add('field_placeholder', 'choice', array(
                'placeholder' => /** @Desc("Choice field with a placeholder") */ 'form.choice_placeholder'
            ))
            ->add('field_empty_value', 'choice', array(
                'empty_value' => /** @Desc("Choice field with an empty_value") */ 'form.choice_empty_value'
            ))
        ;
    }
}
