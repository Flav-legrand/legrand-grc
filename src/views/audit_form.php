<?php $title = !empty($audit['id']) ? "Modifier l'audit" : 'Nouvel audit'; ?>
<h1><?= e($title) ?></h1>
<?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?>
  <label>Intitulé<input name="title" value="<?= e($audit['title'] ?? '') ?>" required maxlength="200"></label>
  <div class="row">
    <label>Type<select name="type"><?php foreach (AUDIT_TYPES as $t): ?><option<?= ($audit['type'] ?? '') === $t ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></label>
    <label>Auditeur / prestataire<input name="auditor" value="<?= e($audit['auditor'] ?? '') ?>" required maxlength="120"></label>
  </div>
  <label>Périmètre<textarea name="scope" rows="2" required maxlength="500"><?= e($audit['scope'] ?? '') ?></textarea></label>
  <div class="row">
    <label>Début<input type="date" name="start_date" value="<?= e($audit['start_date'] ?? date('Y-m-d')) ?>" required></label>
    <label>Fin<input type="date" name="end_date" value="<?= e($audit['end_date'] ?? '') ?>"></label>
    <label>Statut<select name="status"><?php foreach (['planifie', 'en_cours', 'termine'] as $s): ?><option value="<?= $s ?>"<?= ($audit['status'] ?? 'planifie') === $s ? ' selected' : '' ?>><?= e(label('status', $s)) ?></option><?php endforeach; ?></select></label>
  </div>
  <div class="form-actions"><button class="btn primary">Enregistrer</button> <a class="btn ghost" href="<?= e(url('audits')) ?>">Annuler</a></div>
</form>
