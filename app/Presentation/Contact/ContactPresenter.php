<?php
declare(strict_types=1);

namespace App\Presentation\Contact;

use Nette;
use Nette\Application\UI\Form;

final class ContactPresenter extends Nette\Application\UI\Presenter
{
    public function renderDefault(): void
    {
        // Můžeš předat data do šablony, pokud potřebuješ
    }

    protected function createComponentContactForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Jméno:')
            ->setRequired('Zadejte prosím své jméno.');

        $form->addEmail('email', 'E-mail:')
            ->setRequired('Zadejte prosím svůj e-mail.');

        $form->addTextArea('message', 'Zpráva:')
            ->setRequired('Zadejte prosím zprávu.');

        $form->addSubmit('send', 'Odeslat zprávu');

        $form->onSuccess[] = [$this, 'contactFormSucceeded'];

        return $form;
    }

    public function contactFormSucceeded(Form $form, \stdClass $values): void
    {
        $data = sprintf(
            "Jméno: %s\nE-mail: %s\nZpráva:\n%s\n---\n",
            $values->name,
            $values->email,
            $values->message
        );

        $file = __DIR__ . '/../../../message.txt';

        file_put_contents($file, $data, FILE_APPEND | LOCK_EX);

        $this->flashMessage('Zpráva byla úspěšně odeslána.', 'success');
        $this->redirect('this');
    }
}
