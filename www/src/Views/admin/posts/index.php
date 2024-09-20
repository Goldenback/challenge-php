<?php
// Vérifier si le tableau $pages est défini
use App\src\Models\Post;

if (!isset($posts)) {
	echo "Aucun post à afficher.";
	exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des posts</title>
</head>
<body>
<h1>Gestion des posts</h1>
<a href="/admin/pages/create">Créer une nouvelle page</a>
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
			<?php $post = new Post($this->db, $post); ?>
            <tr>
                <td><?= $post->getId() ?></td>
                <td><?= htmlspecialchars($post->getTitle()) ?></td>
                <td><?= htmlspecialchars($post->getSlug()) ?></td>
                <td><?= $post->isPublished() ? 'Oui' : 'Non' ?></td>
                <td>
                    <a href="/admin/post?id=<?= $post->getId() ?>">Voir</a>
                    <a href="/admin/posts/edit?id=<?= $post->getId() ?>">Éditer</a>
                    <form action="/admin/posts/delete" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $post->getId() ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun post trouvée.</p>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-danger">
		<?= $_SESSION['flash_message']; ?>
    </div>
	<?php unset($_SESSION['flash_message']); // Supprimer le message après affichage ?>
<?php endif; ?>
</body>
</html>
