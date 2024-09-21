<?php

namespace App\src\Controllers;

use App\src\Core\DB;
use App\src\Models\User;
use App\src\Models\UserToken;
use DateTimeImmutable;
use JetBrains\PhpStorm\NoReturn;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Random\RandomException;

class AuthController
{
	private ?DB $db;

	public function __construct()
	{
		$this->db = DB::getInstance();
	}

	/**
	 * @throws RandomException
	 */
	public function register(): void
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$username = $_POST['username'] ?? null;
			$email = $_POST['email'] ?? null;
			$password = $_POST['password'] ?? null;

			if ($username && $email && $password) {
				$userExist = $this->db->getOneBy('users', ['email' => $email]);

				if ($userExist) {
					echo "Un compte existe déjà avec cet email.";
					return;
				}

				$user = new User($this->db);
				$user->setUsername($username);
				$user->setEmail($email);
				$user->setPasswordHash($password);

				if ($user->save()) {
					$userId = $this->db->getConnection()->lastInsertId();
					$user->setId($userId);

					$token = bin2hex(random_bytes(16));  // Génération d'un token sécurisé
					$userToken = new UserToken($this->db, [
						'userId' => $user->getId(),
						'token' => $token,
						'tokenType' => 'activation',
						'expiresAt' => (new DateTimeImmutable())->modify('+1 day')  // Expire dans 24h
					]);

					$userToken->save();

					$this->sendActivationEmail($email, $token);

					echo "Utilisateur inscrit avec succès ! Veuillez vérifier votre email pour activer votre compte.";
				} else {
					echo "Erreur lors de l'inscription.";
				}
			} else {
				echo "Tous les champs sont requis.";
			}
		} else {
			require_once __DIR__ . '/../Views/auth/register.php';
		}
	}

	public function login(): void
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$email = $_POST['email'] ?? null;
			$password = $_POST['password'] ?? null;

			if ($email && $password) {
				$user = $this->db->getOneBy('users', ['email' => $email]);

				if ($user && password_verify($password, $user['password_hash'])) {
					if ($user['is_active']) {
						$_SESSION['user'] = [
							'id' => $user['id'],
							'username' => $user['username'],
							'role' => $user['role']
						];
						header('Location: /');
					} else {
						$_SESSION['flash_message'] = 'Votre compte n\'est pas activé. Vérifiez votre email pour activer votre compte.';
						header('Location: /login');
					}
				} else {
					$_SESSION['flash_message'] = 'Email ou mot de passe incorrect.';
					header('Location: /login');
				}
			} else {
				$_SESSION['flash_message'] = 'Tous les champs sont requis.';
				header('Location: /login');
			}
		} else {
			require_once __DIR__ . '/../Views/auth/login.php';
		}
	}

	#[NoReturn] public function logout(): void
	{
		session_destroy();
		header('Location: /login');
		exit;
	}

	private function sendActivationEmail(string $email, string $token): void
	{
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = 'smtp.sendgrid.net';
		$mail->SMTPAuth = true;
		$mail->Username = 'apikey';
		$mail->Password = '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';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;

		$mail->setFrom('iliesrimani.work@gmail.com', 'Votre Site');
		$mail->addAddress($email);

		$mail->isHTML(true);
		$mail->Subject = 'Activation de votre compte';
		$mail->Body = "Cliquez sur ce lien pour activer votre compte : <a href='http://localhost/activate-account?token=$token'>Activer mon compte</a>";

		if (!$mail->send()) {
			echo 'Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo;
		} else {
			echo 'Email d\'activation envoyé avec succès.';
		}
	}

	public function activateAccount(): void
	{
		$token = $_GET['token'] ?? null;

		if ($token) {
			$userToken = $this->db->getOneBy('user_tokens', ['token' => $token, 'token_type' => 'activation']);

			if ($userToken) {
				$userId = $userToken['user_id'];
				$user = $this->db->getOneBy('users', ['id' => $userId]);

				if ($user && !$user['is_active']) {
					// Activer l'utilisateur
					$updatedUser = new User($this->db, $user);
					$updatedUser->setIsActive(true);
					$updatedUser->update();

					// Supprimer le token après activation
					$this->db->delete('user_tokens', ['id' => $userToken['id']]);

					$_SESSION['flash_message'] = 'Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.';
					header('Location: /login');
				} else {
					$_SESSION['flash_message'] = 'Ce compte est déjà activé ou le token est invalide.';
					header('Location: /register');
				}
			} else {
				$_SESSION['flash_message'] = 'Token d\'activation invalide.';
				header('Location: /register');
			}
		} else {
			$_SESSION['flash_message'] = 'Aucun token d\'activation fourni.';
			header('Location: /register');
		}
	}
}
