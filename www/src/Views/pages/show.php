<?php
if (!isset($page)) {
	echo "Page introuvable.";
	exit;
}

$title = $page->getTitle();
$pageContent = $page->getContent(); // Contenu principal de la page

// Assurez-vous que vous avez accès aux posts depuis le contrôleur
$posts = $page->getPosts() ?? []; // Utilisez une liste vide si aucun post n'est trouvé

// Construire le contenu HTML
$content = '<h1>' . htmlspecialchars($title) . '</h1>';
$content .= '<div>' . $pageContent . '</div>';

if (!empty($posts)) {
	$content .= '<h2>Posts associés :</h2>';
	$content .= '<ul>';
	foreach ($posts as $post) {
		$content .= '<li>';
		$content .= '<h3>' . htmlspecialchars($post->getTitle()) . '</h3>';
		$content .= '<p>' . $post->getContent() . '</p>';
		$content .= '<p><a href="/post?id=' . htmlspecialchars($post->getId()) . '">Lire plus</a></p>';
		$content .= '</li>';
	}
	$content .= '</ul>';
}

// Désactiver TinyMCE pour cette vue si ce n'est pas nécessaire
$enableTinyMCE = false;

include __DIR__ . '/../templates/base.php';
