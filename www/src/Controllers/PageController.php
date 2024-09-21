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

	///// ADMIN /////
	// Liste des pages dans le panneau d'administration
	public function index(): void
	{
		requireRole('admin');

		// Fetch all pages from the database
		$pagesData = $this->db->getAll('pages');

		// Convert each page data to a Page object
		$pages = array_map(function ($pageData) {
			return new Page($this->db, $pageData);
		}, $pagesData);

		// Pass the Page objects to the view
		require_once __DIR__ . '/../Views/admin/pages/index.php';
	}

	// Création d'une nouvelle page
	public function create(): void
	{
		requireRole('admin');

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = [
				'title' => $_POST['title'],
				'content' => $_POST['content'],
				'is_published' => isset($_POST['is_published']),
				'is_home' => isset($_POST['is_home'])
			];

			if ($this->db->getOneBy('pages', ['is_home' => true]) && $data['is_home']) {
				$_SESSION['flash_message'] = 'Une page d\'accueil existe déjà.';
				header('Location: /admin/page/create');
				exit;
			}

			if ($data['title']) {
				$page = new Page($this->db);
				$page->setTitle($data['title']);
				$page->setSlug(slugify($data['title']));
				$page->setContent($data['content']);
				$page->setIsPublished($data['is_published']);
				$page->setIsHome($data['is_home']);
				$page->setCreatedAt(new DateTimeImmutable());

				if ($page->save()) {
					$_SESSION['flash_message'] = 'Page créée avec succès.';
					header('Location: /admin/pages');
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de la création de la page.';
					header('Location: /admin/page/create');
				}
			} else {
				$_SESSION['flash_message'] = 'Tous les champs sont requis.';
				header('Location: /admin/page/create');
			}
		} else {
			require_once __DIR__ . '/../Views/admin/pages/create.php';
		}
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
					$data = [
						'title' => $_POST['title'],
						'content' => $_POST['content'],
						'is_published' => isset($_POST['is_published']),
						'is_home' => isset($_POST['is_home'])
					];

					$existingHome = $this->db->getOneBy('pages', ['is_home' => true]);
					if ($existingHome && $data['is_home'] && $existingHome['id'] != $id) {
						$_SESSION['flash_message'] = 'Une page d\'accueil existe déjà.';
						header('Location: /admin/page/edit?id=' . $id);
						exit;
					}

					if ($data['title']) {
						$page->setTitle($data['title']);
						$page->setSlug(slugify($data['title']));
						$page->setContent($data['content']);
						$page->setIsPublished($data['is_published']);
						$page->setIsHome($data['is_home']);
						$page->setUpdatedAt(new DateTimeImmutable());

						if ($page->update()) {
							$_SESSION['flash_message'] = 'Page mise à jour avec succès.';
							header('Location: /admin/pages');
						} else {
							$_SESSION['flash_message'] = 'Erreur lors de la mise à jour de la page.';
							header('Location: /admin/page/edit?id=' . $id);
						}
						exit;
					} else {
						$_SESSION['flash_message'] = 'Tous les champs sont requis.';
						header('Location: /admin/page/edit?id=' . $id);
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

		$id = $_POST['id'] ?? null;

		if ($id) {
			$pageData = $this->db->getOneBy('pages', ['id' => $id]);

			if ($pageData) {
				$page = new Page($this->db, $pageData);

				if ($page->erase()) {
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
	///// ADMIN /////
}