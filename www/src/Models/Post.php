<?php

namespace App\src\Models;

use App\src\Core\DB;
use DateTimeImmutable;

class Post
{
	private ?int $id = null;
	private int $pageId;
	private string $title;
	private string $slug;
	private string $content;
	private bool $isPublished = false;
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
						$value = new \DateTimeImmutable($value);
					} catch (\Exception $e) {
						$value = null;
					}
				}

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

	public function getPageId(): int
	{
		return $this->pageId;
	}

	public function setPageId(int $pageId): void
	{
		$this->pageId = $pageId;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getSlug(): string
	{
		return $this->slug;
	}

	public function setSlug(string $slug): void
	{
		$this->slug = $slug;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function setContent(string $content): void
	{
		$this->content = $content;
	}

	public function isPublished(): bool
	{
		return $this->isPublished;
	}

	public function setIsPublished(bool $isPublished): void
	{
		$this->isPublished = $isPublished;
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
			'page_id' => $this->pageId,
			'title' => $this->title,
			'slug' => $this->slug,
			'content' => $this->content,
			'is_published' => $this->isPublished ? 1 : 0,
			'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
			'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
			'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
		];

		return $this->db->insert('posts', $data);
	}

	public function update(): bool
	{
		$criteria = ['id' => $this->id];
		$data = [
			'title' => $this->title,
			'slug' => $this->slug,
			'content' => $this->content,
			'is_published' => $this->isPublished ? 1 : 0,
			'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
		];

		return $this->db->update('posts', $criteria, $data);
	}

	public function delete(): bool
	{
		$criteria = ['id' => $this->id];
		$data = [
			'deleted_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
		];

		return $this->db->update('posts', $criteria, $data);
	}

	public function erase(): bool
	{
		$criteria = ['id' => $this->id];

		return $this->db->delete('posts', $criteria);
	}

	public function getPage(): ?Page
	{
		$pageData = $this->db->getOneBy('pages', ['id' => $this->pageId, 'deleted_at' => null]);

		if ($pageData) {
			return new Page($this->db, $pageData);
		}

		return null;
	}
}