<?php
$title = 'Créer une nouvelle page';
$content = '
<form action="/admin/page/create" method="POST">
    <label for="title">Titre :</label><br>
    <input type="text" name="title" id="title" required><br><br>

    <label for="content">Contenu :</label><br>
    <textarea name="content" id="content"></textarea><br><br>

    <label for="is_published">Publier :</label>
    <input type="checkbox" name="is_published" id="is_published"><br><br>

    <label for="is_home">Page d\'accueil :</label>
    <input type="checkbox" name="is_home" id="is_home"><br><br>

    <button type="submit">Créer</button>
</form>';
$enableTinyMCE = true;
include __DIR__ . '/../../templates/base.php';
