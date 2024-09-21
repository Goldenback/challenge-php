<?php
$title = 'Créer un nouvel article';
$pageId = $_GET['id'] ?? ''; // Assurez-vous que $pageId est bien défini
$content = '
<form action="/admin/post/create?id=' . htmlspecialchars($pageId) . '" method="POST">
    <label for="title">Titre :</label><br>
    <input type="text" name="title" id="title" required><br><br>

    <label for="content">Contenu :</label><br>
    <textarea name="content" id="content"></textarea><br><br>

    <label for="is_published">Publier :</label>
    <input type="checkbox" name="is_published" id="is_published"><br><br>

    <button type="submit">Créer</button>
</form>';

$enableTinyMCE = true;
include __DIR__ . '/../../templates/base.php';
