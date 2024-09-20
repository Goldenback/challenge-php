<?php

namespace App\src\Models;

use App\src\Core\DB;
use DateTimeImmutable;

class User
{
	private ?int $id = null;
	private string $username;
	private string $email;
	private string $passwordHash;
	private string $role = 'user';
	private bool $isActive = false;
	private DateTimeImmutable $createdAt;
	private ?DateTimeImmutable $updatedAt = null;
	private ?DateTimeImmutable $deletedAt = null;

	// Connexion à la base de données
	private DB $db;

	public function __construct(DB $db, array $data = [])
	{
		$this->db = $db;
		$this->createdAt = new DateTimeImmutable();

		if (!empty($data)) {
			$this->hydrate($data);
		}
	}

	// Hydratation de l'objet User avec les données fournies
	public function hydrate(array $data): void
	{
		foreach ($data as $key => $value) {
			$method = 'set' . ucfirst($key);

			$method = str_replace('_', '', ucwords($method, '_'));

			if (method_exists($this, $method)) {
				// Vérifier si le champ est une date et convertir en DateTimeImmutable
				if (in_array($key, ['created_at', 'updated_at', 'deleted_at']) && $value !== null) {
					try {
						$value = new \DateTimeImmutable($value);
					} catch (\Exception $e) {
						// Gérer l'exception si la date n'est pas valide
						$value = null;
					}
				}

				$this->$method($value);
			}
		}
	}

	// Getters et Setters

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function setPasswordHash(string $passwordHash): void
	{
		// Utilisation d'un hash sécurisé (par exemple, bcrypt)
		$this->passwordHash = password_hash($passwordHash, PASSWORD_BCRYPT);
	}

	public function getRole(): string
	{
		return $this->role;
	}

	public function setRole(string $role): void
	{
		$this->role = $role;
	}

	public function isActive(): bool
	{
		return $this->isActive;
	}

	public function setIsActive(bool $isActive): void
	{
		$this->isActive = $isActive;
	}

	public function getCreatedAt(): DateTimeImmutable
	{
		return $this->createdAt;
	}

	public function setCreatedAt(DateTimeImmutable $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt(): ?DateTimeImmutable
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}

	public function getDeletedAt(): ?DateTimeImmutable
	{
		return $this->deletedAt;
	}

	public function setDeletedAt(?DateTimeImmutable $deletedAt): void
	{
		$this->deletedAt = $deletedAt;
	}

	// Persistance des données

	public function save(): bool
	{
		$data = [
			'username' => $this->username,
			'email' => $this->email,
			'password_hash' => $this->passwordHash,
			'role' => $this->role,
			'is_active' => $this->isActive ? 1 : 0,
			'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
			'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
			'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
		];

		return $this->db->insert('users', $data);
	}

	// Méthode de mise à jour
	public function update(): bool
	{
		$criteria = ['id' => $this->id];
		$data = [
			'username' => $this->username,
			'email' => $this->email,
			'role' => $this->role,
			'is_active' => $this->isActive ? 1 : 0,
			'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
		];

		if (!empty($this->passwordHash)) {
			$data['password_hash'] = $this->passwordHash;
		}

		return $this->db->update('users', $criteria, $data);
	}

	// Suppression logique (soft delete)
	public function delete(): bool
	{
		$criteria = ['id' => $this->id];
		$data = [
			'deleted_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
		];

		return $this->db->update('users', $criteria, $data);
	}

	// Méthode de suppression définitive
	public function erase(): bool
	{
		$criteria = ['id' => $this->id];

		return $this->db->delete('users', $criteria);
	}
}
