The Translation Web UI
======================

This bundle contains a small user interface to ease the translation process,
and review the consistency of translations across different locales.

This interface is disabled by default, and it should only be enabled in your
development environment as it will modify your translation files directly.

Installation
------------

This bundle makes use of annotation configuration for routes, you can include
the routes by adding the following to ``app/config/routing_dev.yml`` (note
that you need to have the SensioFrameworkExtraBundle_ installed)::

    JMSTranslationBundle_ui:
        resource: "@JMSTranslationBundle/Controller/"
        type:     annotation
        prefix:   /_trans

Usage
-----
If you have followed the instructions above, you can now access the interface
under the path::

    http://your-host/app_dev.php/_trans/
