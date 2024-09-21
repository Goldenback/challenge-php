<?php
$title = 'Page de gestion des pages';

// Start output buffering
ob_start();
?>

<?php
if (!isset($pages)) {
	echo "Aucune page à afficher.";
	exit;
}
?>

    <a href="/admin/page/create">Créer une nouvelle page</a>

<?php if (!empty($pages)): ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Slug</th>
            <th>Publié</th>
            <th>Page d'accueil</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($pages as $page): ?>
            <tr>
                <td><?= $page->getId() ?></td>
                <td><?= htmlspecialchars($page->getTitle()) ?></td>
                <td><?= htmlspecialchars($page->getSlug()) ?></td>
                <td><?= $page->isPublished() ? 'Oui' : 'Non' ?></td>
                <td><?= $page->isHome() ? 'Oui' : 'Non' ?></td>
                <td>
                    <a href="/admin/page?id=<?= $page->getId() ?>">Voir</a>
                    <a href="/admin/page/edit?id=<?= $page->getId() ?>">Éditer</a>
                    <form action="/admin/page/delete?id=<?= $page->getId() ?>" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $page->getId() ?>">
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

    <a href="/export-solution" class="btn btn-primary">Exporter la Solution</a>

<?php
// End output buffering and get the content
$content = ob_get_clean();

include __DIR__ . '/../../templates/base.php';
