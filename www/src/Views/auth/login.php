<form action="/login" method="POST">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Se connecter</button>
</form>

<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-success">
		<?= $_SESSION['flash_message']; ?>
    </div>
	<?php unset($_SESSION['flash_message']); // Supprimer le message après affichage ?>
<?php endif; ?>
