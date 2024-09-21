<?php
if (!isset($post)) {
	echo "Page introuvable.";
	exit;
}

$title = htmlspecialchars($post->getTitle());
$content = $post->getContent();

include __DIR__ . '/../../templates/base.php';