<?php // vim: ts=4 sw=4 ai:
namespace App\BackendModule;

use Nette\Http\User;

/**
 * Base class for Admin presenters
 * @author Lopo <lopo@losys.eu>
 */
class BasePresenter
extends \Lohini\Presenters\BasePresenter
{
	/**
	 * @param type $element
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
		$ref=$this->getReflection();
		$user=$this->getUser();

		if (!$user->isLoggedIn()) {
			if ($user->logoutReason===User::INACTIVITY) {
				$this->flashMessage(__('You have been logged out due to inactivity. Please login again.'), 'info');
				}
			$this->flashMessage(__('Restricted access - logIn first'), 'warning');
			$this->redirect($this->loginLink, array('backlink' => $this->getApplication()->storeRequest()));
			}
		try {
			if ($user->identity instanceof \Lohini\Database\Models\Entities\Identity) {
				$this->lang=$user->identity->lang;
				}
			}
		catch (\Nette\InvalidStateException $e) {
			if ($user->logoutReason===User::INACTIVITY) {
				$this->flashMessage(__('Your login session expired. Please login again.'), 'error');
				}
			$user->logout(TRUE);
			$this->redirect($this->loginLink, array('backlink' => $this->getApplication()->storeRequest()));
			}
		$method=$this->formatActionMethod($this->getAction());
		if ($ref->hasMethod($method) && !$user->isAllowed($method)) {
			throw new \Nette\Application\ForbiddenRequestException;
			}
		$method=$this->formatRenderMethod($this->getView());
		if ($ref->hasMethod($method) && !$user->isAllowed($method)) {
			throw new \Nette\Application\ForbiddenRequestException;
			}
		$signal=$this->getSignal();
		if ($signal) {
			$method=$this->formatSignalMethod($signal[1]);
			if ($ref->hasMethod($method) && !$user->isAllowed($method)) {
				throw new \Nette\Application\ForbiddenRequestException;
				}
			}
	}
}
