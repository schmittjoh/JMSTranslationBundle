# Changelog


### 1.2.2 to next release

* Support for placeholders and empty_value.
* Slightly easier webui extension with new extensible js class
* Extracted strings have the full source path stored
* Improvements to WebUI logic
* Various extraction bug fixes (attr as variable)
* Various improvements to xliff dumper
* Ensure full symfony 3.0 support and tests

### 1.2.1 to 1.2.2

* Support nikic/php-parser 1.4.x and 2.0.x
* Set XLF as default output format of the ExtractTranslationCommand
* Better compatibility with Symfony 3.0
* Bugfixes with calls to the logger
* Added more tests for controllers, extension, compiler passes and config
* Code and doc cleanup 

### 1.2.0 to 1.2.1

* New maintainers: @gnat42, @gouaille and @nyholm.
* Updated Jquery from 1.6.1 to 1.12.0.
* Dropping support for Symfony 2.1, 2.2, 2.4, 2.5 and 2.6.
* Removed the requirement on Twig text extension.
* Removed use of the deprecated `Symfony\Component\HttpKernel\Log\LoggerInterface` in favor for `Psr\Log\LoggerInterface`. 

### 1.1 to 1.2

* Lots of minor changes and improvements from June 2013.
