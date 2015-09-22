<?php

namespace JRTests\ContainerControl;

use Tester\Assert;
use Tester\TestCase;
use JR\ContainerControl\ContainerControl;
use JRTests\ContainerControl\TestObjects\DummyControlFactory;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Tests ContainerControl usage.
 *
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 */
final class ContainerControlTestCase extends TestCase
{
	/**
	 * @return void
	 */
	public function testRegisterControlFactory()
	{
		$containerControl = new ContainerControl();
		
		$firstControlFactory = new DummyControlFactory();
		$secondControlFactory = new DummyControlFactory();
		
		$containerControl->registerControlFactory('firstControl', $firstControlFactory);
		$containerControl->registerControlFactory('secondControl', $secondControlFactory);
		
		Assert::type('JRTests\ContainerControl\TestObjects\DummyControl', $containerControl->getComponent('firstControl'));
		Assert::type('JRTests\ContainerControl\TestObjects\DummyControl', $containerControl->getComponent('secondControl'));
	}
	/**
	 * @return void
	 */
	public function testRegisterControlFactoryThrowsControlFactoryAlreadyRegisteredException()
	{
		$name = 'firstControl';
				
		$containerControl = new ContainerControl();
		$containerControl->registerControlFactory($name, new DummyControlFactory());
		
		Assert::exception(function () use ($name, $containerControl) {
			$containerControl->registerControlFactory($name, new DummyControlFactory());
		}, 'JR\ContainerControl\ControlFactoryAlreadyRegisteredException', "Control factory with name '$name' is already registered.");
	}
}

run(new ContainerControlTestCase());
