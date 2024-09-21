<?php

namespace App\src\Controllers;

use App\src\Core\DB;
use App\src\Models\Comment;
use App\src\Models\Post;
use DateTimeImmutable;

class CommentController
{
	private ?DB $db;

	public function __construct()
	{
		$this->db = DB::getInstance();
	}

	// Afficher les commentaires d'un article spécifique
	public function show(): void
	{
		$postId = $_GET['post_id'] ?? null;

		if ($postId) {
			$postData = $this->db->getOneBy('posts', ['id' => $postId]);

			if ($postData) {
				$post = new Post($this->db, $postData);
				$comments = $this->db->getAllBy('comments', ['post_id' => $postId]);

				require_once __DIR__ . '/../Views/comment/show.php';
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
				header('Location: /404');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
			header('Location: /404');
		}
	}

	// Ajouter un commentaire
	public function create(): void
	{
		$postId = $_GET['post_id'] ?? null;

		if ($postId) {
			$postData = $this->db->getOneBy('posts', ['id' => $postId]);

			if ($postData) {
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$data = [
						'content' => $_POST['content'],
					];

					if ($data['content']) {
						$comment = new Comment($this->db);
						$comment->setPostId($postId);
						$comment->setUserId($_SESSION['user']['id'] ?? null); // Assumed that the user is logged in
						$comment->setContent($data['content']);
						$comment->setCreatedAt(new DateTimeImmutable());

						if ($comment->save()) {
							$_SESSION['flash_message'] = 'Commentaire ajouté avec succès.';
							header('Location: /post/show?slug=' . $postData['slug']);
						} else {
							$_SESSION['flash_message'] = 'Erreur lors de l\'ajout du commentaire.';
							header('Location: /post/show?slug=' . $postData['slug']);
						}
						exit;
					}
				} else {
					require_once __DIR__ . '/../Views/comment/create.php';
				}
			} else {
				$_SESSION['flash_message'] = 'Article non trouvé.';
				header('Location: /404');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant d\'article fourni.';
			header('Location: /404');
		}
	}

	// Modifier un commentaire
	public function edit(): void
	{
		$id = $_GET['id'] ?? null;

		if ($id) {
			$commentData = $this->db->getOneBy('comments', ['id' => $id]);

			if ($commentData) {
				$comment = new Comment($this->db, $commentData);

				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$data = [
						'content' => $_POST['content'],
						'status' => $_POST['status'] ?? 'pending',
					];

					$comment->setContent($data['content']);
					$comment->setStatus($data['status']);
					$comment->setUpdatedAt(new DateTimeImmutable());

					if ($comment->update()) {
						$_SESSION['flash_message'] = 'Commentaire modifié avec succès.';
						header('Location: /post/show?slug=' . $comment->getPost()->getSlug());
					} else {
						$_SESSION['flash_message'] = 'Erreur lors de la modification du commentaire.';
						header('Location: /post/show?slug=' . $comment->getPost()->getSlug());
					}
					exit;
				} else {
					require_once __DIR__ . '/../Views/comment/edit.php';
				}
			} else {
				$_SESSION['flash_message'] = 'Commentaire non trouvé.';
				header('Location: /404');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de commentaire fourni.';
			header('Location: /404');
		}
	}

	// Approuver un commentaire
	public function approve(): void
	{
		$id = $_GET['id'] ?? null;

		if ($id) {
			$commentData = $this->db->getOneBy('comments', ['id' => $id]);

			if ($commentData) {
				$comment = new Comment($this->db, $commentData);
				$comment->setStatus('approved');
				$comment->setUpdatedAt(new DateTimeImmutable());

				if ($comment->update()) {
					$_SESSION['flash_message'] = 'Commentaire approuvé avec succès.';
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de l\'approbation du commentaire.';
				}
				header('Location: /post/show?slug=' . $comment->getPost()->getSlug());
				exit;
			} else {
				$_SESSION['flash_message'] = 'Commentaire non trouvé.';
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de commentaire fourni.';
		}
		header('Location: /404');
	}

	public function delete(): void
	{
		$id = $_GET['id'] ?? null;

		if ($id) {
			$commentData = $this->db->getOneBy('comments', ['id' => $id]);

			if ($commentData) {
				$comment = new Comment($this->db, $commentData);

				if ($comment->delete()) {
					$_SESSION['flash_message'] = 'Commentaire supprimé avec succès.';
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de la suppression du commentaire.';
				}
				header('Location: /post/show?slug=' . $comment->getPost()->getSlug());
				exit;
			} else {
				$_SESSION['flash_message'] = 'Commentaire non trouvé.';
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de commentaire fourni.';
		}
		header('Location: /404');
	}

	// Supprimer un commentaire
	public function erase(): void
	{
		$id = $_GET['id'] ?? null;

		if ($id) {
			$commentData = $this->db->getOneBy('comments', ['id' => $id]);

			if ($commentData) {
				$comment = new Comment($this->db, $commentData);

				if ($comment->erase()) {
					$_SESSION['flash_message'] = 'Commentaire supprimé avec succès.';
				} else {
					$_SESSION['flash_message'] = 'Erreur lors de la suppression du commentaire.';
				}
				header('Location: /post/show?slug=' . $comment->getPost()->getSlug());
				exit;
			} else {
				$_SESSION['flash_message'] = 'Commentaire non trouvé.';
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun identifiant de commentaire fourni.';
		}
		header('Location: /404');
	}
}
