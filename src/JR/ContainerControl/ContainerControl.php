<?php

namespace JR\ContainerControl;

use Nette\Application\UI\Control;

/**
 * Description of ContainerControl.
 *
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 */
class ContainerControl extends Control
{
	/** @var IControlFactory[] */
	private $controlFactories = [];
	
	/**
	 * @param string Component name
	 * @param IControlFactory
	 * @return self
	 * @throws ControlFactoryAlreadyRegisteredException
	 */
	public function registerControlFactory($name, IControlFactory $controlFactory)
	{
		if (array_key_exists($name, $this->controlFactories)) {
			throw new ControlFactoryAlreadyRegisteredException("Control factory with name '$name' is already registered.");
		}
		$this->controlFactories[$name] = $controlFactory;
		return $this;
	}
	
	/*
	 * @inheritdoc
	 */
	protected function createComponent($name)
	{
		if (isset($this->controlFactories[$name])) {
			$controlFactory = $this->controlFactories[$name];
			return $controlFactory->create();
		}
		return parent::createComponent($name);
	}
}
