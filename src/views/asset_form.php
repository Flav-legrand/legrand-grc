<?php $title = !empty($asset['id']) ? 'Modifier un actif' : 'Nouvel actif'; ?>
<h1><?= e($title) ?></h1>
<?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?>
  <label>Nom de l'actif<input name="name" value="<?= e($asset['name'] ?? '') ?>" required maxlength="150"></label>
  <div class="row">
    <label>Catégorie<select name="category"><?php foreach (ASSET_CATEGORIES as $c): ?><option<?= ($asset['category'] ?? '') === $c ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></label>
    <label>Propriétaire<input name="owner" value="<?= e($asset['owner'] ?? '') ?>" required maxlength="120"></label>
  </div>
  <label>Description<textarea name="description" rows="3"><?= e($asset['description'] ?? '') ?></textarea></label>
  <div class="row">
    <?php foreach (['confidentiality' => 'Confidentialité', 'integrity' => 'Intégrité', 'availability' => 'Disponibilité'] as $k => $l): ?>
    <label><?= $l ?><select name="<?= $k ?>"><?= scale_options((int) ($asset[$k] ?? 2), ['1 – Public', '2 – Interne', '3 – Important', '4 – Vital']) ?></select></label>
    <?php endforeach; ?>
  </div>
  <div class="form-actions"><button class="btn primary">Enregistrer</button> <a class="btn ghost" href="<?= e(url('assets')) ?>">Annuler</a></div>
</form>
