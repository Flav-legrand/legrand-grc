<?php $title = 'Connexion'; ?>
<div class="login-box">
  <img src="assets/logo.svg" alt="Legrand GRC" class="login-logo">
  <p class="muted">Pilotage de la sécurité de l'information — risques, conformité ISO 27001 / RGSSI, audits.</p>
  <?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="<?= e(url('login')) ?>" autocomplete="off">
    <?= csrf_field() ?>
    <label>E-mail<input type="email" name="email" value="<?= e($email) ?>" required autofocus></label>
    <label>Mot de passe<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn primary block" type="submit">Se connecter</button>
  </form>
  <p class="tiny muted">Accès réservé. Toutes les actions sont journalisées.</p>
</div>
