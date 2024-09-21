<?php
$title = 'Connexion';
$content = '<form action="/login" method="POST">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Se connecter</button>
</form>';
include __DIR__ . '/../templates/base.php';
