<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Security\Authenticator;
use Nette\Security\Identity;
use Nette\Security\AuthenticationException;
use Nette\Database\Explorer;
use Nette\Security\Passwords;

final class AuthFacade implements Authenticator
{
	public function __construct(
		private Explorer $database,
		private Passwords $passwords,
	) {}

	public function authenticate(string $username, string $password): Identity
{
    $userRow = $this->database
        ->table('users')
        ->where('email', $username)
        ->fetch();

    if (!$userRow) {
        throw new AuthenticationException('Uživatel nenalezen.');
    }

    if (!$this->passwords->verify($password, $userRow->password)) {
        throw new AuthenticationException('Nesprávné heslo.');
    }

    return new Identity($userRow->id, $userRow->role ?? 'user', [
        'username' => $userRow->username,
        'email' => $userRow->email,
        'role' => $userRow->role, 
    ]);
}


	public function register(string $email, string $password): void
	{
		$exists = $this->database->table('users')->where('email', $email)->fetch();
		if ($exists) {
			throw new \Exception('E-mail už existuje.');
		}

		$this->database->table('users')->insert([
			'email' => $email,
			'username' => explode('@', $email)[0],
			'password' => $this->passwords->hash($password),
			'role' => 'user',
		]);
	}



	public function deleteUser(int $userId): void
	{

		$categoryIds = $this->database->table('category')
			->where('user_id', $userId)
			->fetchPairs('id', 'id');

		if (!empty($categoryIds)) {
			$this->database->table('hours')
				->where('category_id', array_values($categoryIds))
				->delete();
		}

		$this->database->table('category')
			->where('user_id', $userId)
			->delete();

		$this->database->table('hours')
			->where('user_id', $userId)
			->delete();
	

		$this->database->table('users')
			->where('id', $userId)
			->delete();
	}
	
	
public function updateUser(int $userId, array $data): void
{
    $this->database->table('users')->where('id', $userId)->update($data);
}


}
