<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une nouvelle page</title>
    <!-- Inclure TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/7h4clec09f3a0ki3xq7qqhl147b1tqa7gu8efhnixh2z1zr5/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
<h1>Créer une nouvelle page</h1>

<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-danger">
		<?= $_SESSION['flash_message']; ?>
    </div>
	<?php unset($_SESSION['flash_message']); // Supprimer le message après affichage ?>
<?php endif; ?>

<form action="/admin/pages/create" method="POST">
    <label for="title">Titre :</label><br>
    <input type="text" name="title" id="title" required><br><br>

    <label for="slug">Slug :</label><br>
    <input type="text" name="slug" id="slug" required><br><br>

    <label for="content">Contenu :</label><br>
    <textarea name="content" id="content"></textarea><br><br>

    <label for="is_published">Publier :</label>
    <input type="checkbox" name="is_published" id="is_published"><br><br>

    <button type="submit">Créer</button>
</form>

<!-- Initialiser TinyMCE -->
<script>
    tinymce.init({
        selector: '#content',
        plugins: 'link image code',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code'
    });
</script>
</body>
</html>
