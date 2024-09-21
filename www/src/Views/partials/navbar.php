<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once __DIR__ . '/../../Core/helpers.php';

use App\src\Core\DB;
use App\src\Models\Page;

$db = DB::getInstance();
?>

<nav>
    <ul>
		<?php
		$pages = Page::getAllPublishedPages($db);

		foreach ($pages as $navItem) {
			$navItem = new Page($db, $navItem);
			echo '<li><a href="/page?slug=' . htmlspecialchars($navItem->getSlug()) . '">' . htmlspecialchars($navItem->getTitle()) . '</a></li>';
		}
		?>

		<?php if (isLoggedIn()): ?>
            <!--            <li><a href="/profile">Mon profil</a></li>-->
			<?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <li><a href="/admin/pages">Paramètres</a></li>
			<?php endif; ?>
            <li><a href="/logout">Déconnexion</a></li>
		<?php else: ?>
            <li><a href="/login" class="<?= ($_SERVER['REQUEST_URI'] == '/login') ? 'active' : '' ?>">Connexion</a></li>
            <li><a href="/register" class="<?= ($_SERVER['REQUEST_URI'] == '/register') ? 'active' : '' ?>">Inscription</a></li>
		<?php endif; ?>
    </ul>
</nav>