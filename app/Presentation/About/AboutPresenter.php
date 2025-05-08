<?php

declare(strict_types=1);

namespace App\Presentation\About;
use App\Model\NewsFacade;

use Nette;

final class AboutPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private NewsFacade $newsFacade
    ) {}
    
    public function renderDefault(): void
    {
        $this->template->news = $this->newsFacade->getAllNews();
    }
    
}
