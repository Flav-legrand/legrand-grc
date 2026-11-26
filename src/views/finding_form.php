<?php $title = !empty($f['id']) ? 'Modifier le constat' : 'Nouveau constat'; ?>
<h1><?= e($title) ?> <small class="muted"><?= e($audit['title']) ?></small></h1>
<?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?>
  <label>Constat<input name="title" value="<?= e($f['title'] ?? '') ?>" required maxlength="200"></label>
  <div class="row">
    <label>Gravité<select name="severity"><?= options('severity', $f['severity'] ?? 'moyenne') ?></select></label>
    <label>Mesure ISO 27002 concernée<select name="control_id"><option value="0">—</option><?php foreach ($controls as $c): ?><option value="<?= (int) $c['id'] ?>"<?= (int) ($f['control_id'] ?? 0) === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['ref'] . ' ' . $c['title']) ?></option><?php endforeach; ?></select></label>
  </div>
  <label>Recommandation / action corrective<textarea name="recommendation" rows="3" required maxlength="600"><?= e($f['recommendation'] ?? '') ?></textarea></label>
  <div class="row">
    <label>Responsable de l'action<input name="action_owner" value="<?= e($f['action_owner'] ?? '') ?>" required maxlength="120"></label>
    <label>Échéance<input type="date" name="due_date" value="<?= e($f['due_date'] ?? '') ?>"></label>
    <label>Statut<select name="status"><?php foreach (['ouvert', 'en_cours', 'corrige', 'accepte'] as $s): ?><option value="<?= $s ?>"<?= ($f['status'] ?? 'ouvert') === $s ? ' selected' : '' ?>><?= e(label('status', $s)) ?></option><?php endforeach; ?></select></label>
  </div>
  <div class="form-actions"><button class="btn primary">Enregistrer</button> <a class="btn ghost" href="<?= e(url('audit_view', ['id' => $audit['id']])) ?>">Annuler</a></div>
</form>
