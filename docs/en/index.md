# Quickstart

This package provides simple solution to a problem of having uncertain number of controls which you want to use in your application (e.g. using modular concept).
These controls can be registered into single instance of `JR\ContainerControl\ContainerControl`, thus being accessible throughout all your application (namely in [Presenters](https://github.com/nette/application/blob/master/src/Application/UI/Presenter.php)).

## Installation

1. Install the package using [Composer](http://getcomposer.org/):

   ```sh
   $ composer require jr/container-control
   ```

2. Register & optionally configure the extension via standard [neon config](https://github.com/nette/neon):

   ```yml
   extensions:
       containerControl: JR\ContainerControl\DI\ContainerControlExtension

   containerControl:
       containerControlFactories:
           # note that following 'default' container control factory is registered automatically
           default:
               implement: 'JR\ContainerControl\IContainerControlFactory'
           another:
               implement: 'Vendor\App\IContainerControlFactory' # name of your custom container control factory interface

   services:
       # in order to register following control factory automatically into the 'default' container control,
       # you must set 'containerControlControl' tag with name of the control and target container control
       myControlFactory:
           implement: 'Vendor\App\IMyControlFactory' # must be of JR\ContainerControl\IControlFactory type
           tags: [containerControlControl: [name: 'myControl', containerControl: 'default']] # 'default' is name of target container control as defined above
   ```

## Usage

First create `ContainerControl` factory method inside one of your Presenters (or any other [Control](https://github.com/nette/application/blob/master/src/Application/UI/Control.php)):

```php
<?php

use Nette\Application\UI\Presenter;
use JR\ContainerControl\ContainerControl;
use JR\ContainerControl\IContainerControlFactory;

class MyPresenter extends Presenter
{
    /** @var IContainerControlFactory */
    private $containerControlFactory;

    public function __construct(IContainerControlFactory $containerControlFactory)
    {
        $this->containerControlFactory = $containerControlFactory;
    }

    /**
     * @param string
     * @return ContainerControl
     */
    protected function createComponentContainerControl($name)
    {
        return $this->containerControlFactory->create();
    }
}
```

Then you can use the `ContainerControl` in the same way as any other [Control](https://github.com/nette/application/blob/master/src/Application/UI/Control.php):
```html
{* @layout.latte *}
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        {* render whole container control *}
        {control containerControl}
        {* or any particular subcomponent(s) *}
        {control containerControl-newsControl}
    </body>
</html>
```
