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
use JMS\TranslationBundle\Annotation\Domain;
use JMS\TranslationBundle\Annotation\Domains;

class MyFormTypeWithDomainAnnotation extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('field_with_domain_label', 'text', array(
                'label' => /** @Domain("messages_domain") */
                    'form.label.field_with_domain_label'
            ))
            ->add('field_with_domains_label', 'text', array(
                'label' => /** @Domains({"messages_domains_one", "messages_domains_two"}) */
                    'form.label.field_with_domains_label'
            ));
    }
}
