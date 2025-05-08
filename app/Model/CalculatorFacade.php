<?php
namespace App\Model;

use Nette\Database\Explorer;

class CalculatorFacade
{
    public function __construct(
        private Explorer $database,
        private \Nette\Security\User $user
    ) {}

    public function getUserHours(): array
    {
        return $this->database->table('hours')
            ->select('hours.*, category.name, category.color')
            ->where('hours.user_id', $this->user->getId())
            ->order('date ASC')
            ->fetchAll();
    }
    

public function getCategoriesByUser(?int $userId = null): array
{
    $query = $this->database->table('category')
        ->select('id, name, color'); 

    if ($userId !== null) {
        $query->where('user_id', $userId);
    }

    return $query->fetchAll();
}


public function getLastUserHours(): array
{
    return $this->database->table('hours')
        ->where('user_id', $this->user->getId())
        ->order('date DESC')
        ->fetchAll();
}


public function getHourById(int $id): ?\Nette\Database\Table\ActiveRow
{
    return $this->database->table('hours')
        ->where('id', $id)
        ->where('user_id', $this->user->getId())
        ->fetch();
}

public function addHourRecord(int $userId, \stdClass $values): void
{
    $this->database->table('hours')->insert([
        'user_id' => $userId,
        'category_id' => $values->category_id,
        'date' => $values->date,
        'from_time' => $values->from_time ?: null,
        'to_time' => $values->to_time ?: null,
        'duration' => $values->duration ?: null,
        'note' => $values->note,
        'description' => $values->description,
    ]);
}

public function deleteHour(int $id): void
{
    $this->database->table('hours')
        ->where('id', $id)
        ->where('user_id', $this->user->getId())
        ->delete();
}

public function deleteCategory(int $id): void
{
    $this->database->table('hours')
        ->where('category_id', $id)
        ->update(['category_id' => null]); 

    $this->database->table('category')
        ->where('id', $id)
        ->where('user_id', $this->user->getId())
        ->delete();
}



public function addCategory(int $userId, string $name, string $color): void
{
    $this->database->table('category')->insert([
        'user_id' => $userId,
        'name' => $name,
        'color' => $color,
    ]);
}

}
