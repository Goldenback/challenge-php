<?php
if (!isset($page)) {
	echo "Page introuvable.";
	exit;
}

$link = '<a href="/admin/posts?id=' . htmlspecialchars($page->getId()) . '">Voir les posts</a>';

$title = htmlspecialchars($page->getTitle());
$content = $link . '<br>' . $page->getContent();

include __DIR__ . '/../../templates/base.php';

