<?php $title = $title ?? 'Erreur'; ?>
<div class="card"><h1><?= e($title) ?></h1><p><?= e($message) ?></p><p><a class="btn" href="<?= e(url('dashboard')) ?>">Retour au tableau de bord</a></p></div>
