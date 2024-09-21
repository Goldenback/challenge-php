<?php
if (!isset($page)) {
	echo "Page introuvable.";
	exit;
}

$title = $page->getTitle();
$content = $page->getContent();
include __DIR__ . '/../templates/base.php';
