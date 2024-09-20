<?php

namespace App\src\Controllers;

use App\src\Core\DB;
use App\src\Models\Page;
use DateTimeImmutable;

class PageController
{
	private ?DB $db;

	public function __construct()
	{
		$this->db = DB::getInstance();
	}

	// Création d'une nouvelle page
	public function create(): void
	{
		requireRole('admin');

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			$title = $_POST['title'] ?? null;
			$slug = $_POST['slug'] ?? null;
			$content = $_POST['content'] ?? null;
			$isPublished = isset($_POST['is_published']);

			if ($title && $slug && $content) {
				if ($this->db->getOneBy('pages', ['slug' => $slug])) {
					$_SESSION['flash_message'] = 'Un slug identique existe déjà.';
					header('Location: /admin/pages/create');
					exit;
				}

				$page = new Page($this->db);
				$page->setTitle($title);
				$page->setSlug($slug);
				$page->setContent($content);
				$page->setIsPublished($isPublished);

				if ($page->save()) {
					$_SESSION['flash_message'] = 'Page créée avec succès.';
					header('Location: /admin/pages');
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de la création de la page.';
					header('Location: /admin/pages/create');
				}
			} else {
				$_SESSION['flash_message'] = 'Tous les champs sont requis.';
				header('Location: /admin/pages/create');
			}
		} else {
			require_once __DIR__ . '/../Views/admin/pages/create.php';
		}
	}

	// Afficher une page spécifique
	public function show(): void
	{
		$slug = $_GET['slug'] ?? null;

		if ($slug) {
			$pageData = $this->db->getOneBy('pages', ['slug' => $slug, 'is_published' => true]);

			if ($pageData) {
				$page = new Page($this->db, $pageData);

				$posts = $page->getPosts();

				require_once __DIR__ . '/../Views/pages/show.php';
			} else {
				$_SESSION['flash_message'] = 'Page non trouvée.';
				header('Location: /404');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de page fourni.';
			header('Location: /404');
		}
	}

	// Liste des pages dans le panneau d'administration
	public function index(): void
	{
		requireRole('admin'); // Vérifie que l'utilisateur est administrateur

		$stmt = $this->db->getConnection()->prepare("SELECT * FROM pages WHERE deleted_at IS NULL");
		$stmt->execute();
		$pages = $stmt->fetchAll();

		require_once __DIR__ . '/../Views/admin/pages/index.php';
	}

	// Afficher une page spécifique en admin
	public function admin_show(): void
	{
		requireRole('admin');

		$id = $_GET['id'] ?? null;

		if ($id) {
			$pageData = $this->db->getOneBy('pages', ['id' => $id]);

			if ($pageData) {
				$page = new Page($this->db, $pageData);

				$posts = $page->getPosts();

				require_once __DIR__ . '/../Views/admin/pages/show.php';
			} else {
				$_SESSION['flash_message'] = 'Page non trouvée.';
				header('Location: /admin/pages');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de page fourni.';
			header('Location: /admin/pages');
		}
	}

	// Édition d'une page existante
	public function edit(): void
	{
		requireRole('admin');

		$id = $_GET['id'] ?? null;

		if ($id) {
			$pageData = $this->db->getOneBy('pages', ['id' => $id]);

			if ($pageData) {
				$page = new Page($this->db, $pageData);

				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$title = $_POST['title'] ?? null;
					$slug = $_POST['slug'] ?? null;
					$content = $_POST['content'] ?? null;
					$isPublished = isset($_POST['is_published']);

					if ($title && $slug && $content) {
						$page->setTitle($title);
						$page->setSlug($slug);
						$page->setContent($content);
						$page->setIsPublished($isPublished);
						$page->setUpdatedAt(new DateTimeImmutable());

						if ($page->update()) {
							$_SESSION['flash_message'] = 'Page mise à jour avec succès.';
							header('Location: /admin/pages');
						} else {
							$_SESSION['flash_message'] = 'Erreur lors de la mise à jour de la page.';
							header('Location: /admin/pages/edit?id=' . $id);
						}
					} else {
						$_SESSION['flash_message'] = 'Tous les champs sont requis.';
						header('Location: /admin/pages/edit?id=' . $id);
					}
				} else {
					require_once __DIR__ . '/../Views/admin/pages/edit.php';
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

	// Suppression d'une page
	public function delete(): void
	{
		requireRole('admin');

		$id = $_GET['id'] ?? null;

		if ($id) {
			$pageData = $this->db->getOneBy('pages', ['id' => $id]);

			if ($pageData) {
				$page = new Page($this->db, $pageData);

				if ($page->delete()) {
					$_SESSION['flash_message'] = 'Page supprimée avec succès.';
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de la suppression de la page.';
				}
			} else {
				$_SESSION['flash_message'] = 'Page non trouvée.';
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de page fourni.';
		}
		header('Location: /admin/pages');
	}
}