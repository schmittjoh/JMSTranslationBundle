Usage
-----

Creating Translation Messages
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
While not strictly necessary, this bundle strongly advocates the usage of
abstract keys such as "form.label.firstname" as translation messages. Many of 
the features of this bundle were designed to facilitate this.

Abstract keys are used for two main reasons:

#. Translation messages are mostly written by developers, and thus their
   first draft of the message might not be perfect from a copywriters point
   of view, or changes might be necessitated later for other reasons. These
   changes would then result in changes for all supported languages instead 
   of only for the source language, and some translations might actually be
   lost in the process.

#. Some words in English (or whatever your source language is) are spelled 
   differently in other languages depending on their meaning. Let's take the 
   English word "Archive" as an example. This can be a noun ("The Archive"), 
   and also a verb ("to archive"). In German, these are two different words
   "Archiv" for the noun, and "Archivieren" for the verb. If you were using
   the source message as id, you could not use the word "Archiv" with different
   meanings on your site as you could only either translate it to the German
   "Archiv", or "Archivieren", but not both.

Whereas abstract keys do not suffer from these limitations, they come with some
of their own. For example, sometimes it is hard for the translator to know what 
s/he is supposed to translate. Let's take a look at the following example where 
we use the source message as key:

.. code-block :: jinja

    {# index.html.twig #}
    {{ "{0} There is no apples|{1} There is one apple|]1,Inf] There are %count% apples"|transchoice(count) }}

If we translate this to use an abstract key instead, we would get something like 
the following:

.. code-block :: jinja

    {# index.html.twig #}
    {{ "text.apples_remaining"|transchoice(count) }}

If a translator now sees this abstract key, s/he does not really know what the
expected translation should look like. Fortunately, there is a solution for 
this. We simply allow the developer to convey more context to the translator 
via the ``desc`` filter:

.. code-block :: jinja

    {# index.html.twig #}
    {{ "text.apples_remaining"|transchoice(count)
           |desc("{0} There is no apples|{1} There is one apple|]1,Inf] There are %count% apples") }}

As you can see we have basically moved the source translation to the ``desc`` filter.
This filter can contain any information that aids a translator in producing a better
translated message. When extracting messages, this message will also automatically
be used as the default translation.

.. note ::

    The ``desc`` filter is removed when your Twig template is compiled, and does
    not affect the runtime performance of your template.

Of course, an equivalent to the ``desc`` filter is also available for 
translations in PHP code, the ``@Desc`` annotation:

.. code-block :: php

    <?php

    // Controller.php
    /** @Desc("{0} There is no apples|{1} There is one apple|]1,Inf] There are %count% apples") */
    $this->translator->transChoice('text_apples_remaining', $count)

You can place the doc comment anywhere in the method call chain or directly 
before the key.

Extracting Translation Messages
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
This bundle automatically supports extracting messages from the following 
sources:

- Twig: ``trans``, and ``transchoice`` filters as well as ``trans``,
  and ``transchoice`` blocks
- PHP: 

  - all calls to the ``trans``, or ``transChoice`` method
  - all classes implementing the ``TranslationContainerInterface``
  - all form labels that are defined as options to the ->add() method of the FormBuilder
  - messages declared in validation constraints

If you need to customize this process even further, you can implement your own
``FileVisitorInterface`` service, and tag it with ``jms_translation.file_visitor``. As an example,
you can take a look at the JMSGoogleClosureBundle_ which extracts translations from Javascript

While all of the aforementioned methods extract translation messages from the file system,
in some cases, you cannot attribute translation messages to specific files. For these cases,
you can implement an ``ExtractorInterface`` service, and tag it with ``jms_translation.extractor``.

As an example, you can take a look at the JMSI18nRoutingBundle_ which implements an `extractor service`_
for routes, and the corresponding `service definition`_.
Due to the global nature of these extractors, they are not enabled by default, but you need to 
enabled each of them explicitly. You can do that by passing the ``--enable-extractor=fooAlias``
command line option, or enable it in the configuration (see below).

.. _JMSGoogleClosureBundle: https://github.com/schmittjoh/JMSGoogleClosureBundle/blob/master/Translation/GoogleClosureTranslationExtractor.php
.. _JMSI18nRoutingBundle: https://github.com/schmittjoh/JMSI18nRoutingBundle/blob/master/Translation/RouteTranslationExtractor.php
.. _extractor service: https://github.com/schmittjoh/JMSI18nRoutingBundle/blob/master/Translation/RouteTranslationExtractor.php
.. _service definition: https://github.com/schmittjoh/JMSI18nRoutingBundle/blob/master/Resources/config/services.xml#L43

Dumping Translation Messages
~~~~~~~~~~~~~~~~~~~~~~~~~~~~
For dumping, the bundle provides you with a console command which you can use to update
your translation files, or also just to preview all changes that have been made.

Updating Files:

.. code-block :: bash

    php app/console translation:extract de --dir=./src/ --output-dir=./app/Resources/translations

If you would like to preview the changes first, you can simply add the ``--dry-run`` option.

The command provides several command line options which you can use to adapt the extraction
process to your specific needs, just run:

.. code-block :: bash

    php app/console translation:extract --help

One notable option is "--bundle" which lets you easily dump the translation files for one
bundle:

.. code-block :: bash

    php app/console translation:extract de --bundle=MyFooBundle
    
.. tip ::

    This bundle supports the following formats: csv, ini, php, qt, xliff, and yml
    
    Note however, that the best integration exists with the XLIFF format. This is simply 
    due to the fact that the other formats are not so extensible, and do not allow for 
    some of the more advanced features like tracking where a translation is used, whether 
    it is new, etc.

    
    