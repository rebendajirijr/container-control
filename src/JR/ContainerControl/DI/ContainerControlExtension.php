<?php

namespace JR\ContainerControl\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers as ConfigHelpers;
use Nette\Utils\Validators;

/**
 * DI Compiler extension for ContainerControl.
 *
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 */
class ContainerControlExtension extends CompilerExtension
{
	/** @var string */
	const DEFAULT_EXTENSION_NAME = 'containerControl';
	
	/** @var string */
	const TAG_CONTAINER_CONTROL_CONTROL = 'containerControlControl';
	
	/** @var array */
	public $defaults = [
		'containerControls' => [
			'default',
		],
	];
	
	/** @var array */
	public $containerControlDefaults = [
		'implement' => 'JR\ContainerControl\IContainerControlFactory',
		'controlFactories' => [],
	];
	
	/*
	 * @inheritdoc
	 */
	public function loadConfiguration()
	{
		parent::loadConfiguration();
		
		$config = $this->validateConfig($this->defaults);
		$containerBuilder = $this->getContainerBuilder();
		
		Validators::assertField($config, 'containerControls', 'array');
		
		$autowired = (count($config['containerControls']) === 1);
		
		foreach ($config['containerControls'] as $key => $containerControlOptions) {
			if (is_string($containerControlOptions)) {
				$containerControlOptions = [
					'name' => $containerControlOptions,
				];
			} else {
				$containerControlOptions['name'] = $key;
			}
			
			$containerControlOptions = ConfigHelpers::merge($containerControlOptions, $this->containerControlDefaults);
			
			Validators::assertField($containerControlOptions, 'name', 'string');
			Validators::assertField($containerControlOptions, 'implement', 'string');
			
			$containerBuilder->addDefinition($this->prefix($containerControlOptions['name'] . 'ContainerControlFactory'))
				->setImplement($containerControlOptions['implement'])
				->setAutowired($autowired);
		}
	}
	
	/*
	 * @inheritdoc
	 */
	public function beforeCompile()
	{
		parent::beforeCompile();
		
		$containerBuilder = $this->getContainerBuilder();
		
		foreach ($containerBuilder->findByTag(static::TAG_CONTAINER_CONTROL_CONTROL) as $serviceName => $containerControlControlTags) {
			Validators::assertField($containerControlControlTags, 'name', 'string');
			Validators::assertField($containerControlControlTags, 'containerControl', 'string');
			
			$containerControlService = $containerBuilder->getDefinition($this->prefix($containerControlControlTags['containerControl'] . 'ContainerControlFactory'));
			$containerControlService->addSetup('?->registerControlFactory(?, ?)', [
				'@self',
				$containerControlControlTags['name'],
				'@' . $serviceName,
			]);
		}
	}
	
	/**
	 * @param Configurator
	 * @return void
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
			$compiler->addExtension(static::DEFAULT_EXTENSION_NAME, new static());
		};
	}
}
