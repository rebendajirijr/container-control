<?php

namespace JR\ContainerControl;

use Nette\Application\UI\Control;

/**
 * Common Control factory.
 * 
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 */
interface IControlFactory
{
	/**
	 * @return Control
	 */
	function create();
}
