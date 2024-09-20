<?php
// Vérifier si l'objet $page est défini
if (!isset($page)) {
	echo "Page introuvable.";
	exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de la page <?= $page->getTitle() ?></title>
</head>
<body>
<h1><?= $page->getTitle() ?></h1>
<a href="/admin/pages/post/create?id=<?= $page->getId() ?>">Ajouter un post</a>

<?php if (!empty($posts)): ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Slug</th>
            <th>Publié</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($posts as $post): ?>
            <tr>
                <td><?= $post['id'] ?></td>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td><?= htmlspecialchars($post['slug']) ?></td>
                <td><?= $post['is_published'] ? 'Oui' : 'Non' ?></td>
                <td>
                    <a href="/admin/posts/edit?id=<?= $page['id'] ?>">Éditer</a>
                    <form action="/admin/posts/delete" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $page['id'] ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune post trouvée.</p>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-danger">
		<?= $_SESSION['flash_message']; ?>
    </div>
	<?php unset($_SESSION['flash_message']); // Supprimer le message après affichage ?>
<?php endif; ?>
</body>
</html>
