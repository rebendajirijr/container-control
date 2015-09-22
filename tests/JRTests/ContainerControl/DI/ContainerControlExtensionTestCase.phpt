<?php

namespace JRTests\ContainerControl\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\Utils\Strings;
use Tester\Assert;
use Tester\TestCase;
use JR\ContainerControl\DI\ContainerControlExtension;
use JR\ContainerControl\IContainerControlFactory;
use JR\ContainerControl\ContainerControl;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Description of ContainerControlExtensionTestCase.
 *
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 */
final class ContainerControlExtensionTestCase extends TestCase
{
	/**
	 * @return void
	 */
	public function testDefaultConfiguration()
	{
		$configurator = $this->createConfigurator();
		$container = $configurator->createContainer();
		
		/* @var $containerControlFactory IContainerControlFactory */
		$containerControlFactory = $container->getService('containerControl.defaultContainerControlFactory');
		Assert::type('JR\ContainerControl\IContainerControlFactory', $containerControlFactory);
	}
	
	/**
	 * @return void
	 */
	public function testMultipleContainerControlsConfiguration()
	{
		$configurator = $this->createConfigurator([
			'containerControls' => [
				'containerControl1',
				'containerControl2',
			],
		]);
		$container = $configurator->createContainer();
		
		/* @var $containerControlFactory1 IContainerControlFactory */
		$containerControlFactory1 = $container->getService('containerControl.containerControl1ContainerControlFactory');
		Assert::type('JR\ContainerControl\IContainerControlFactory', $containerControlFactory1);
		
		/* @var $containerControlFactory2 IContainerControlFactory */
		$containerControlFactory2 = $container->getService('containerControl.containerControl2ContainerControlFactory');
		Assert::type('JR\ContainerControl\IContainerControlFactory', $containerControlFactory2);
		
		Assert::exception(function () use ($container) {
			$container->getService('containerControl.containerControl3ContainerControlFactory');
		}, 'Nette\DI\MissingServiceException');
	}
	
	/**
	 * @return void
	 */
	public function testControlFactoryIsRegistered()
	{
		$configurator = $this->createConfigurator();
		$configurator->addConfig(\Tester\FileMock::create('
services:
	dummyControlFactory:
		class: JRTests\ContainerControl\TestObjects\DummyControlFactory
		tags: [containerControlControl: [name: \'dummyControl\', containerControl: \'default\']]
', 'neon'));
		$container = $configurator->createContainer();
		
		/* @var $containerControlFactory IContainerControlFactory */
		$containerControlFactory = $container->getService(ContainerControlExtension::DEFAULT_EXTENSION_NAME . '.defaultContainerControlFactory');
		
		/* @var $containerControl ContainerControl */
		$containerControl = $containerControlFactory->create();
		
		Assert::type('JR\ContainerControl\ContainerControl', $containerControl);
		Assert::type('JRTests\ContainerControl\TestObjects\DummyControl', $containerControl->getComponent('dummyControl'));
	}
	
	/**
	 * @return Configurator
	 */
	private function createConfigurator(array $config = [])
	{
		$configurator = new Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addParameters([
			'container' => [
				'class' => 'SystemContainer_' . Strings::random(),
			],
		]);
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($config) {
			$containerControlExtension = new ContainerControlExtension();
			$containerControlExtension->setConfig($config);
			$compiler->addExtension(ContainerControlExtension::DEFAULT_EXTENSION_NAME, $containerControlExtension);
			
			$extensions = $compiler->getExtensions('Nette\Bridges\ApplicationDI\ApplicationExtension');
			$applicationExtension = array_shift($extensions);
			if ($applicationExtension !== NULL) {
				$applicationExtension->defaults['scanDirs'] = FALSE;
			}
		};
		return $configurator;
	}
}

run(new ContainerControlExtensionTestCase());
