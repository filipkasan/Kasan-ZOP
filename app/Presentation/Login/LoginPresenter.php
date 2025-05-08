<?php

declare(strict_types=1);

namespace App\Presentation\Login;

use App\Model\AuthFacade;
use Nette;
use Nette\Application\UI\Form;

final class LoginPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private AuthFacade $authFacade,
	) {}

	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addEmail('email', 'E-mail:')
			->setRequired('Zadej e-mail.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadej heslo.');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
				$this->getUser()->login($data->email, $data->password);
				$this->redirect('Home:default');
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError($e->getMessage());
			}
		};

		return $form;
	}

	protected function createComponentSignUpForm(): Form
	{
		$form = new Form;
		$form->addEmail('email', 'E-mail:')
			->setRequired('Zadej e-mail.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadej heslo.')
			->addRule($form::MinLength, 'Heslo musí mít alespoň %d znaků.', 6);

		$form->addPassword('passwordVerify', 'Heslo znovu:')
			->setRequired('Zadej heslo znovu.')
			->addRule($form::Equal, 'Hesla se neshodují.', $form['password']);

		$form->addSubmit('send', 'Registrovat se');

		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
				$this->authFacade->register($data->email, $data->password);
				$this->flashMessage('Úspěšná registrace. Můžeš se přihlásit.');
				$this->redirect('this');
			} catch (\Exception $e) {
				$form->addError($e->getMessage());
			}
		};

		return $form;
	}

	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jsi odhlášen.');
		$this->redirect('Login:default');
	}
}
