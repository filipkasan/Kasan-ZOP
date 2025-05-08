<?php

declare(strict_types=1);

namespace App\Presentation\Admin;

use Nette;
use Nette\Application\UI\Presenter;
use App\Model\AuthFacade;
use App\Model\NewsFacade;
use Nette\Database\Explorer;
use Nette\Security\User;
use Nette\Application\UI\Form;

final class AdminPresenter extends Presenter
{
    public function __construct(
        private Explorer $database,
        private User $user,
        private AuthFacade $authFacade,
        private NewsFacade $newsFacade
    ) {}

    public function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn() || $this->user->getIdentity()->role !== 'admin') {
            $this->redirect('login:default');
        }
    }

    public function renderDefault(): void
    {
        $this->template->adminName = $this->user->getIdentity()->username;
        $this->template->users = $this->database
            ->table('users')
            ->order('createDate DESC')
            ->fetchAll();

        $this->template->news = $this->newsFacade->getAllNews();
    }

    // UŽIVATELÉ

    public function handleDeleteUser(int $id): void
    {
        if ($id === $this->user->id) {
            $this->flashMessage('Nemůžeš smazat sám sebe.', 'error');
        } else {
            $this->authFacade->deleteUser($id);
            $this->flashMessage('Uživatel byl smazán.', 'success');
        }
        $this->redirect('this');
    }

    protected function createComponentEditUserForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');
        $form->addText('username', 'Uživatel')->setRequired();
        $form->addText('email', 'Email')->setRequired()->addRule(Form::EMAIL);
        $form->addText('role', 'Role');

        $form->addSubmit('save', 'Uložit změny');
        $form->onSuccess[] = [$this, 'editUserFormSucceeded'];
        return $form;
    }

    public function handleEditUser(int $id): void
    {
        $user = $this->database->table('users')->get($id);
        if (!$user) {
            $this->flashMessage('Uživatel nenalezen.', 'error');
            $this->redirect('this');
        }

        $this['editUserForm']->setDefaults($user->toArray());
        $this->template->editingUser = true;
    }

    public function editUserFormSucceeded(Form $form, array $values): void
    {
        $id = (int) $values['id'];
        unset($values['id']);

        $this->authFacade->updateUser($id, $values);
        $this->flashMessage('Uživatel upraven.', 'success');
        $this->redirect('this');
    }

    public function actionEditUser(int $id): void
    {
        $user = $this->database->table('users')->get($id);

        if (!$user) {
            $this->flashMessage('Uživatel nenalezen.', 'error');
            $this->redirect('default');
        }

        $this['editUserForm']->setDefaults([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    protected function createComponentAddUserForm(): Form
    {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired();

        $form->addEmail('email', 'Email:')
            ->setRequired();

        $form->addPassword('password', 'Heslo:')
            ->setRequired();

        $form->addPassword('passwordVerify', 'Potvrzení hesla:')
            ->setRequired()
            ->addRule(Form::EQUAL, 'Hesla se musí shodovat.', $form['password']);

        $form->addSelect('role', 'Role:', [
            'user' => 'User',
            'admin' => 'Admin',
        ])
        ->setDefaultValue('user')
        ->setRequired();

        $form->addSubmit('send', 'Přidat');
        $form->onSuccess[] = [$this, 'addUserFormSucceeded'];
        return $form;
    }

    public function addUserFormSucceeded(Form $form, array $values): void
    {
        try {
            $username = $values['username'] ?: explode('@', $values['email'])[0];

            $this->database->table('users')->insert([
                'email' => $values['email'],
                'username' => $username,
                'password' => password_hash($values['password'], PASSWORD_DEFAULT),
                'role' => $values['role'],
            ]);

            $this->flashMessage('Uživatel byl úspěšně přidán.', 'success');
        } catch (\Exception $e) {
            $this->flashMessage('Chyba: ' . $e->getMessage(), 'error');
        }

        $this->redirect('this');
    }

    // NOVINKY

    protected function createComponentAddNewsForm(): Form
    {
        $form = new Form;

        $form->addText('title', 'Nadpis:')
            ->setRequired();

        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

        $form->addSubmit('send', 'Přidat novinku');
        $form->onSuccess[] = [$this, 'addNewsFormSucceeded'];
        return $form;
    }

    public function addNewsFormSucceeded(Form $form, array $values): void
    {
        $this->newsFacade->addNews($values['title'], $values['content']);
        $this->flashMessage('Novinka přidána.', 'success');
        $this->redirect('this');
    }

    public function handleDeleteNews(int $id): void
    {
        $this->newsFacade->deleteNews($id);
        $this->flashMessage('Novinka byla smazána.', 'success');
        $this->redirect('this');
    }

    public function handleEditNews(int $id): void
    {
        $news = $this->newsFacade->getNewsById($id);
        if (!$news) {
            $this->flashMessage('Novinka nenalezena.', 'error');
            $this->redirect('this');
        }

        $this['editNewsForm']->setDefaults($news->toArray());
        $this->template->editingNews = true;
    }

    protected function createComponentEditNewsForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');
        $form->addText('title', 'Nadpis:')
            ->setRequired();
        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

        $form->addSubmit('save', 'Uložit změny');
        $form->onSuccess[] = [$this, 'editNewsFormSucceeded'];
        return $form;
    }

    public function editNewsFormSucceeded(Form $form, array $values): void
    {
        $this->newsFacade->updateNews((int) $values['id'], $values['title'], $values['content']);
        $this->flashMessage('Novinka byla upravena.', 'success');
        $this->redirect('this');
    }
}
