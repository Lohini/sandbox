<?php // vim: ts=4 sw=4 ai:
/**
 * This file is part of Lohini (http://lohini.net)
 *
 * @copyright (c) 2010, 2013 Lopo <lopo@lohini.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 */
namespace App;

/**
 * Base presenter for all application presenters
 *
 * @author Lopo <lopo@lohini.net>
 */
abstract class BasePresenter
extends \Lohini\Application\UI\Presenter
{
	protected function createComponentCss($name)
	{
		return new \Lohini\WebLoader\CssLoader($this, $name);
	}

	protected function createComponentJs($name)
	{
		$ldr=new \Lohini\WebLoader\JsLoader($this, $name);
		$ldr->useHeadJs=FALSE;
		return $ldr;
	}
}
