# Changelog

## [1.6.0](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.6.0) (2021-01-16)

- Php8 [\#556](https://github.com/schmittjoh/JMSTranslationBundle/pull/556) ([VincentLanglet](https://github.com/VincentLanglet))
- Use composer v1 \(fix ci pipeline\) [\#555](https://github.com/schmittjoh/JMSTranslationBundle/pull/555) ([goetas](https://github.com/goetas))
- Prevent all non message Form Constraint option from being extracted [\#554](https://github.com/schmittjoh/JMSTranslationBundle/pull/554) ([nfragnet](https://github.com/nfragnet))
- Add the option to dump files to the ICU message format [\#551](https://github.com/schmittjoh/JMSTranslationBundle/pull/551) ([mark-gerarts](https://github.com/mark-gerarts))
- Update FormExtractor.php [\#549](https://github.com/schmittjoh/JMSTranslationBundle/pull/549) ([TheRatG](https://github.com/TheRatG))
- Added support to extract translations from form constraints [\#546](https://github.com/schmittjoh/JMSTranslationBundle/pull/546) ([balazscsaba2006](https://github.com/balazscsaba2006))
- strtolower\(\) expects parameter 1 to be string, object given Issue \#544 [\#545](https://github.com/schmittjoh/JMSTranslationBundle/pull/545) ([TheRatG](https://github.com/TheRatG))

## [1.5.4](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.5.4) (2020-04-21)

**Fixed bugs:**

- test translation state is preserved for xliff [\#540](https://github.com/schmittjoh/JMSTranslationBundle/pull/540) ([goetas](https://github.com/goetas))

## [1.5.3](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.5.3) (2020-04-19)

**Closed issues:**

- ExtractTranslationCommand unlink\(\) expects parameter 1 to be a valid path, object given [\#537](https://github.com/schmittjoh/JMSTranslationBundle/issues/537)
- 1.5 =\> simplexml\_load\_file\(\) expects parameter 1 to be a valid path, object given [\#535](https://github.com/schmittjoh/JMSTranslationBundle/issues/535)

**Merged pull requests:**

- Update Updater.php [\#538](https://github.com/schmittjoh/JMSTranslationBundle/pull/538) ([TheRatG](https://github.com/TheRatG))
- Remove duplicated Changelog [\#534](https://github.com/schmittjoh/JMSTranslationBundle/pull/534) ([franmomu](https://github.com/franmomu))

## [1.5.2](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.5.2) (2020-03-19)

**Merged pull requests:**

- Fix casting int to string [\#536](https://github.com/schmittjoh/JMSTranslationBundle/pull/536) ([franmomu](https://github.com/franmomu))

## [1.5.1](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.5.1) (2020-03-17)

**Merged pull requests:**

- Fixed object instead of string error introduced by strict\_types [\#533](https://github.com/schmittjoh/JMSTranslationBundle/pull/533) ([ViniTou](https://github.com/ViniTou))

## [1.5.0](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.5.0) (2020-03-15)

**Closed issues:**

- New major release [\#527](https://github.com/schmittjoh/JMSTranslationBundle/issues/527)
- Requirement of nikic/php-parser does not match packgist page [\#514](https://github.com/schmittjoh/JMSTranslationBundle/issues/514)
- The format "yaml~" does not exist. [\#512](https://github.com/schmittjoh/JMSTranslationBundle/issues/512)
- ClassLoader deprecation notice [\#511](https://github.com/schmittjoh/JMSTranslationBundle/issues/511)
- Symfony 4 : Unable to find template "JMSTranslationBundle::base.html.twig" [\#503](https://github.com/schmittjoh/JMSTranslationBundle/issues/503)
- Symfony 2.7 should be required in composer.json [\#263](https://github.com/schmittjoh/JMSTranslationBundle/issues/263)

**Merged pull requests:**

- Apply cs [\#532](https://github.com/schmittjoh/JMSTranslationBundle/pull/532) ([franmomu](https://github.com/franmomu))
- Use array access to twig argument nodes [\#531](https://github.com/schmittjoh/JMSTranslationBundle/pull/531) ([franmomu](https://github.com/franmomu))
- Allow Symfony 5 and Twig 3 [\#530](https://github.com/schmittjoh/JMSTranslationBundle/pull/530) ([franmomu](https://github.com/franmomu))
- Remove extending from ContainerAwareCommand [\#529](https://github.com/schmittjoh/JMSTranslationBundle/pull/529) ([franmomu](https://github.com/franmomu))
- Allow Translation Contracts [\#528](https://github.com/schmittjoh/JMSTranslationBundle/pull/528) ([franmomu](https://github.com/franmomu))
- Fix calls passing the right scalar type [\#526](https://github.com/schmittjoh/JMSTranslationBundle/pull/526) ([franmomu](https://github.com/franmomu))
- Add Doctrine Coding Standards in tests [\#525](https://github.com/schmittjoh/JMSTranslationBundle/pull/525) ([franmomu](https://github.com/franmomu))
- Test minimum requirements [\#524](https://github.com/schmittjoh/JMSTranslationBundle/pull/524) ([franmomu](https://github.com/franmomu))
- Cleanup tests [\#523](https://github.com/schmittjoh/JMSTranslationBundle/pull/523) ([franmomu](https://github.com/franmomu))
- Replace kernel.root\_dir with kernel.project\_dir [\#522](https://github.com/schmittjoh/JMSTranslationBundle/pull/522) ([franmomu](https://github.com/franmomu))
- Remove duplicated packages in composer [\#521](https://github.com/schmittjoh/JMSTranslationBundle/pull/521) ([franmomu](https://github.com/franmomu))
- Drop symfony \< 3.4 and \< 4.3 for 4.x [\#520](https://github.com/schmittjoh/JMSTranslationBundle/pull/520) ([franmomu](https://github.com/franmomu))
- Address namespace change in Twig [\#519](https://github.com/schmittjoh/JMSTranslationBundle/pull/519) ([greg0ire](https://github.com/greg0ire))
- Compile nodes before comparing them [\#518](https://github.com/schmittjoh/JMSTranslationBundle/pull/518) ([greg0ire](https://github.com/greg0ire))
- Remove deprecated usage of TreeBuilder on Symfony 4 [\#513](https://github.com/schmittjoh/JMSTranslationBundle/pull/513) ([emodric](https://github.com/emodric))
- Update for Symfony 4.1 [\#504](https://github.com/schmittjoh/JMSTranslationBundle/pull/504) ([tilimac](https://github.com/tilimac))

## [1.4.4](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.4.4) (2019-05-14)

**Closed issues:**

- Invalid type annotation for the return value of getTranslationMessages\(\) [\#508](https://github.com/schmittjoh/JMSTranslationBundle/issues/508)
- php 7.2 method return type throw error  [\#494](https://github.com/schmittjoh/JMSTranslationBundle/issues/494)
-  The annotation "@created" ,  "@optional" ... was never imported.  [\#488](https://github.com/schmittjoh/JMSTranslationBundle/issues/488)
- Unable to find template "@JMSTranslation/translate/index.html.twig" [\#474](https://github.com/schmittjoh/JMSTranslationBundle/issues/474)
- Symfony 4 support? [\#471](https://github.com/schmittjoh/JMSTranslationBundle/issues/471)
- Translatable options for ArrayChoiceList not being translated \(moving from ChoiceList\) [\#469](https://github.com/schmittjoh/JMSTranslationBundle/issues/469)
- symfony 3.4 problem [\#466](https://github.com/schmittjoh/JMSTranslationBundle/issues/466)
- Deleting translation entries : how to prevent certain messages being culled when running translation:extract ? [\#448](https://github.com/schmittjoh/JMSTranslationBundle/issues/448)

**Merged pull requests:**

- Test if filter exists before use [\#510](https://github.com/schmittjoh/JMSTranslationBundle/pull/510) ([gnat42](https://github.com/gnat42))
- Import Message, fixes \#508 [\#509](https://github.com/schmittjoh/JMSTranslationBundle/pull/509) ([arnaud-lb](https://github.com/arnaud-lb))

## [1.4.3](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.4.3) (2018-06-28)

**Merged pull requests:**

- SF4 Compatibility [\#493](https://github.com/schmittjoh/JMSTranslationBundle/pull/493) ([gnat42](https://github.com/gnat42))

## [1.4.2](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.4.2) (2018-06-27)

**Closed issues:**

- PHP-Parser v4.0 compatibility [\#476](https://github.com/schmittjoh/JMSTranslationBundle/issues/476)
- Possibility to remove the jms:reference-file from the dumped .xlf [\#409](https://github.com/schmittjoh/JMSTranslationBundle/issues/409)

**Merged pull requests:**

- fix memory limit issues [\#491](https://github.com/schmittjoh/JMSTranslationBundle/pull/491) ([gnat42](https://github.com/gnat42))
- Fix nikic/php-parser v4 compatibility [\#490](https://github.com/schmittjoh/JMSTranslationBundle/pull/490) ([soullivaneuh](https://github.com/soullivaneuh))
- Allow nikic/php-parser v4 [\#477](https://github.com/schmittjoh/JMSTranslationBundle/pull/477) ([soullivaneuh](https://github.com/soullivaneuh))

## [1.4.1](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.4.1) (2018-02-09)

**Closed issues:**

- The last release is not on packagist [\#480](https://github.com/schmittjoh/JMSTranslationBundle/issues/480)
- The format "yml" does not exist [\#468](https://github.com/schmittjoh/JMSTranslationBundle/issues/468)

## [1.4.0](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.4.0) (2018-02-07)

**Closed issues:**

- Get rid of JMSDI [\#457](https://github.com/schmittjoh/JMSTranslationBundle/issues/457)
- JMS translation doesn't extract dynamic form build? [\#444](https://github.com/schmittjoh/JMSTranslationBundle/issues/444)
- Improve management of xlf files [\#372](https://github.com/schmittjoh/JMSTranslationBundle/issues/372)
- Remove dependency on DiExtraBundle [\#355](https://github.com/schmittjoh/JMSTranslationBundle/issues/355)

**Merged pull requests:**

- Manage legacy aliases if exist [\#479](https://github.com/schmittjoh/JMSTranslationBundle/pull/479) ([soullivaneuh](https://github.com/soullivaneuh))
- Add missing Symfony validator dependency [\#470](https://github.com/schmittjoh/JMSTranslationBundle/pull/470) ([bocharsky-bw](https://github.com/bocharsky-bw))
- Remove dependency on JMSDiExtraBundle [\#464](https://github.com/schmittjoh/JMSTranslationBundle/pull/464) ([tommygnr](https://github.com/tommygnr))
- Avoid method call on null in FormExtractor [\#463](https://github.com/schmittjoh/JMSTranslationBundle/pull/463) ([dontub](https://github.com/dontub))
- Allow user to filter messages in the web-ui [\#460](https://github.com/schmittjoh/JMSTranslationBundle/pull/460) ([azine](https://github.com/azine))
- use precise for php5.3 [\#456](https://github.com/schmittjoh/JMSTranslationBundle/pull/456) ([gnat42](https://github.com/gnat42))
- Fixed return typehint. [\#455](https://github.com/schmittjoh/JMSTranslationBundle/pull/455) ([racinmat](https://github.com/racinmat))
- Update version of the bundle [\#454](https://github.com/schmittjoh/JMSTranslationBundle/pull/454) ([FabienSalles](https://github.com/FabienSalles))
- Fixes translation\_domain detection with setDefault [\#453](https://github.com/schmittjoh/JMSTranslationBundle/pull/453) ([simitter](https://github.com/simitter))
- Fixed twig node visitor signature [\#449](https://github.com/schmittjoh/JMSTranslationBundle/pull/449) ([deguif](https://github.com/deguif))
- Fix PHP Notice: "Array to string conversion" [\#445](https://github.com/schmittjoh/JMSTranslationBundle/pull/445) ([snpy](https://github.com/snpy))
- Use \Twig\_BaseNodeVisitor instead of duplicated visitors code [\#441](https://github.com/schmittjoh/JMSTranslationBundle/pull/441) ([emodric](https://github.com/emodric))

## [1.3.2](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.3.2) (2017-04-20)

**Closed issues:**

- PHPUnit Errors on a clean forked clone of master HEAD [\#436](https://github.com/schmittjoh/JMSTranslationBundle/issues/436)
- Incompatibility between bundle 1.3.1 and Symfony 3.2.3 [\#429](https://github.com/schmittjoh/JMSTranslationBundle/issues/429)
- PHP error with Twig 2.0 [\#425](https://github.com/schmittjoh/JMSTranslationBundle/issues/425)
- Call to a member function getNames\(\) on null | TranslateController.php:64 [\#417](https://github.com/schmittjoh/JMSTranslationBundle/issues/417)
- abstract keys are lost when desc exists [\#414](https://github.com/schmittjoh/JMSTranslationBundle/issues/414)
- LogicException  Node "domain" does not exist for Node "Symfony\Bridge\Twig\Node\TransNode". [\#402](https://github.com/schmittjoh/JMSTranslationBundle/issues/402)
- Translation of variable placeholders [\#401](https://github.com/schmittjoh/JMSTranslationBundle/issues/401)
-  ReferenceError: event is not defined with firebug. [\#397](https://github.com/schmittjoh/JMSTranslationBundle/issues/397)
- The Twig\_Node\_Expression\_ExtensionReference class is deprecated [\#387](https://github.com/schmittjoh/JMSTranslationBundle/issues/387)

**Merged pull requests:**

- support nikic/php-parser 3.0 / php7.1 [\#440](https://github.com/schmittjoh/JMSTranslationBundle/pull/440) ([gnat42](https://github.com/gnat42))
- Twig 2 compatibility [\#439](https://github.com/schmittjoh/JMSTranslationBundle/pull/439) ([emodric](https://github.com/emodric))
- Add configuration options to disable date/sources in xliff dump [\#421](https://github.com/schmittjoh/JMSTranslationBundle/pull/421) ([artursvonda](https://github.com/artursvonda))
- Fix wrong error message [\#413](https://github.com/schmittjoh/JMSTranslationBundle/pull/413) ([mahmouds](https://github.com/mahmouds))
- Fix compatibility with recent Symfony versions [\#405](https://github.com/schmittjoh/JMSTranslationBundle/pull/405) ([stof](https://github.com/stof))
- Fixed wrong offset [\#400](https://github.com/schmittjoh/JMSTranslationBundle/pull/400) ([deguif](https://github.com/deguif))

## [1.3.1](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.3.1) (2016-08-13)

**Closed issues:**

- New translations  [\#392](https://github.com/schmittjoh/JMSTranslationBundle/issues/392)

**Merged pull requests:**

- Prepare for 1.3.1 [\#396](https://github.com/schmittjoh/JMSTranslationBundle/pull/396) ([Nyholm](https://github.com/Nyholm))
- Fix XLIFF messages status not being set appropiately [\#393](https://github.com/schmittjoh/JMSTranslationBundle/pull/393) ([snamor](https://github.com/snamor))
- Fixed relative path calculation [\#391](https://github.com/schmittjoh/JMSTranslationBundle/pull/391) ([ABM-Dan](https://github.com/ABM-Dan))

## [1.3.0](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.3.0) (2016-08-07)

**Fixed bugs:**

- Full paths instead of relative paths since upgrade to 1.2.3 [\#366](https://github.com/schmittjoh/JMSTranslationBundle/issues/366)

**Closed issues:**

- The format "php~" does not exist. [\#375](https://github.com/schmittjoh/JMSTranslationBundle/issues/375)
- Custom form with constraint [\#373](https://github.com/schmittjoh/JMSTranslationBundle/issues/373)
- Add contribution guide [\#361](https://github.com/schmittjoh/JMSTranslationBundle/issues/361)
- Extract error with optipng & jpegoptim filters [\#346](https://github.com/schmittjoh/JMSTranslationBundle/issues/346)
- Translation extract error [\#340](https://github.com/schmittjoh/JMSTranslationBundle/issues/340)
- The license of the bundle.  [\#316](https://github.com/schmittjoh/JMSTranslationBundle/issues/316)
- Roadmap for version 1.3.0 [\#306](https://github.com/schmittjoh/JMSTranslationBundle/issues/306)
- Extracting a concatenated string [\#296](https://github.com/schmittjoh/JMSTranslationBundle/issues/296)
- How to use a different context for form labels translation [\#288](https://github.com/schmittjoh/JMSTranslationBundle/issues/288)
- Not working on symfony 3 [\#287](https://github.com/schmittjoh/JMSTranslationBundle/issues/287)
- Cant launch Web UI [\#272](https://github.com/schmittjoh/JMSTranslationBundle/issues/272)
- It is ignoring existing messages while dumping translation [\#266](https://github.com/schmittjoh/JMSTranslationBundle/issues/266)
- output\_dir should accept @BundlePaths [\#264](https://github.com/schmittjoh/JMSTranslationBundle/issues/264)
- File paths next to the translation line. [\#257](https://github.com/schmittjoh/JMSTranslationBundle/issues/257)
- You can only merge messages with the same id.  [\#249](https://github.com/schmittjoh/JMSTranslationBundle/issues/249)
- The format "yml~" does not exist. [\#245](https://github.com/schmittjoh/JMSTranslationBundle/issues/245)
- php parser dependency [\#244](https://github.com/schmittjoh/JMSTranslationBundle/issues/244)
- The annotation "@brief" in class Converter\Converter was never imported. [\#243](https://github.com/schmittjoh/JMSTranslationBundle/issues/243)
- JMS Translation Bundle does not extract keys from controllers in Symfony2 - getTranslationMessages\(\) [\#239](https://github.com/schmittjoh/JMSTranslationBundle/issues/239)
- Support for /\* @Ignore \*/ [\#234](https://github.com/schmittjoh/JMSTranslationBundle/issues/234)
- The extension with alias "jms\_translation" does not have its getConfiguration\(\) method setup  [\#232](https://github.com/schmittjoh/JMSTranslationBundle/issues/232)
- Change location of translation files [\#231](https://github.com/schmittjoh/JMSTranslationBundle/issues/231)
- xliff dumper error [\#229](https://github.com/schmittjoh/JMSTranslationBundle/issues/229)
- Extractor misunderstands my "label" key [\#227](https://github.com/schmittjoh/JMSTranslationBundle/issues/227)
- "Translations in this page" debug toolbar helper [\#226](https://github.com/schmittjoh/JMSTranslationBundle/issues/226)
- error on extract [\#224](https://github.com/schmittjoh/JMSTranslationBundle/issues/224)
- Invalid output [\#218](https://github.com/schmittjoh/JMSTranslationBundle/issues/218)
- Messages inside propel validators aren't extracted [\#216](https://github.com/schmittjoh/JMSTranslationBundle/issues/216)
- usage of Twig\_Extension\_StringLoader [\#211](https://github.com/schmittjoh/JMSTranslationBundle/issues/211)
- Generate empty translations [\#208](https://github.com/schmittjoh/JMSTranslationBundle/issues/208)
- Errors when using the global namespace [\#201](https://github.com/schmittjoh/JMSTranslationBundle/issues/201)
- Customize jmstranslation web ui [\#200](https://github.com/schmittjoh/JMSTranslationBundle/issues/200)
- Indentation mess in ultilines translations using PHP dumper  [\#192](https://github.com/schmittjoh/JMSTranslationBundle/issues/192)
- Twig parser ignore {% for ... else ... endfor %} syntax [\#189](https://github.com/schmittjoh/JMSTranslationBundle/issues/189)
- How to only update routes.\*.yml files ? [\#167](https://github.com/schmittjoh/JMSTranslationBundle/issues/167)
- phpunit annotations like @covers crashes the extractor [\#157](https://github.com/schmittjoh/JMSTranslationBundle/issues/157)
- Keep XLIFF attributes [\#147](https://github.com/schmittjoh/JMSTranslationBundle/issues/147)
- cant create routes in other langs, keep giving me erros [\#142](https://github.com/schmittjoh/JMSTranslationBundle/issues/142)
- Exception at Translation Extract with bundle [\#141](https://github.com/schmittjoh/JMSTranslationBundle/issues/141)
- PrettyPrint: disable in config.yml [\#136](https://github.com/schmittjoh/JMSTranslationBundle/issues/136)
- Preserve the approve status as set by QT Linguist [\#115](https://github.com/schmittjoh/JMSTranslationBundle/issues/115)
- Throws an exception for grouped choice items in form class [\#88](https://github.com/schmittjoh/JMSTranslationBundle/issues/88)
- Display in UI full path [\#87](https://github.com/schmittjoh/JMSTranslationBundle/issues/87)
- Incorrect translation when multiple @Desc for the same key on the same file [\#86](https://github.com/schmittjoh/JMSTranslationBundle/issues/86)
- Translator chokes on numerical IDs [\#83](https://github.com/schmittjoh/JMSTranslationBundle/issues/83)
- Definition of source-language [\#78](https://github.com/schmittjoh/JMSTranslationBundle/issues/78)
- \[RFC\] Open referenced sources from web UI [\#110](https://github.com/schmittjoh/JMSTranslationBundle/issues/110)

**Merged pull requests:**

- Keep new lines inside translations [\#383](https://github.com/schmittjoh/JMSTranslationBundle/pull/383) ([Nyholm](https://github.com/Nyholm))
- Display available format list for invalid format exception [\#371](https://github.com/schmittjoh/JMSTranslationBundle/pull/371) ([thomasbeaujean](https://github.com/thomasbeaujean))
- typo [\#365](https://github.com/schmittjoh/JMSTranslationBundle/pull/365) ([Nyholm](https://github.com/Nyholm))
- Wrong assets path jms.js [\#363](https://github.com/schmittjoh/JMSTranslationBundle/pull/363) ([jacomensink](https://github.com/jacomensink))
- Added a contribution guide [\#362](https://github.com/schmittjoh/JMSTranslationBundle/pull/362) ([Nyholm](https://github.com/Nyholm))
- Improved documentation [\#360](https://github.com/schmittjoh/JMSTranslationBundle/pull/360) ([Nyholm](https://github.com/Nyholm))
- Suggest a new PR template [\#359](https://github.com/schmittjoh/JMSTranslationBundle/pull/359) ([Nyholm](https://github.com/Nyholm))
- Xliff attributes rebased [\#356](https://github.com/schmittjoh/JMSTranslationBundle/pull/356) ([Nyholm](https://github.com/Nyholm))
- Fix extraction for global namespace [\#354](https://github.com/schmittjoh/JMSTranslationBundle/pull/354) ([cmfcmf](https://github.com/cmfcmf))
- Allow to extend the DefaultPhpFileExtractor watched methods [\#187](https://github.com/schmittjoh/JMSTranslationBundle/pull/187) ([damienalexandre](https://github.com/damienalexandre))
- Release [\#388](https://github.com/schmittjoh/JMSTranslationBundle/pull/388) ([Nyholm](https://github.com/Nyholm))
- Making source file clickable in WebUI [\#385](https://github.com/schmittjoh/JMSTranslationBundle/pull/385) ([Nyholm](https://github.com/Nyholm))
- Make sure Message id is always a string [\#384](https://github.com/schmittjoh/JMSTranslationBundle/pull/384) ([Nyholm](https://github.com/Nyholm))
- Relative path [\#382](https://github.com/schmittjoh/JMSTranslationBundle/pull/382) ([Nyholm](https://github.com/Nyholm))
- Sort the sources to use alpha order in the xliff file. [\#380](https://github.com/schmittjoh/JMSTranslationBundle/pull/380) ([thomasbeaujean](https://github.com/thomasbeaujean))

## [1.2.3](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.2.3) (2016-05-08)

**Fixed bugs:**

- Handle attr as a variable [\#348](https://github.com/schmittjoh/JMSTranslationBundle/pull/348) ([gnat42](https://github.com/gnat42))

**Closed issues:**

- Translation Web UI - Fatal error [\#341](https://github.com/schmittjoh/JMSTranslationBundle/issues/341)
- Form extractor crashes when label is false [\#126](https://github.com/schmittjoh/JMSTranslationBundle/issues/126)
- List also available placeholders [\#113](https://github.com/schmittjoh/JMSTranslationBundle/issues/113)
- Duplicated extraction on Inherited Validation Constraints of different bundles classes. [\#79](https://github.com/schmittjoh/JMSTranslationBundle/issues/79)

**Merged pull requests:**

- Updated changelog prior to release [\#357](https://github.com/schmittjoh/JMSTranslationBundle/pull/357) ([gnat42](https://github.com/gnat42))
- Move inline JS to a class [\#352](https://github.com/schmittjoh/JMSTranslationBundle/pull/352) ([mwoynarski](https://github.com/mwoynarski))
- fix tests broken by merges [\#351](https://github.com/schmittjoh/JMSTranslationBundle/pull/351) ([gnat42](https://github.com/gnat42))
- Do not allow to fail on SF3 [\#350](https://github.com/schmittjoh/JMSTranslationBundle/pull/350) ([Nyholm](https://github.com/Nyholm))
- Dev nyholm placeholder [\#349](https://github.com/schmittjoh/JMSTranslationBundle/pull/349) ([Nyholm](https://github.com/Nyholm))
- Check response content when update message [\#347](https://github.com/schmittjoh/JMSTranslationBundle/pull/347) ([AntoineLemaire](https://github.com/AntoineLemaire))
- Feature refactor enter node [\#342](https://github.com/schmittjoh/JMSTranslationBundle/pull/342) ([gnat42](https://github.com/gnat42))
- Do not truncate source file path [\#331](https://github.com/schmittjoh/JMSTranslationBundle/pull/331) ([sustmi](https://github.com/sustmi))
- Set the reference and the reference position as optionnal [\#330](https://github.com/schmittjoh/JMSTranslationBundle/pull/330) ([thomasbeaujean](https://github.com/thomasbeaujean))
- Set Loggers on NodeVisitors at construct time [\#267](https://github.com/schmittjoh/JMSTranslationBundle/pull/267) ([bburnichon](https://github.com/bburnichon))

## [1.2.2](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.2.2) (2016-04-03)

**Closed issues:**

- Extracting choices not keys from forms [\#332](https://github.com/schmittjoh/JMSTranslationBundle/issues/332)
- Run the test suite [\#329](https://github.com/schmittjoh/JMSTranslationBundle/issues/329)
- Remove branch 'dataCollector' [\#323](https://github.com/schmittjoh/JMSTranslationBundle/issues/323)
- Add test case for bundle config and service definition [\#318](https://github.com/schmittjoh/JMSTranslationBundle/issues/318)
- Update current translation files [\#298](https://github.com/schmittjoh/JMSTranslationBundle/issues/298)
- jms xliff loader not working in Symfony 3.0 [\#281](https://github.com/schmittjoh/JMSTranslationBundle/issues/281)
- Bundle does not work in Symfony 3. [\#279](https://github.com/schmittjoh/JMSTranslationBundle/issues/279)
- Fix Deprecations for Symfony 2.8 preparing for Symfony 3.0 [\#278](https://github.com/schmittjoh/JMSTranslationBundle/issues/278)
- Add a way to extract custom Validation messageProperties [\#159](https://github.com/schmittjoh/JMSTranslationBundle/issues/159)
- Impossible to update the bundle via composer. [\#138](https://github.com/schmittjoh/JMSTranslationBundle/issues/138)
- getClassMetadata\(\) is deprecated since symfony 2.2 [\#129](https://github.com/schmittjoh/JMSTranslationBundle/issues/129)
- Cannot redeclare class Doctrine\ORM\Mapping\Annotation on extraction [\#124](https://github.com/schmittjoh/JMSTranslationBundle/issues/124)
- OutputLogger not conform to new LoggerInterface \(PsrLogger\) [\#95](https://github.com/schmittjoh/JMSTranslationBundle/issues/95)
- One case where @Desc before doesn't work [\#77](https://github.com/schmittjoh/JMSTranslationBundle/issues/77)
- Change definitions for XLIFF loader and dumper to accomodate XLF [\#29](https://github.com/schmittjoh/JMSTranslationBundle/issues/29)

**Merged pull requests:**

- Updated changelog prior release. [\#337](https://github.com/schmittjoh/JMSTranslationBundle/pull/337) ([Nyholm](https://github.com/Nyholm))
- Set xlf as default file format [\#335](https://github.com/schmittjoh/JMSTranslationBundle/pull/335) ([wimvds](https://github.com/wimvds))
- Solving translation of choices label using SF \> 2.7 [\#334](https://github.com/schmittjoh/JMSTranslationBundle/pull/334) ([benjamin-hubert](https://github.com/benjamin-hubert))
- Fix logger interface calls [\#325](https://github.com/schmittjoh/JMSTranslationBundle/pull/325) ([gnat42](https://github.com/gnat42))
- Changing the default answer to "Tests pass?" to "no" [\#322](https://github.com/schmittjoh/JMSTranslationBundle/pull/322) ([Nyholm](https://github.com/Nyholm))
- Removed installation instructions for Symfony 2.0.x [\#321](https://github.com/schmittjoh/JMSTranslationBundle/pull/321) ([cmfcmf](https://github.com/cmfcmf))
- Added tests for Extension, compiler passes and config [\#320](https://github.com/schmittjoh/JMSTranslationBundle/pull/320) ([Nyholm](https://github.com/Nyholm))
- update php-parser to either 1.4.1 or 2.x [\#314](https://github.com/schmittjoh/JMSTranslationBundle/pull/314) ([gnat42](https://github.com/gnat42))
- Symfony 3.0 compat [\#297](https://github.com/schmittjoh/JMSTranslationBundle/pull/297) ([MisatoTremor](https://github.com/MisatoTremor))

## [1.2.1](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.2.1) (2016-03-23)

**Fixed bugs:**

- Error when choice label is not defined [\#242](https://github.com/schmittjoh/JMSTranslationBundle/issues/242)
- Fatal Error : FormExtractor due to empty index array [\#106](https://github.com/schmittjoh/JMSTranslationBundle/issues/106)

**Closed issues:**

- Supported Symfony versions [\#305](https://github.com/schmittjoh/JMSTranslationBundle/issues/305)
- New release [\#300](https://github.com/schmittjoh/JMSTranslationBundle/issues/300)
- Truncate Filter [\#299](https://github.com/schmittjoh/JMSTranslationBundle/issues/299)
- \[Symfony\Component\Debug\Exception\ContextErrorException\]                                                                                                                                                                                                     Catchable Fatal Error: Argument 2 passed to JMS\TranslationBundle\Translation\Extractor\FileExtractor::\_\_construct\(\) must be an instance of Symfony\Component\HttpKernel\Log\LoggerInterface, instance of Symfony\Bridge\Monolog\Logger given, called in... [\#293](https://github.com/schmittjoh/JMSTranslationBundle/issues/293)
- Maintainer not responding [\#280](https://github.com/schmittjoh/JMSTranslationBundle/issues/280)
- you might want to do a version \(tag\) 1.2.0? [\#236](https://github.com/schmittjoh/JMSTranslationBundle/issues/236)
- Is this bundle abandoned ? [\#213](https://github.com/schmittjoh/JMSTranslationBundle/issues/213)
- Please update to use newer parser version [\#155](https://github.com/schmittjoh/JMSTranslationBundle/issues/155)
- Unit Tests [\#103](https://github.com/schmittjoh/JMSTranslationBundle/issues/103)

**Merged pull requests:**

- Prepare for release 1.2.1 [\#315](https://github.com/schmittjoh/JMSTranslationBundle/pull/315) ([Nyholm](https://github.com/Nyholm))
- getting rid of the twig text extension [\#313](https://github.com/schmittjoh/JMSTranslationBundle/pull/313) ([benjamin-hubert](https://github.com/benjamin-hubert))
- remove unused injected container [\#312](https://github.com/schmittjoh/JMSTranslationBundle/pull/312) ([gnat42](https://github.com/gnat42))
- remove unused use statement [\#311](https://github.com/schmittjoh/JMSTranslationBundle/pull/311) ([gnat42](https://github.com/gnat42))
- injected request into controller action [\#310](https://github.com/schmittjoh/JMSTranslationBundle/pull/310) ([gnat42](https://github.com/gnat42))
- Only support official supported symfony versions [\#309](https://github.com/schmittjoh/JMSTranslationBundle/pull/309) ([Nyholm](https://github.com/Nyholm))
- Added gitter badge [\#308](https://github.com/schmittjoh/JMSTranslationBundle/pull/308) ([Nyholm](https://github.com/Nyholm))
- updated jquery version to v1.12.0 [\#307](https://github.com/schmittjoh/JMSTranslationBundle/pull/307) ([gnat42](https://github.com/gnat42))
- Added travis build matrix [\#304](https://github.com/schmittjoh/JMSTranslationBundle/pull/304) ([Nyholm](https://github.com/Nyholm))
- Added template for PRs and issues [\#303](https://github.com/schmittjoh/JMSTranslationBundle/pull/303) ([Nyholm](https://github.com/Nyholm))
- add sf3 compatibility: remove Symfony\Component\HttpKernel\Log\Logger… [\#285](https://github.com/schmittjoh/JMSTranslationBundle/pull/285) ([MaximeThoonsen](https://github.com/MaximeThoonsen))

## [1.2.0](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.2.0) (2016-03-23)

**Closed issues:**

- Is this bundle dead ? [\#294](https://github.com/schmittjoh/JMSTranslationBundle/issues/294)
- twig deprecation [\#273](https://github.com/schmittjoh/JMSTranslationBundle/issues/273)
- @Ignore does not work on custom Extractor [\#265](https://github.com/schmittjoh/JMSTranslationBundle/issues/265)
- Conflict with nikic/php-parser 1.4.1 [\#262](https://github.com/schmittjoh/JMSTranslationBundle/issues/262)
- PHP Parser 0.9.1 cannot recognize new php 5.6 syntax. [\#255](https://github.com/schmittjoh/JMSTranslationBundle/issues/255)
- security.yml configuration [\#252](https://github.com/schmittjoh/JMSTranslationBundle/issues/252)
- Error with logger dependency in symfony 2.6.\* [\#237](https://github.com/schmittjoh/JMSTranslationBundle/issues/237)
- config.yml issue [\#214](https://github.com/schmittjoh/JMSTranslationBundle/issues/214)
- Can't extract "%kernel.root\_dir%/../src" dir in config.yml \(translation:extract command\) [\#210](https://github.com/schmittjoh/JMSTranslationBundle/issues/210)
- Cannot use object of type PHPParser\_Comment\_Doc as array [\#205](https://github.com/schmittjoh/JMSTranslationBundle/issues/205)
- Ignore comment cause Fatal error [\#204](https://github.com/schmittjoh/JMSTranslationBundle/issues/204)
- Error on placeholder extraction [\#193](https://github.com/schmittjoh/JMSTranslationBundle/issues/193)
- Tags and versioning [\#190](https://github.com/schmittjoh/JMSTranslationBundle/issues/190)
- Composer installed a fork instead if this bundle [\#177](https://github.com/schmittjoh/JMSTranslationBundle/issues/177)
- Error "no DTD found" [\#169](https://github.com/schmittjoh/JMSTranslationBundle/issues/169)
-  cave [\#168](https://github.com/schmittjoh/JMSTranslationBundle/issues/168)
- Admin UI - no cache options when updating translations [\#151](https://github.com/schmittjoh/JMSTranslationBundle/issues/151)
- Exception in FormExtractor [\#145](https://github.com/schmittjoh/JMSTranslationBundle/issues/145)
- jms/di-extra-bundle should be required in composer.json [\#144](https://github.com/schmittjoh/JMSTranslationBundle/issues/144)
- Web interface error [\#139](https://github.com/schmittjoh/JMSTranslationBundle/issues/139)
- Error with DI [\#133](https://github.com/schmittjoh/JMSTranslationBundle/issues/133)
- XLIFF Wrong file extension [\#128](https://github.com/schmittjoh/JMSTranslationBundle/issues/128)
- Extractor overrides empty "" values with "translation.identifier" [\#125](https://github.com/schmittjoh/JMSTranslationBundle/issues/125)
- Not so helpfull error message [\#84](https://github.com/schmittjoh/JMSTranslationBundle/issues/84)

**Merged pull requests:**

- add PHP 7 [\#291](https://github.com/schmittjoh/JMSTranslationBundle/pull/291) ([ickbinhier](https://github.com/ickbinhier))
- Fix FileWriter::write signature [\#286](https://github.com/schmittjoh/JMSTranslationBundle/pull/286) ([artursvonda](https://github.com/artursvonda))
- Update TranslateController.php [\#271](https://github.com/schmittjoh/JMSTranslationBundle/pull/271) ([nursultanturdaliev](https://github.com/nursultanturdaliev))
- \[WIP\] add support for symfony 3 [\#270](https://github.com/schmittjoh/JMSTranslationBundle/pull/270) ([shieldo](https://github.com/shieldo))
- renamed Symfony2 to Symfony [\#259](https://github.com/schmittjoh/JMSTranslationBundle/pull/259) ([OskarStark](https://github.com/OskarStark))
- Removed deprecated Twig features [\#256](https://github.com/schmittjoh/JMSTranslationBundle/pull/256) ([XWB](https://github.com/XWB))
-  php parser dependency \#244  [\#246](https://github.com/schmittjoh/JMSTranslationBundle/pull/246) ([skotaa](https://github.com/skotaa))
- Fixed example config in the docs [\#215](https://github.com/schmittjoh/JMSTranslationBundle/pull/215) ([attilabukor](https://github.com/attilabukor))
- Fix error due to changed PHPParser API [\#207](https://github.com/schmittjoh/JMSTranslationBundle/pull/207) ([vansante](https://github.com/vansante))
- Added PHP 5.6 and HHVM to travis.yml [\#203](https://github.com/schmittjoh/JMSTranslationBundle/pull/203) ([Nyholm](https://github.com/Nyholm))
- Update composer.json [\#175](https://github.com/schmittjoh/JMSTranslationBundle/pull/175) ([flip111](https://github.com/flip111))
- Moved alert message to previous TD [\#170](https://github.com/schmittjoh/JMSTranslationBundle/pull/170) ([Jaspur](https://github.com/Jaspur))
- Update to the last version of phpparser [\#166](https://github.com/schmittjoh/JMSTranslationBundle/pull/166) ([benji07](https://github.com/benji07))
- Method addBuilder to permit to inject aditionnal builders at runtime [\#165](https://github.com/schmittjoh/JMSTranslationBundle/pull/165) ([paulandrieux](https://github.com/paulandrieux))
- Added PHP5.5 for travis tests [\#163](https://github.com/schmittjoh/JMSTranslationBundle/pull/163) ([tristanbes](https://github.com/tristanbes))
- Sort translation locales [\#161](https://github.com/schmittjoh/JMSTranslationBundle/pull/161) ([slu125](https://github.com/slu125))
- Change ValidationExtractor to extract all properties ending with 'Message' [\#160](https://github.com/schmittjoh/JMSTranslationBundle/pull/160) ([vansante](https://github.com/vansante))
- Update FileUtils.php [\#158](https://github.com/schmittjoh/JMSTranslationBundle/pull/158) ([slu125](https://github.com/slu125))
- Ints should be translatable in choice lists. [\#156](https://github.com/schmittjoh/JMSTranslationBundle/pull/156) ([tarjei](https://github.com/tarjei))
- Handle variants where attr is not an array but a function pointer. [\#153](https://github.com/schmittjoh/JMSTranslationBundle/pull/153) ([tarjei](https://github.com/tarjei))
- Fixed multiple occurences in extraction leading to overwritten desc-valu... [\#149](https://github.com/schmittjoh/JMSTranslationBundle/pull/149) ([peetersdiet](https://github.com/peetersdiet))
- Alter value with desc or id only if new message [\#143](https://github.com/schmittjoh/JMSTranslationBundle/pull/143) ([ghost](https://github.com/ghost))
- Added extractor for "empty\_value" as array  [\#140](https://github.com/schmittjoh/JMSTranslationBundle/pull/140) ([goetas](https://github.com/goetas))
- Fix order of JMS custom elements as per XLIFF spec [\#137](https://github.com/schmittjoh/JMSTranslationBundle/pull/137) ([azogheb](https://github.com/azogheb))
- Added Service to fix XLIFF file extension [\#135](https://github.com/schmittjoh/JMSTranslationBundle/pull/135) ([carlcraig](https://github.com/carlcraig))
- \[FormExtractor\] fixes Fatal error: Call to a member function getDocComment\(\) on a non-object  [\#130](https://github.com/schmittjoh/JMSTranslationBundle/pull/130) ([chbruyand](https://github.com/chbruyand))
- Extract translations from form widgets 'placeholder'- and 'title'-attributes [\#120](https://github.com/schmittjoh/JMSTranslationBundle/pull/120) ([azine](https://github.com/azine))

## [1.1.0](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.1.0) (2013-06-08)

**Closed issues:**

- master and sf \<2.3  [\#116](https://github.com/schmittjoh/JMSTranslationBundle/issues/116)
- Manually add translation [\#109](https://github.com/schmittjoh/JMSTranslationBundle/issues/109)
- PUT no longer allowed by default in Symfony 2.2 [\#99](https://github.com/schmittjoh/JMSTranslationBundle/issues/99)
- Extractor searching same directories mulitple times [\#97](https://github.com/schmittjoh/JMSTranslationBundle/issues/97)
- Translation extractor delete unfound keys [\#94](https://github.com/schmittjoh/JMSTranslationBundle/issues/94)
- PHPParser\_Error with no information to debug [\#92](https://github.com/schmittjoh/JMSTranslationBundle/issues/92)
- Parse strings in annotations [\#91](https://github.com/schmittjoh/JMSTranslationBundle/issues/91)
- Composer does not work with symfony 2.0.x [\#90](https://github.com/schmittjoh/JMSTranslationBundle/issues/90)
- Translation could not be saved [\#89](https://github.com/schmittjoh/JMSTranslationBundle/issues/89)
- Inconsistent defaults between translation:extract and translation:update [\#85](https://github.com/schmittjoh/JMSTranslationBundle/issues/85)
- Regarding Setting Default domain for templates [\#82](https://github.com/schmittjoh/JMSTranslationBundle/issues/82)
- Regarding Forcing Translator to look into a perticular directory [\#81](https://github.com/schmittjoh/JMSTranslationBundle/issues/81)
- Regarding inclusion of Multiple domains [\#80](https://github.com/schmittjoh/JMSTranslationBundle/issues/80)
- Wrong php code generated when using |trans with parameters in twig [\#73](https://github.com/schmittjoh/JMSTranslationBundle/issues/73)
- Use framework.session.cookie\_domain to store "hl" cookie ? [\#72](https://github.com/schmittjoh/JMSTranslationBundle/issues/72)
- Position of @Desc in PHP [\#16](https://github.com/schmittjoh/JMSTranslationBundle/issues/16)

**Merged pull requests:**

- Missing sprintf\(\) invocation. [\#123](https://github.com/schmittjoh/JMSTranslationBundle/pull/123) ([Crozin](https://github.com/Crozin))
- Add anchors in translation UI [\#121](https://github.com/schmittjoh/JMSTranslationBundle/pull/121) ([tkleinhakisa](https://github.com/tkleinhakisa))
- Class metadata interface changes in symfony [\#119](https://github.com/schmittjoh/JMSTranslationBundle/pull/119) ([ajohnstone](https://github.com/ajohnstone))
- FormExtractor extracts labels from create [\#108](https://github.com/schmittjoh/JMSTranslationBundle/pull/108) ([geecu](https://github.com/geecu))
- Search translation in ui [\#107](https://github.com/schmittjoh/JMSTranslationBundle/pull/107) ([tkleinhakisa](https://github.com/tkleinhakisa))
- Twig Embed Support [\#104](https://github.com/schmittjoh/JMSTranslationBundle/pull/104) ([gmoreira](https://github.com/gmoreira))
- Fixe scan directories multiple times [\#102](https://github.com/schmittjoh/JMSTranslationBundle/pull/102) ([tkleinhakisa](https://github.com/tkleinhakisa))
- Fix for \#99 [\#100](https://github.com/schmittjoh/JMSTranslationBundle/pull/100) ([nurikabe](https://github.com/nurikabe))
- Fix for issue \#95 [\#98](https://github.com/schmittjoh/JMSTranslationBundle/pull/98) ([nurikabe](https://github.com/nurikabe))
- Refactor the form extractor to allow extraction from FormListeners [\#96](https://github.com/schmittjoh/JMSTranslationBundle/pull/96) ([acasademont](https://github.com/acasademont))
- The Desc attribute can now be one line before call to trans or transChoice. The same applies to forms. [\#76](https://github.com/schmittjoh/JMSTranslationBundle/pull/76) ([mvrhov](https://github.com/mvrhov))
- fix composer for symfony 2.2 [\#75](https://github.com/schmittjoh/JMSTranslationBundle/pull/75) ([gimler](https://github.com/gimler))
- Clear previous config when setConfig on TranslationUpdater [\#74](https://github.com/schmittjoh/JMSTranslationBundle/pull/74) ([stephpy](https://github.com/stephpy))

## [1.0.0](https://github.com/schmittjoh/JMSTranslationBundle/tree/1.0.0) (2012-09-21)

**Closed issues:**

- Twig Error when launch extract command [\#70](https://github.com/schmittjoh/JMSTranslationBundle/issues/70)
- Example of JMS\TranslationBundle\Annotation\Ignore [\#68](https://github.com/schmittjoh/JMSTranslationBundle/issues/68)
- non-existent service config\_factory [\#67](https://github.com/schmittjoh/JMSTranslationBundle/issues/67)
- Translation\_domain on forms [\#61](https://github.com/schmittjoh/JMSTranslationBundle/issues/61)
- Readme add how to test this bundle as it does not test tand-alone [\#58](https://github.com/schmittjoh/JMSTranslationBundle/issues/58)
- OS ordering seems to be causing git merge conflicts [\#57](https://github.com/schmittjoh/JMSTranslationBundle/issues/57)
- Ids on xliff entries on generation [\#56](https://github.com/schmittjoh/JMSTranslationBundle/issues/56)
- can't tag service jms\_translation.file\_vistor or not working [\#54](https://github.com/schmittjoh/JMSTranslationBundle/issues/54)
- Advanced translations depending on variables gender, number, etc... [\#52](https://github.com/schmittjoh/JMSTranslationBundle/issues/52)
- FileExtractor would not work with current php-parser [\#50](https://github.com/schmittjoh/JMSTranslationBundle/issues/50)
- translation:extract command for a Bundle not correct [\#49](https://github.com/schmittjoh/JMSTranslationBundle/issues/49)
- Issue running script for extracts from the cli and website [\#48](https://github.com/schmittjoh/JMSTranslationBundle/issues/48)
- Look for FormBuilderInterface instances too [\#46](https://github.com/schmittjoh/JMSTranslationBundle/issues/46)
- Extract options/second\_options labels of form field repeated type [\#45](https://github.com/schmittjoh/JMSTranslationBundle/issues/45)
- Fatal Error when exporting translations [\#42](https://github.com/schmittjoh/JMSTranslationBundle/issues/42)
- error in 'clean' install \(used to work before\) [\#41](https://github.com/schmittjoh/JMSTranslationBundle/issues/41)
- illegal XLIFF file format due to jms:reference-file elements [\#39](https://github.com/schmittjoh/JMSTranslationBundle/issues/39)
- Invalid WebUI configurations [\#38](https://github.com/schmittjoh/JMSTranslationBundle/issues/38)
- translation:extract --bundle=XXXBundle =\> grabbing messages from all bundles [\#37](https://github.com/schmittjoh/JMSTranslationBundle/issues/37)
- Keep empty strings [\#36](https://github.com/schmittjoh/JMSTranslationBundle/issues/36)
- yml and linebreaks [\#35](https://github.com/schmittjoh/JMSTranslationBundle/issues/35)
- translating messages from validation.yml [\#33](https://github.com/schmittjoh/JMSTranslationBundle/issues/33)
- Can't run tests [\#30](https://github.com/schmittjoh/JMSTranslationBundle/issues/30)
- Problem with WebUi [\#26](https://github.com/schmittjoh/JMSTranslationBundle/issues/26)
- Please remove dependency on the DI Bundle [\#23](https://github.com/schmittjoh/JMSTranslationBundle/issues/23)
- The twig 'desc' filter does not work with the 'transchoice' filter [\#22](https://github.com/schmittjoh/JMSTranslationBundle/issues/22)
- @Ignore gets ignored for plain text form field labels [\#20](https://github.com/schmittjoh/JMSTranslationBundle/issues/20)
- Extract empty\_values of form field choice/entity type [\#17](https://github.com/schmittjoh/JMSTranslationBundle/issues/17)
- Content of desc filter not dumped [\#14](https://github.com/schmittjoh/JMSTranslationBundle/issues/14)
- How to use @Ignore? [\#13](https://github.com/schmittjoh/JMSTranslationBundle/issues/13)
- WebUI: Update fail without domain "message" \(Bug fix include\) [\#11](https://github.com/schmittjoh/JMSTranslationBundle/issues/11)
- Inline Editing? [\#10](https://github.com/schmittjoh/JMSTranslationBundle/issues/10)
- Empty meaning and wrong description in Message while dumping [\#9](https://github.com/schmittjoh/JMSTranslationBundle/issues/9)
- Use of removed MessageCatalogue-\>all\(\) method in Updater [\#8](https://github.com/schmittjoh/JMSTranslationBundle/issues/8)
- export option to remove jms specific xliff code [\#7](https://github.com/schmittjoh/JMSTranslationBundle/issues/7)
- Translation.loader dependency [\#1](https://github.com/schmittjoh/JMSTranslationBundle/issues/1)

**Merged pull requests:**

- Bugfix in RuntimeException when when trying to load an incorrectly formatted XML [\#71](https://github.com/schmittjoh/JMSTranslationBundle/pull/71) ([skwi](https://github.com/skwi))
- Use the "validators" domain for the "invalid\_message" options in forms. [\#66](https://github.com/schmittjoh/JMSTranslationBundle/pull/66) ([bvleur](https://github.com/bvleur))
- Parse default option for translation\_domain [\#65](https://github.com/schmittjoh/JMSTranslationBundle/pull/65) ([bvleur](https://github.com/bvleur))
- Extract invalid\_message option for repeated field type [\#64](https://github.com/schmittjoh/JMSTranslationBundle/pull/64) ([bvleur](https://github.com/bvleur))
- Support translation\_domain option on forms [\#63](https://github.com/schmittjoh/JMSTranslationBundle/pull/63) ([bvleur](https://github.com/bvleur))
- \[\#57\] fix problem with undetermined comparison callback on uasort\( , strcasecmp\) [\#62](https://github.com/schmittjoh/JMSTranslationBundle/pull/62) ([cordoval](https://github.com/cordoval))
- \[\#56\] fix ids on xliff files to be sha1 to avoid merging conflicts [\#59](https://github.com/schmittjoh/JMSTranslationBundle/pull/59) ([cordoval](https://github.com/cordoval))
- Support labels for "repeated" field groups [\#55](https://github.com/schmittjoh/JMSTranslationBundle/pull/55) ([bvleur](https://github.com/bvleur))
- Look for FormBuilderInterface instances too [\#47](https://github.com/schmittjoh/JMSTranslationBundle/pull/47) ([albyrock87](https://github.com/albyrock87))
- Change incorrect "master-dev" to "dev-master" [\#44](https://github.com/schmittjoh/JMSTranslationBundle/pull/44) ([jonathaningram](https://github.com/jonathaningram))
- Keep empty strings, Yml line breaks [\#43](https://github.com/schmittjoh/JMSTranslationBundle/pull/43) ([cdfre](https://github.com/cdfre))
- Specify a php-parser version [\#40](https://github.com/schmittjoh/JMSTranslationBundle/pull/40) ([nikic](https://github.com/nikic))
- Fix "Argument 7 passed \(...\) must be an array, null given" [\#34](https://github.com/schmittjoh/JMSTranslationBundle/pull/34) ([frosas](https://github.com/frosas))
- check consistency of messages desc [\#32](https://github.com/schmittjoh/JMSTranslationBundle/pull/32) ([arnaud-lb](https://github.com/arnaud-lb))
- Added CDATA support for XLIFF format [\#31](https://github.com/schmittjoh/JMSTranslationBundle/pull/31) ([dlsniper](https://github.com/dlsniper))
- Changed the XLIFF dumper so that it supports CDATA for target and source [\#28](https://github.com/schmittjoh/JMSTranslationBundle/pull/28) ([dlsniper](https://github.com/dlsniper))
- don't pass the message id in the URL \(workaround for https://github.com/symfony/symfony/issues/2962 \) [\#27](https://github.com/schmittjoh/JMSTranslationBundle/pull/27) ([arnaud-lb](https://github.com/arnaud-lb))
- add composer file [\#25](https://github.com/schmittjoh/JMSTranslationBundle/pull/25) ([cdfre](https://github.com/cdfre))
- Add Support for Choices [\#21](https://github.com/schmittjoh/JMSTranslationBundle/pull/21) ([AlexKa](https://github.com/AlexKa))
- Fix DefaultApplyingNodeVisitor when |trans has replacement param [\#18](https://github.com/schmittjoh/JMSTranslationBundle/pull/18) ([arnaud-lb](https://github.com/arnaud-lb))
- set validator messages' domain to "validators" [\#15](https://github.com/schmittjoh/JMSTranslationBundle/pull/15) ([arnaud-lb](https://github.com/arnaud-lb))
- Resolve issue \#11 [\#12](https://github.com/schmittjoh/JMSTranslationBundle/pull/12) ([jpierront](https://github.com/jpierront))
- Add an option to load external resources [\#6](https://github.com/schmittjoh/JMSTranslationBundle/pull/6) ([rande](https://github.com/rande))
- Add LoggerAwareInterface, display error messages on parsing errors [\#5](https://github.com/schmittjoh/JMSTranslationBundle/pull/5) ([rande](https://github.com/rande))
- Add domain management [\#4](https://github.com/schmittjoh/JMSTranslationBundle/pull/4) ([rande](https://github.com/rande))
- add an option to keep old translation [\#3](https://github.com/schmittjoh/JMSTranslationBundle/pull/3) ([rande](https://github.com/rande))
- Add only domains option [\#2](https://github.com/schmittjoh/JMSTranslationBundle/pull/2) ([rande](https://github.com/rande))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
