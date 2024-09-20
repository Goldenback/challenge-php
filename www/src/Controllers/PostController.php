<?php

namespace App\src\Controllers;

use App\src\Core\DB;
use App\src\Models\Page;
use App\src\Models\Post;
use DateTimeImmutable;

class PostController
{
	private ?DB $db;

	public function __construct()
	{
		$this->db = DB::getInstance();
	}

	// Création d'un nouvel article
	public function create(): void
	{
		requireRole('admin');
		$pageId = $_GET['id'] ?? null;

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = [
				'title' => $_POST['title'],
				'slug' => $_POST['slug'],
				'content' => $_POST['content'],
				'is_published' => $_POST['is_published'] ?? false,
			];

			$post = new Post($this->db);
			$post->setPageId($pageId);
			$post->setTitle($data['title']);
			$post->setSlug($data['slug']);
			$post->setContent($data['content']);
			$post->setIsPublished($data['is_published']);
			$post->setCreatedAt(new DateTimeImmutable());

			if ($post->save()) {
				$_SESSION['flash_message'] = 'Article créé avec succès.';
				header('Location: /admin/posts?id=' . $pageId);
			} else {
				$_SESSION['flash_message'] = 'Erreur lors de la création de l\'article.';
				header('Location: /admin/posts/create');
			}
		} else {
			require_once __DIR__ . '/../Views/admin/posts/create.php';
		}
	}

	// Liste des articles dans le panneau d'administration
	public function index(): void
	{
		requireRole('admin'); // Vérifie que l'utilisateur est administrateur

		$pageRequest = $this->db->getOneBy('pages', ['id' => $_GET['id']]);
		$page = new Page($this->db, $pageRequest);

		$stmt = $this->db->getConnection()->prepare("SELECT * FROM posts WHERE deleted_at IS NULL AND page_id = " . $page->getId());
		$stmt->execute();
		$posts = $stmt->fetchAll();

		require_once __DIR__ . '/../Views/admin/posts/index.php';
	}

	// Afficher un article spécifique
	public function show(): void
	{
		$slug = $_GET['slug'] ?? null;

		if ($slug) {
			$postData = $this->db->getOneBy('posts', ['slug' => $slug, 'is_published' => true]);

			if ($postData) {
				$post = new Post($this->db, $postData);

				require_once __DIR__ . '/../Views/posts/show.php';
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
				header('Location: /404');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
			header('Location: /404');
		}
	}

	// Modification d'un article existant
	public function edit(): void
	{
		requireRole('admin');

		$id = $_GET['id'] ?? null;

		if ($id) {
			$postData = $this->db->getOneBy('posts', ['id' => $id]);

			if ($postData) {
				$post = new Post($this->db, $postData);

				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$data = [
						'title' => $_POST['title'],
						'slug' => $_POST['slug'],
						'content' => $_POST['content'],
						'is_published' => $_POST['is_published'] ?? false,
					];

					$post->setTitle($data['title']);
					$post->setSlug($data['slug']);
					$post->setContent($data['content']);
					$post->setIsPublished($data['is_published']);

					if ($post->update()) {
						$_SESSION['flash_message'] = 'Article modifié avec succès.';
						header('Location: /admin/posts');
					} else {
						$_SESSION['flash_message'] = 'Erreur lors de la modification de l\'article.';
						header('Location: /admin/posts/edit?id=' . $id);
					}
				} else {
					require_once __DIR__ . '/../Views/admin/posts/edit.php';
				}
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
				header('Location: /admin/posts');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
			header('Location: /admin/posts');
		}
	}

	// Suppression d'un article
	public function delete(): void
	{
		requireRole('admin');

		$id = $_GET['id'] ?? null;

		if ($id) {
			$postData = $this->db->getOneBy('posts', ['id' => $id]);

			if ($postData) {
				$post = new Post($this->db, $postData);

				if ($post->delete()) {
					$_SESSION['flash_message'] = 'Article supprimé avec succès.';
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de la suppression de l\'article.';
				}
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
		}
		header('Location: /admin/posts');
	}
}