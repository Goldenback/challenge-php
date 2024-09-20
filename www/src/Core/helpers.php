<?php

function isLoggedIn(): bool
{
	return isset($_SESSION['user']);
}

function getCurrentUser(): ?array
{
	return $_SESSION['user'] ?? null;
}

function requireLogin(): void
{
	if (!isLoggedIn()) {
		$_SESSION['flash_message'] = 'Vous devez être connecté pour accéder à cette page.';
		header('Location: /login');
		exit;
	}
}

function requireRole(string $role): void
{
	if (!isLoggedIn() || $_SESSION['user']['role'] !== $role) {
		$_SESSION['flash_message'] = 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.';
		header('Location: /login');
		exit;
	}
}