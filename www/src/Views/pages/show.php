<?php
if (!isset($page)) {
	echo "Page introuvable.";
	exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = $page->getTitle();
include __DIR__ . '/../partials/header.php';
?>
<body>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div>
	<?= $page->getContent() ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>