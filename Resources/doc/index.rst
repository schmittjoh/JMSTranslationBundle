JMSTranslationBundle
====================

Introduction
------------

This bundle puts the Symfony Translation Component on steroids. While the 
Translation component is highly optimized to reduce the runtime overhead of
your code, it lacks a few features for translators. The aim of this bundle
is to make translating a site easier while still retaining all of the 
performance optimizations that are currently in place.

Key Features include:

- allows developers to add additional context to translation ids to aid
  translators in finding the best possible translation
- optimized dumping commands (nicer formatting, more information for
  translators, marks new messages)
- optimized search algorithm (messages are found faster, and more reliably)
- can extract messages for bundles, and your application (bundles)
- extraction configs can be set-up through configuration to avoid having 
  to re-type many command line arguments/options
- Web-based UI for easier translation of messages

Components:

This bundle has three major components:

- **Extractor** - extracts translation keys from your source code
- **Dumper** - writes translations to file
- **WebUI** - lets you edit your translations in a user friendly interface


Documentation
-------------

.. toctree ::
    :hidden:
    
    installation
    usage
    webinterface
    /cookbook/extraction_configs

- :doc:`Installation <installation>`
- :doc:`Usage <usage>`
- :doc:`Webinterface for Translators <webinterface>`
- Recipies
  - :doc:`Saving Common Extracting Settings </cookbook/extraction_configs>`

License
-------

The code is released under the business-friendly `Apache2 license`_. 

Documentation is subject to the `Attribution-NonCommercial-NoDerivs 3.0 Unported
license`_.

.. _Apache2 license: http://www.apache.org/licenses/LICENSE-2.0.html
.. _Attribution-NonCommercial-NoDerivs 3.0 Unported license: http://creativecommons.org/licenses/by-nc-nd/3.0/

