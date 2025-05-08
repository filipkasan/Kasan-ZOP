<?php

declare(strict_types=1);

namespace App\Presentation\Register;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;

final class RegisterPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(private Explorer $database)
{
	parent::__construct();
	\Tracy\Debugger::barDump($this->database->getConnection()->getDsn(), 'DB DSN');
}


    protected function createComponentSignUpForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired();

        $form->addEmail('email', 'E-mail:')
            ->setRequired();

        $form->addPassword('password', 'Heslo:')
            ->setRequired()
            ->addRule(Form::MinLength, 'Heslo musí mít alespoň %d znaků.', 6);

        $form->addPassword('passwordVerify', 'Potvrzení hesla:')
            ->setRequired()
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password']);

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = function (Form $form, \stdClass $values): void {
            if ($this->database->table('users')->where('username', $values->username)->fetch()) {
                $form['username']->addError('Uživatelské jméno je již obsazeno.');
                return;
            }

            $this->database->table('users')->insert([
                'username' => $values->username,
                'email' => $values->email,
                'password' => password_hash($values->password, PASSWORD_DEFAULT),
                'role' => 'user',
            ]);

            $this->flashMessage('Registrace proběhla úspěšně.', 'success');
            $this->redirect('Login:default');
        };

        return $form;
    }
}
