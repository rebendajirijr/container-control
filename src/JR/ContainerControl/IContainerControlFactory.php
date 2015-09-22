<?php

namespace JR\ContainerControl;

/**
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 */
interface IContainerControlFactory
{
	/**
	 * @return ContainerControl
	 */
	function create();
}
