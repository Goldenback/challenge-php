<?php
$title = 'Éditer la Page';
$content = '
<form action="/admin/page/edit?id=' . htmlspecialchars($page->getId()) . '" method="POST">
    <label for="title">Titre :</label><br>
    <input type="text" name="title" id="title" value="' . htmlspecialchars($page->getTitle()) . '" required><br><br>

    <label for="content">Contenu :</label><br>
    <textarea name="content" id="content">' . htmlspecialchars($page->getContent()) . '</textarea><br><br>

    <label for="is_published">Publier :</label>
    <input type="checkbox" name="is_published" id="is_published" ' . ($page->isPublished() ? 'checked' : '') . '><br><br>

    <label for="is_home">Page d\'accueil :</label>
    <input type="checkbox" name="is_home" id="is_home" ' . ($page->isHome() ? 'checked' : '') . '><br><br>

    <button type="submit">Mettre à jour</button>
</form>';
$enableTinyMCE = true;
include __DIR__ . '/../../templates/base.php';
