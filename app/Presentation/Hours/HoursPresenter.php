<?php

declare(strict_types=1);

namespace App\Presentation\Hours;

use Nette\Application\UI\Presenter;

final class HoursPresenter extends Presenter
{
    private array $hours = [
        [
            'id' => 1,
            'date' => '2025-04-01',
            'duration' => 5,
            'category' => 'Programování',
            'note' => 'Práce na dashboardu'
        ],
        [
            'id' => 2,
            'date' => '2025-04-02',
            'duration' => 2,
            'category' => 'Meetingy',
            'note' => 'Týmová porada'
        ],
        [
            'id' => 3,
            'date' => '2025-04-03',
            'duration' => 3,
            'category' => 'Design',
            'note' => 'Wireframy pro novou sekci'
        ],
        [
            'id' => 4,
            'date' => '2025-04-04',
            'duration' => 6,
            'category' => 'Programování',
            'note' => 'Implementace heatmapy'
        ]
    ];

    public function renderDefault(): void
    {
        $this->template->hours = $this->hours;
    }

    public function renderDetail(int $id): void
    {
        foreach ($this->hours as $hour) {
            if ($hour['id'] === $id) {
                $this->template->hour = $hour;
                return;
            }
        }

        $this->error('Záznam nebyl nalezen.');
    }
}
