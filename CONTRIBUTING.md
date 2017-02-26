Contributing
============

If you're here, you would like to contribute to this project and you're really welcome!

Bug reports
-----------

If you find a bug or a documentation issue, please report it or even better: fix it :). If you report it,
please be as precise as possible. Please use the provided template when creating an issue. Your issue is 
more likley to get fast feedback if you are clear and detailed in your report.

Feature requests
----------------

If you think a feature is missing, please report it or even better: implement it :). If you report it,describe the 
feature as precisely as possible, including what you would like to see implemented and we will discuss what is the 
best approach for it. If you can do some research before submitting it and provide links to the resources in your 
description, you're awesome! It will allow us to more easily understood/implement it.

Sending a Pull Request
----------------------

If you're here, you are going to fix a bug or implement a feature and you're the best!
To do it, first fork the repository on Github. Following the commands below you can clone it and create a new branch:

```bash
$ git clone git@github.com:your-name/repo-name.git
$ git checkout -b feature-or-bug-fix-description
```

Then install the dependencies through Composer:

```bash
$ composer install
```

Write code and tests. When you are ready, run the tests.

```bash
$ phpunit
```

When you feel your code is ready, tested and documented, you can commit and push it with the following commands:

```bash
$ git commit -m "Feature or bug fix description"
$ git push origin feature-or-bug-fix-description
```

> Please write your commit messages in the imperative and follow the 
[guidelines](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html) for clear and concise messages.

Then [create a pull request](https://help.github.com/articles/creating-a-pull-request/) on GitHub.

Please make sure that each individual commit in your pull request is meaningful.
If you had to make multiple intermediate commits while developing,
please squash them before submitting with the following commands
(here, we assume you would like to squash 3 commits in a single one):

```bash
$ git rebase -i HEAD~3
```

If your branch conflicts with the master branch, you will need to rebase and re-push it with the following commands:

```bash
$ git remote add upstream git@github.com:orga/repo-name.git
$ git pull --rebase upstream master
$ git push -f origin feature-or-bug-fix-description
```

Writing tests
-------------

Test cases should be as small as possible and cover all the new functionallity you add. Many of our tests are using fixtures. You 
should not modify those fixtures, instead you should create new ones. Modifying the fixtures may cause other tests to break and you should only modify other tests if you are making BC breaking changes. 

Coding standard
---------------

This repository follows the [PSR-2 standard](http://www.php-fig.org/psr/psr-2) and 
the [Symfony coding standard](http://symfony.com/doc/current/contributing/code/standards.html) so, if you want to contribute,
you must follow these rules.

Reviewing a pull request
------------------------

This repository has a few maintainers, to ensure the quality of the code each pull request must be reviewed by at least two 
maintainers. The second maintaier is free to merge if the first maintainer also agrees with the change. 


Release
--------

We are trying to follow [semver](http://semver.org). When you are making BC breaking changes,
please let us know why you think it is important.
In this case, your patch can only be included in the next major version.

When it is time for a release a maintainer must: 
* Review the changes made since last release to decide the next version number
* Update the Changelog.md
* Tag the new version and use the GitHub website to create a release on that tag. 

License
-------

This repository are licensed under the Apache

