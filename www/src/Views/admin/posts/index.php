<?php
$title = 'Page de gestion des posts';

ob_start();
?>

<?php
if (!isset($posts)) {
	echo "Aucun post à afficher.";
	exit;
}
?>

    <a href="/admin/post/create?id=<?= $page->getId() ?>">Créer un nouveau post</a>

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
                <td><?= $post->getId() ?></td>
                <td><?= htmlspecialchars($post->getTitle()) ?></td>
                <td><?= htmlspecialchars($post->getSlug()) ?></td>
                <td><?= $post->isPublished() ? 'Oui' : 'Non' ?></td>
                <td>
                    <a href="/admin/post?id=<?= $post->getId() ?>">Voir</a>
                    <a href="/admin/post/edit?id=<?= $post->getId() ?>">Éditer</a>
                    <form action="/admin/post/delete?id=<?= $post->getId() ?>" method="POST" style="display:inline;">
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
<?php

$content = ob_get_clean();

include __DIR__ . '/../../templates/base.php';

