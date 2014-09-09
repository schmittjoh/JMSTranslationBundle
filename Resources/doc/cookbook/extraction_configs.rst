Saving Common Extraction Settings
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Once you have found a suitable combination of command line options, it might be a bit tedious
to specify them each time when you want to run the extraction command. For this, you can
also set-up some pre-defined settings via the configuration:

.. code-block :: yml

    # config.yml
    jms_translation:
        configs:
            app:
                dirs: [%kernel.root_dir%, %kernel.root_dir%/../src]
                output_dir: %kernel.root_dir%/Resources/translations
                ignored_domains: [routes]
                excluded_names: ["*TestCase.php", "*Test.php"]
                excluded_dirs: [cache, data, logs]
                extractors: [alias_of_the_extractor]

You can then run the extraction process with this configuration with the following command:

.. note ::
    Since Symfony 2.5.4, quotes around *TestCase.php and *Test.php are
    necessary. Without them it the Yaml cannot be parsed.

.. code-block :: bash

    php app/console translation:extract de --config=app
    
The ``--config`` option also supports overriding via command-line options. Let's assume that
you would like to change the output format that has been defined in the config, but leave all
other settings the same, you would run:

.. code-block :: bash

    php app/console translation:extract de --config=app --output-format=xliff
