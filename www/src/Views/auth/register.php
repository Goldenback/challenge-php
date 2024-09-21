<?php
$title = 'Inscription';
$content = '<form action="/register" method="POST">
    <label for="username">Nom d\'utilisateur :</label>
    <input type="text" name="username" id="username" required>

    <label for="email">Email :</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Mot de passe :</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">S\'inscrire</button>
</form>';
include __DIR__ . '/../templates/base.php';