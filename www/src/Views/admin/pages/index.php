<?php
// Vérifier si le tableau $pages est défini
if (!isset($pages)) {
	echo "Aucune page à afficher.";
	exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des pages</title>
</head>
<body>
<h1>Gestion des pages</h1>
<a href="/admin/pages/create">Créer une nouvelle page</a>
<?php if (!empty($pages)): ?>
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
		<?php foreach ($pages as $page): ?>
            <tr>
                <td><?= $page['id'] ?></td>
                <td><?= htmlspecialchars($page['title']) ?></td>
                <td><?= htmlspecialchars($page['slug']) ?></td>
                <td><?= $page['is_published'] ? 'Oui' : 'Non' ?></td>
                <td>
                    <a href="/admin/page?id=<?= $page['id'] ?>">Voir</a>
                    <a href="/admin/pages/edit?id=<?= $page['id'] ?>">Éditer</a>
                    <form action="/admin/pages/delete" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $page['id'] ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune page trouvée.</p>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-danger">
		<?= $_SESSION['flash_message']; ?>
    </div>
	<?php unset($_SESSION['flash_message']); // Supprimer le message après affichage ?>
<?php endif; ?>
</body>
</html>
