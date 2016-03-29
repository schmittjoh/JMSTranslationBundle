Installation
============

To install JMSTranslationBundle with Composer execute the following command:

.. code-block :: bash

    $ composer require jms/translation-bundle "~1.2.1"
    
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
