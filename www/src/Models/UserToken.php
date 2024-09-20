<?php

namespace App\src\Models;

use App\src\Core\DB;
use DateTimeImmutable;

class UserToken
{
	private ?int $id = null;
	private int $userId;
	private string $token;
	private string $tokenType; // e.g., 'activation', 'password_reset'
	private DateTimeImmutable $expiresAt;
	private DateTimeImmutable $createdAt;

	private DB $db;

	public function __construct(DB $db, array $data = [])
	{
		$this->db = $db;
		$this->createdAt = new DateTimeImmutable();

		if (!empty($data)) {
			$this->hydrate($data);
		}
	}

	public function hydrate(array $data): void
	{
		foreach ($data as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getUserId(): int
	{
		return $this->userId;
	}

	public function setUserId(int $userId): void
	{
		$this->userId = $userId;
	}

	public function getToken(): string
	{
		return $this->token;
	}

	public function setToken(string $token): void
	{
		$this->token = $token;
	}

	public function getTokenType(): string
	{
		return $this->tokenType;
	}

	public function setTokenType(string $tokenType): void
	{
		$this->tokenType = $tokenType;
	}

	public function getExpiresAt(): DateTimeImmutable
	{
		return $this->expiresAt;
	}

	public function setExpiresAt(DateTimeImmutable $expiresAt): void
	{
		$this->expiresAt = $expiresAt;
	}

	public function getCreatedAt(): DateTimeImmutable
	{
		return $this->createdAt;
	}

	public function setCreatedAt(DateTimeImmutable $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function save(): bool
	{
		$data = [
			'user_id' => $this->userId,
			'token' => $this->token,
			'token_type' => $this->tokenType,
			'expires_at' => $this->expiresAt->format('Y-m-d H:i:s'),
			'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
		];

		return $this->db->insert('user_tokens', $data);
	}

	public function update(): bool
	{
		$criteria = ['id' => $this->id];
		$data = [
			'user_id' => $this->userId,
			'token' => $this->token,
			'token_type' => $this->tokenType,
			'expires_at' => $this->expiresAt->format('Y-m-d H:i:s'),
		];

		return $this->db->update('user_tokens', $criteria, $data);
	}

	// Suppression du token (pas de soft delete ici, on supprime vraiment)
	public function delete(): bool
	{
		$criteria = ['id' => $this->id];
		return $this->db->delete('user_tokens', $criteria);
	}
}
