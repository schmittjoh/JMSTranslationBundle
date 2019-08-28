# Changelog
### 1.4.2 to 1.4.3
* Added support for SF4
* Fixed jms.js error

### 1.3.2 to 1.4.2
* Remove dependencies on JMSDiExtraBundle and JMSAopBundle. If you do not use these bundles elsewhere in your application you will need to remove the reference to them in `registerBundles` of your `AppKernel` 
* Added support for nikic/parser v4

### 1.3.1 to 1.3.2
* Added configuration options to disable date/sources in xliff dump
* Fixed trim bug with @XBundle notation
* Added support for php 7.1
* Added Twig 2 support

### 1.3.0 to 1.3.1

* Fixed new messages not showing at the top in WebUI when in XLIFF format.
* Fixed relative path calculation when file path is not expressed in the form of `kernel.root_dir."/../whatever"`

### 1.2.3 to 1.3.0

* XliffMessage is improved with note elements and attributes. 
* Keeping new lines inside translations
* Fix global namespace extraction.
* Better documentation
* Clearer Exception messages
* The `DefaultPhpFileExtractor` could be extended an modified which function we should extraction messages from.
* Make sure Message ID is always a string to avoid issues with numerial IDs. 
* Make source files clickable in WebUI
* Message sources in Xliff files will be sorted in alphabetical order 

### 1.2.2 to 1.2.3

* Support for placeholders and empty_value.
* Slightly easier WebUI extension with new extensible js class
* Extracted strings have the full source path stored
* Improvements to WebUI logic
* Various extraction bug fixes (attr as variable)
* Various improvements to xliff dumper
* Ensure full Symfony 3.0 support and tests

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
