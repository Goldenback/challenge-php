<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Default Title'; ?></title>
    <link rel="stylesheet" href="/www/public/assets/css">
	<?php if (isset($enableTinyMCE) && $enableTinyMCE): ?>
        <script src="https://cdn.tiny.cloud/1/7h4clec09f3a0ki3xq7qqhl147b1tqa7gu8efhnixh2z1zr5/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
	<?php endif; ?>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-success">
		<?= $_SESSION['flash_message']; ?>
    </div>
	<?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<main>
	<?php echo $content; ?>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<?php if (isset($enableTinyMCE) && $enableTinyMCE): ?>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'link image code',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code'
        });
    </script>
<?php endif; ?>
</body>
</html>
