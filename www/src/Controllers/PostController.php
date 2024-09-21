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

	///// ADMIN /////
	// Liste des articles dans le panneau d'administration
	public function index(): void
	{
		requireRole('admin');

		$id = $_GET['id'] ?? null;

		if ($id) {
			$pageData = $this->db->getOneBy('pages', ['id' => $id]);

			if ($pageData) {
				$page = new Page($this->db, $pageData);
				$posts = $page->getPosts();

				require_once __DIR__ . '/../Views/admin/posts/index.php';
			} else {
				$_SESSION['flash_message'] = 'Page non trouvée.';
				header('Location: /admin/pages');
			}
			exit;
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de page fourni.';
			header('Location: /admin/pages');
		}
	}

	// Création d'un nouvel article
	public function create(): void
	{
		requireRole('admin');

		$pageId = $_GET['id'] ?? null;

		if ($pageId) {
			$pageData = $this->db->getOneBy('pages', ['id' => $pageId]);

			if ($pageData) {
				$page = new Page($this->db, $pageData);

				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$data = [
						'title' => $_POST['title'],
						'content' => $_POST['content'],
						'is_published' => $_POST['is_published'] ?? false,
					];

					if ($data['title']) {
						$post = new Post($this->db);
						$post->setPageId($pageId);
						$post->setTitle($data['title']);
						$post->setSlug(slugify($data['title']));
						$post->setContent($data['content']);
						$post->setIsPublished($data['is_published']);
						$post->setCreatedAt(new DateTimeImmutable());

						if ($post->save()) {
							$_SESSION['flash_message'] = 'Article créé avec succès.';
							header('Location: /admin/posts?id=' . $pageId);
						} else {
							$_SESSION['flash_message'] = 'Erreur lors de la création de l\'article.';
							header('Location: /admin/posts/create?id=' . $pageId);
						}
						exit;
					}
				} else {
					require_once __DIR__ . '/../Views/admin/posts/create.php';
				}
			} else {
				$_SESSION['flash_message'] = 'Page non trouvée.';
				header('Location: /admin/pages');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de page fourni.';
			header('Location: /admin/pages');
		}
	}

	// Afficher une page spécifique en admin
	public function admin_show(): void
	{
		requireRole('admin');

		$id = $_GET['id'] ?? null;

		if ($id) {
			$postData = $this->db->getOneBy('posts', ['id' => $id]);

			if ($postData) {
				$post = new Post($this->db, $postData);

				require_once __DIR__ . '/../Views/admin/posts/show.php';
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
				header('Location: /admin/posts');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
			header('Location: /admin/posts');
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
						'content' => $_POST['content'],
						'is_published' => $_POST['is_published'] ?? false,
					];

					$post->setTitle($data['title']);
					$post->setSlug(slugify($data['title']));
					$post->setContent($data['content']);
					$post->setIsPublished($data['is_published']);
					$post->setUpdatedAt(new DateTimeImmutable());

					if ($post->update()) {
						$_SESSION['flash_message'] = 'Article modifié avec succès.';
						header('Location: /admin/posts?id=' . $post->getPageId());
					} else {
						$_SESSION['flash_message'] = 'Erreur lors de la modification de l\'article.';
						header('Location: /admin/post/edit?id=' . $id);
					}
					exit;
				} else {
					require_once __DIR__ . '/../Views/admin/posts/edit.php';
				}
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
				header('Location: /admin/posts');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
			header('Location: /admin/posts/');
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
				$pageId = $post->getPageId();

				if ($post->erase()) {
					$_SESSION['flash_message'] = 'Article supprimé avec succès.';
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de la suppression de l\'article.';
				}
				header('Location: /admin/posts?id=' . $pageId);
				exit;
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
		}
		header('Location: /admin/posts');
	}
	///// ADMIN /////

}