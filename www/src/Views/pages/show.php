<?php
if (!isset($page)) {
	echo "Page introuvable.";
	exit;
}

$title = $page->getTitle();
$content = '<h1>' . htmlspecialchars($title) . '</h1>';
$content .= '<div>' . htmlspecialchars($content) . '</div>';

$posts = $page->getPosts() ?? []; // Utilisez une liste vide si aucun post n'est trouvé

if (!empty($posts)) {
	$content .= '<h2>Posts associés :</h2>';
	$content .= '<ul>';
	foreach ($posts as $post) {
		$content .= '<li>';
		$content .= '<h3>' . htmlspecialchars($post->getTitle()) . '</h3>';
		$content .= '<p>' . htmlspecialchars($post->getContent()) . '</p>';
		$content .= '<p><a href="/post?id=' . htmlspecialchars($post->getId()) . '">Lire plus</a></p>';
		$content .= '</li>';
	}
	$content .= '</ul>';
} else {
	$content .= '<p>Aucun post associé à cette page.</p>';
}

$enableTinyMCE = false; // Assurez-vous que TinyMCE est désactivé pour cette vue
include __DIR__ . '/../templates/base.php';
