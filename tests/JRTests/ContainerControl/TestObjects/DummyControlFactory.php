<?php

namespace JRTests\ContainerControl\TestObjects;

use JR\ContainerControl\IControlFactory;

/**
 * Dummy control factory.
 * 
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 */
class DummyControlFactory implements IControlFactory
{
	/*
	 * @inheritdoc
	 */
	public function create()
	{
		return new DummyControl();
	}
}
