<?php $title = 'Utilisateurs'; ?>
<h1>Utilisateurs et rôles</h1>
<?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
<table><thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Dernière connexion</th><th>Statut</th><th></th></tr></thead><tbody>
<?php foreach ($users as $u): ?>
<tr class="<?= $u['is_active'] ? '' : 'dim' ?>"><td><?= e($u['full_name']) ?></td><td><?= e($u['email']) ?></td><td><?= e(label('role', $u['role'])) ?></td><td><?= e($u['last_login_at'] ?? '—') ?></td><td><?= $u['is_active'] ? 'Actif' : 'Désactivé' ?></td>
<td><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $u['id'] ?>"><button class="btn small"><?= $u['is_active'] ? 'Désactiver' : 'Réactiver' ?></button></form></td></tr>
<?php endforeach; ?></tbody></table>
<h2>Créer un compte</h2>
<form method="post" class="card form">
  <?= csrf_field() ?><input type="hidden" name="action" value="create">
  <div class="row"><label>Nom complet<input name="full_name" required maxlength="120"></label><label>E-mail<input type="email" name="email" required></label></div>
  <div class="row"><label>Rôle<select name="role"><?= options('role', 'lecteur') ?></select></label>
  <label>Mot de passe initial<input type="password" name="password" required minlength="12" autocomplete="new-password"></label></div>
  <p class="tiny muted">Politique : 12 caractères minimum avec majuscule, minuscule, chiffre et caractère spécial. Principe du moindre privilège : attribuer le rôle minimal nécessaire.</p>
  <div class="form-actions"><button class="btn primary">Créer</button></div>
</form>
