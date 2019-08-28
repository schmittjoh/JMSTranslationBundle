Installation
============

To install JMSTranslationBundle with Composer execute the following command:

.. code-block :: bash

    $ composer require jms/translation-bundle "^1.3"
    
Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\TranslationBundle\JMSTranslationBundle(),
        // ...
    );

Congratulations the bundle is now installed. Lets start configure with the bundle.
These resources may be of interest:

- :doc:`Example configuration <cookbook/extraction_config.rst`
- :doc:`Configuration reference <cookbook/config_reference.rst`
- :doc:`Using the WebUI <webinterface.rst`
