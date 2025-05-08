<?php

namespace App\Presentation\Calculator;

use App\Model\CalculatorFacade;
use Nette\Application\UI\Presenter;

class CalculatorPresenter extends Presenter
{
    public function __construct(
        private CalculatorFacade $calculatorFacade
    ) {}

    public function renderDefault(): void
    {
        $hours = $this->calculatorFacade->getUserHours();
        $categories = $this->calculatorFacade->getCategoriesByUser($this->user->getId());
    
        $this->template->categories = $categories ?? [];
        $this->template->records = [];
        $this->template->totalHours = 0;
        $this->template->averagePerDay = 0;
        $this->template->busiestDay = 'Neuvedeno';
        $this->template->monthlyData = [];
        $this->template->chartLabelsJson = '[]';
        $this->template->chartDataJson = '[]';
        $this->template->chartColorsJson = '[]';
    
        if (empty($hours)) {
            $this->template->noRecordsMessage = "Žádné hodiny nebyly zaznamenány.";
            return;
        }
    
        $totalMinutes = 0;
        $minutesPerDay = [];
        $categoryHours = [];
        $categoryColors = [];
    
        foreach ($hours as $row) {
            $totalMinutes += $row->duration;
            $day = $row->date->format('Y-m-d');
            $categoryName = $row->ref('category')->name ?? 'Neznámá';
            $categoryColor = $row->ref('category')->color ?? '#FFFFFF';
    
            $categoryHours[$categoryName] = ($categoryHours[$categoryName] ?? 0) + $row->duration;
            $categoryColors[$categoryName] = $categoryColor;
            $minutesPerDay[$day] = ($minutesPerDay[$day] ?? 0) + $row->duration;
        }
    
        $chartLabels = array_keys($categoryHours);
        $chartData = array_map(fn($min) => round($min / 60, 2), array_values($categoryHours));
        $chartColors = array_values($categoryColors);
    
        $this->template->records = $this->getRecordTable($hours);
        $this->template->totalHours = round($totalMinutes / 60, 1);
        $this->template->averagePerDay = round($totalMinutes / count($minutesPerDay) / 60, 1);
        $this->template->busiestDay = array_search(max($minutesPerDay), $minutesPerDay);
        $this->template->monthlyData = $this->generateMonthlyData($hours);
        $this->template->chartLabelsJson = json_encode($chartLabels);
        $this->template->chartDataJson = json_encode($chartData);
        $this->template->chartColorsJson = json_encode($chartColors);
    }
    

    private function generateMonthlyData(array $hours): array
    {
        $monthlyData = [];
        foreach ($hours as $row) {
            $month = $row->date->format('Y-m');
            $monthlyData[$month] = ($monthlyData[$month] ?? 0) + round($row->duration / 60, 1);
        }
        return $monthlyData;
    }

    private function getRecordTable(array $hours): array
    {
        $records = [];
    
        foreach ($hours as $row) {
            $category = $row->ref('category');
            $categoryName = $category->name ?? 'Neznámá';
            $categoryColor = $category->color ?? '#FFFFFF';
    
            $date = $row->date;
    
            if (!$date instanceof \DateTime) {
                try {
                    $date = new \DateTime($date); 
                } catch (\Exception $e) {
                    $date = null; 
                }
            }
    
            if ($date instanceof \DateTime) {
                $records[] = [
                    'date' => $date,
                    'category' => $categoryName,
                    'categoryColor' => $categoryColor,
                    'hours' => round($row->duration / 60, 1),
                ];
            }
        }

        usort($records, function ($a, $b) {
            return $b['date'] <=> $a['date']; 
        });
    
        $records = array_slice($records, 0, 10);
    
        return $records;
    }
    
    public function renderList(): void
    {
        $hours = $this->calculatorFacade->getLastUserHours(); 
        $this->template->hours = array_map(function ($hour) {
            return [
                'id' => $hour->id,
                'note' => $hour->note,
                'date' => $hour->date->format('Y-m-d'),
                'category' => $hour->ref('category')->name ?? 'Neznámá',
                'duration' => round($hour->duration / 60, 2),
            ];
        }, $hours);
    }
    

public function renderDetail(int $id): void
{
    $hour = $this->calculatorFacade->getHourById($id);
    if (!$hour) {
        $this->error('Záznam nenalezen.');
    }

    $durationInMinutes = $hour->duration;

    if ($durationInMinutes < 60) {
        $formattedDuration = $durationInMinutes . ' min';
    } else {
        $hours = floor($durationInMinutes / 60);
        $minutes = $durationInMinutes % 60;

        if ($minutes === 0) {
            $formattedDuration = $hours . ' h';
        } else {
            $formattedDuration = $hours . ' h ' . $minutes . ' min';
        }
    }

    $this->template->hour = [
        'id' => $hour->id,
        'date' => $hour->date->format('Y-m-d'),
        'category' => $hour->ref('category')->name ?? 'Neznámá',
        'duration' => $formattedDuration,
        'note' => $hour->note,
        'description' => $hour->description,
    ];
    }


    protected function createComponentAddHourForm(): \Nette\Application\UI\Form
    {
        $form = new \Nette\Application\UI\Form;

        $categories = $this->calculatorFacade->getCategoriesByUser($this->user->getId());
        $categoryOptions = [];
        foreach ($categories as $category) {
            $categoryOptions[$category->id] = $category->name;
        }

        $form->addSelect('category_id', 'Kategorie:')
            ->setItems($categoryOptions)
            ->setPrompt('Vyberte kategorii')
            ->setRequired();

        $form->addText('date', 'Datum:')
            ->setDefaultValue((new \DateTime())->format('Y-m-d'))
            ->setHtmlType('date')
            ->setRequired();

        $form->addText('from_time', 'Od:')
            ->setHtmlType('time');

        $form->addText('to_time', 'Do:')
            ->setHtmlType('time');

        $form->addInteger('duration', 'Délka (v minutách):')
            ->setNullable()
            ->addConditionOn($form['from_time'], $form::FILLED)
            ->addRule($form::MAX_LENGTH, 'Zadejte dobu nebo časové rozmezí', 4);

        $form->addTextArea('note', 'Poznámka:');
        $form->addTextArea('description', 'Popis:');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = [$this, 'addHourFormSucceeded'];

        return $form;
    }

    public function addHourFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values): void
    {
        $hasDuration = !empty($values->duration);
        $hasFromAndTo = !empty($values->from_time) && !empty($values->to_time);

        if ($hasDuration && $hasFromAndTo) {
            $form->addError('Vyplňte buď délku (Duration), nebo časy Od a Do — ne obojí zároveň.');
            return;
        }

        if (!$hasDuration && !$hasFromAndTo) {
            $form->addError('Musíte zadat buď délku (Duration), nebo časy Od a Do.');
            return;
        }

        if (!$hasDuration && $hasFromAndTo) {
            try {
                $from = new \DateTimeImmutable($values->from_time);
                $to = new \DateTimeImmutable($values->to_time);
                $diff = $to->getTimestamp() - $from->getTimestamp();

                if ($diff > 0) {
                    $values->duration = (int) round($diff / 60);
                } else {
                    $form->addError('Čas "Do" musí být později než "Od".');
                    return;
                }
            } catch (\Exception $e) {
                $form->addError('Chyba při výpočtu délky. Zkontrolujte zadané časy.');
                return;
            }
        }

        $this->calculatorFacade->addHourRecord($this->user->getId(), $values);
        $this->flashMessage('Záznam byl úspěšně přidán.', 'success');
        $this->redirect('default');
    }

    public function handleDelete(int $id): void
    {
        $this->calculatorFacade->deleteHour($id);
        $this->flashMessage('Záznam byl smazán.', 'success');
        $this->redirect('this');
    }

    public function handleDeleteCategory(int $id): void
    {
        $this->calculatorFacade->deleteCategory($id);
        $this->flashMessage('kategorie byla smazán.', 'success');
        $this->redirect('this');
    }
    public function renderCategoryList(): void
    {
        $categories = $this->calculatorFacade->getCategoriesByUser($this->user->getId());
        $this->template->categories = $categories;
    }
    





    //category

protected function createComponentAddCategoryForm(): \Nette\Application\UI\Form
{
    $form = new \Nette\Application\UI\Form;

    $form->addText('name', 'Název kategorie:')
        ->setRequired('Zadejte název kategorie.');

    $form->addColor('color', 'Barva kategorie:')
        ->setRequired('Zvolte barvu kategorie.');

    $form->addSubmit('send', 'Uložit');

    $form->onSuccess[] = [$this, 'addCategoryFormSucceeded'];

    return $form;
}

public function addCategoryFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values): void
{
    try {
        $this->calculatorFacade->addCategory($this->user->getId(), $values->name, $values->color);
        $this->flashMessage('Kategorie byla úspěšně přidána.', 'success');
    } catch (\Exception $e) {
        $this->flashMessage('Došlo k chybě při přidávání kategorie.', 'error');
    }

    $this->redirect('this');
}


}
