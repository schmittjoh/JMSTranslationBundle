<a href="#"><?php echo $view['translator']->trans('foo.bar') ?></a>

<a href="#"><?php echo /** @Desc("Foo Bar") */ $view['translator']->trans('baz', array(), 'moo') ?></a>