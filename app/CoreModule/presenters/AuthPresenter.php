<?php // vim: ts=4 sw=4 ai:
/**
 * This file is part of Lohini
 *
 * @copyright (c) 2010, 2011 Lopo <lopo@losys.eu>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 */
namespace App\CoreModule;

use Lohini\Application\UI\Form,
	Lohini\Environment;

/**
 * Authentification presenter
 *
 * @author Lopo <lopo@losys.eu>
 */
class AuthPresenter
extends \Lohini\Presenters\BasePresenter
{
	public function renderLogin()
	{
		$this->template->formLogin=$this['formLogin'];
	}

	/**
	 * Sign in form component factory.
	 * @return AppForm
	 */
	protected function createComponentFormLogin()
	{
		$form=new Form;
		$form->addText('username', 'Username')
				->addRule(Form::FILLED, 'Please provide a username.');
		$form->addPswd('password', 'Password')
				->addRule(Form::FILLED, 'Please provide a password.');
		$form->addCheckbox('remember', 'Remember me on this computer');
		$form->addSubmit('send', 'Sign in');
		$form->onSuccess[]=callback($this, 'formLoginSubmitted');
		return $form;
	}

	/**
	 * @param \Lohini\Application\UI\Form $form
	 */
	public function formLoginSubmitted($form)
	{
		try {
			$values=$form->getValues();
			$user=$this->getUser();
			if ($values['remember']) {
				$user->setExpiration('+ 14 days', FALSE);
				}
			else {
				$user->setExpiration('+ 20 minutes', TRUE);
				}
			$user->login($values->username, $values->password);
			$this->flashMessage(_('Login successfull'), 'success');
			$this->redirectUrl($this->context->router->getRootLink());
			}
		catch (\Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			}
	}

	public function actionLogout()
	{
		$this->getUser()->logout();
		$this->flashMessage(_('You have been signed out.'));
		$this->redirectUrl($this->context->router->getRootLink());
	}
}
