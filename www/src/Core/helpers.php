<?php

function isLoggedIn(): bool
{
	return isset($_SESSION['user']);
}

function getCurrentUser(): ?array
{
	return $_SESSION['user'] ?? null;
}

function requireLogin(): bool
{
	if (!isLoggedIn()) {
		$_SESSION['flash_message'] = 'Vous devez être connecté pour accéder à cette page.';
		header('Location: /login');
		return false;
	}
	return true;
}

function requireRole(string $role): bool
{
	if (!isLoggedIn() || $_SESSION['user']['role'] !== $role) {
		$_SESSION['flash_message'] = 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.';
		header('Location: /');
		return false;
	}
	return true;
}

// Utility function to convert a string to a URL-friendly slug
function slugify(string $text): string
{
	// Replace non-letter or non-digit characters with '-'
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);

	// Transliterate (convert to ASCII)
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	// Remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);

	// Trim and lowercase
	$text = trim($text, '-');
	$text = strtolower($text);

	// Return 'n-a' if the resulting slug is empty
	if (empty($text)) {
		return 'n-a';
	}

	return $text;
}
