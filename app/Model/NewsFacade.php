<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Utils\DateTime;

final class NewsFacade
{
    public function __construct(
        private Explorer $database
    ) {}

    public function addNews(string $title, string $content): void
    {
        $this->database->table('news')->insert([
            'title' => $title,
            'content' => $content,
            'created_at' => new DateTime(),
        ]);
    }

    public function updateNews(int $id, string $title, string $content): void
    {
        $this->database->table('news')->where('id', $id)->update([
            'title' => $title,
            'content' => $content,
        ]);
    }

    public function deleteNews(int $id): void
    {
        $this->database->table('news')->where('id', $id)->delete();
    }

    public function getNewsById(int $id): ?\Nette\Database\Table\ActiveRow
    {
        return $this->database->table('news')->get($id);
    }

    public function getAllNews(): \Nette\Database\Table\Selection
    {
        return $this->database->table('news')->order('created_at DESC');
    }
}
