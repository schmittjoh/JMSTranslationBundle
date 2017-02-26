Configuration reference
~~~~~~~~~~~~~~~~~~~~~~~
On this page you will find all available configuration options and their meaning.

.. code-block :: yml

    # config.yml
    jms_translation:
        locales: ['en', 'fr', 'sv'] # List of locales supported by your application
        source_language: 'en' # The language that the sources is written in

        # You may have different configurations under this key
        configs:
            # Create a configuration named "app"
            app:
                # List of directories we should extract translations keys from
                dirs: ["%kernel.root_dir%", "%kernel.root_dir%/../src"]

                # Where to write the translation files
                output_dir: "%kernel.root_dir%/Resources/translations"

                # Whitelist domains
                domains: ["messages"]

                # Blacklist domains
                ignored_domains: ["routes"]

                # What files to exclude
                excluded_names: ["*TestCase.php", "*Test.php"]

                # What directories to exclude
                excluded_dirs: [cache, data, logs]

                # List of extractors to use. Defaults to ???
                extractors: [alias_of_the_extractor]

                # Load translation files from external directory
                external_translations_dirs: ~

                # If empty it uses the format of existing files
                # Possible values ["php", "xliff", "yaml", "xlf"]
                output_format: ~

                # The default output format (defaults to xlf)
                default_output_format: "xlf"

                # If true, we will never remove messages from the translation files.
                # If false, the translation files are up to date with the source.
                keep: false