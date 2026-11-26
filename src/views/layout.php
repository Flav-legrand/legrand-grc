<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(($title ?? '') ? $title . ' · ' : '') ?>Legrand GRC</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="icon" href="assets/logo.svg">
<script src="assets/app.js" defer></script>
</head>
<body class="<?= $user ? 'app' : 'guest' ?>">
<?php if ($user): ?>
<aside class="sidebar">
  <a href="<?= e(url('dashboard')) ?>" class="brand"><img src="assets/logo.svg" alt="Legrand GRC"></a>
  <div class="org"><?= e(config('organisation')) ?></div>
  <nav>
    <a href="<?= e(url('dashboard')) ?>">Tableau de bord</a>
    <span class="nav-h">Risques</span>
    <a href="<?= e(url('assets')) ?>">Actifs</a>
    <a href="<?= e(url('risks')) ?>">Registre des risques</a>
    <a href="<?= e(url('matrix')) ?>">Cartographie</a>
    <span class="nav-h">Conformité</span>
    <a href="<?= e(url('controls')) ?>">Mesures ISO 27002 / SoA</a>
    <a href="<?= e(url('requirements')) ?>">Exigences RGSSI</a>
    <a href="<?= e(url('audits')) ?>">Audits &amp; plans d'action</a>
    <span class="nav-h">Pilotage</span>
    <a href="<?= e(url('report')) ?>">Rapport Direction générale</a>
    <?php if (can('admin')): ?>
    <a href="<?= e(url('users')) ?>">Utilisateurs</a>
    <a href="<?= e(url('journal')) ?>">Journal d'audit</a>
    <?php endif; ?>
  </nav>
  <div class="me">
    <strong><?= e($user['full_name']) ?></strong><br><small><?= e(label('role', $user['role'])) ?></small>
    <form method="post" action="<?= e(url('logout')) ?>"><?= csrf_field() ?><button class="btn ghost small" type="submit">Se déconnecter</button></form>
  </div>
</aside>
<?php endif; ?>
<main class="content">
  <?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
  <?= $content ?>
</main>
</body>
</html>
