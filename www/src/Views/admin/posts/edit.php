<?php
$title = 'Éditer le Post';
$content = '
<form action="/admin/post/edit?id=' . htmlspecialchars($post->getId()) . '" method="POST">
    <label for="title">Titre :</label><br>
    <input type="text" name="title" id="title" value="' . htmlspecialchars($post->getTitle()) . '" required><br><br>

    <label for="content">Contenu :</label><br>
    <textarea name="content" id="content">' . htmlspecialchars($post->getContent()) . '</textarea><br><br>

    <label for="is_published">Publier :</label>
    <input type="checkbox" name="is_published" id="is_published" ' . ($post->isPublished() ? 'checked' : '') . '><br><br>

    <button type="submit">Mettre à jour</button>
</form>';

$enableTinyMCE = true; // Enable TinyMCE for this page
$editorContent = htmlspecialchars($post->getContent()); // Content to be loaded into TinyMCE
include __DIR__ . '/../../templates/base.php';
