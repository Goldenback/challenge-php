<?php

namespace App\src\Models;

use App\src\Core\DB;
use DateTimeImmutable;

class Comment
{
	private ?int $id = null;
	private int $postId;
	private ?int $userId = null;
	private string $content;
	private string $status = 'pending';
	private DateTimeImmutable $createdAt;
	private ?DateTimeImmutable $updatedAt = null;
	private ?DateTimeImmutable $deletedAt = null;

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

			$method = str_replace('_', '', ucwords($method, '_'));

			if (method_exists($this, $method)) {
				if (in_array($key, ['created_at', 'updated_at', 'deleted_at']) && $value !== null) {
					try {
						$value = new DateTimeImmutable($value);
					} catch (\Exception $e) {
						$value = null;
					}
				}

				$this->$method($value);
			}
		}
	}

	// Getters and setters for all properties...

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getPostId(): int
	{
		return $this->postId;
	}

	public function setPostId(int $postId): void
	{
		$this->postId = $postId;
	}

	public function getUserId(): ?int
	{
		return $this->userId;
	}

	public function setUserId(?int $userId): void
	{
		$this->userId = $userId;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function setContent(string $content): void
	{
		$this->content = $content;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setStatus(string $status): void
	{
		$this->status = $status;
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

	public function save(): bool
	{
		$data = [
			'post_id' => $this->postId,
			'user_id' => $this->userId,
			'content' => $this->content,
			'status' => $this->status,
			'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
			'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
			'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
		];

		return $this->db->insert('comments', $data);
	}

	public function update(): bool
	{
		$criteria = ['id' => $this->id];
		$data = [
			'content' => $this->content,
			'status' => $this->status,
			'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
		];

		return $this->db->update('comments', $criteria, $data);
	}

	public function approve(): bool
	{
		$this->status = 'approved';

		return $this->update();
	}

	public function delete(): bool
	{
		$this->deletedAt = new DateTimeImmutable();

		return $this->update();
	}

	public function erase(): bool
	{
		$criteria = ['id' => $this->id];

		return $this->db->delete('comments', $criteria);
	}

	public function getPost(): ?Post
	{
		$postData = $this->db->getOneBy('posts', ['id' => $this->postId]);

		if ($postData) {
			return new Post($this->db, $postData);
		}

		return null;
	}
}
