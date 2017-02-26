<?php $view['translation']->trans('php.foo') ?>

<?php /** @Desc("Bar") */ $view['translation']->trans('php.bar') ?>

<?php /** @Meaning("Baz") */ $view['translation']->trans('php.baz') ?>

<?php /** @Desc("Foo") @Meaning("Bar") */ $view['translation']->trans('php.foo_bar') ?>