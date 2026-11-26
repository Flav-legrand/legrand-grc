<?php $title = !empty($risk['id']) ? 'Modifier le risque ' . ($risk['ref'] ?? '') : 'Nouveau risque'; ?>
<h1><?= e($title) ?></h1>
<?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?>
  <div class="row">
    <label class="narrow">Référence<input name="ref" value="<?= e($risk['ref'] ?? '') ?>" required maxlength="20"></label>
    <label>Intitulé du scénario<input name="title" value="<?= e($risk['title'] ?? '') ?>" required maxlength="200"></label>
  </div>
  <label>Actif concerné<select name="asset_id"><option value="0">— Aucun —</option>
    <?php foreach ($assets as $a): ?><option value="<?= (int) $a['id'] ?>"<?= (int) ($risk['asset_id'] ?? 0) === (int) $a['id'] ? ' selected' : '' ?>><?= e($a['name']) ?> (<?= e($a['category']) ?>)</option><?php endforeach; ?>
  </select></label>
  <div class="row">
    <label>Source de menace / menace<input name="threat" value="<?= e($risk['threat'] ?? '') ?>" required maxlength="300"></label>
    <label>Vulnérabilité exploitée<input name="vulnerability" value="<?= e($risk['vulnerability'] ?? '') ?>" required maxlength="300"></label>
  </div>
  <label>Conséquences (impact métier)<input name="consequence" value="<?= e($risk['consequence'] ?? '') ?>" maxlength="300"></label>
  <fieldset><legend>Évaluation du risque brut</legend><div class="row">
    <label>Vraisemblance<select name="likelihood"><?= scale_options((int) ($risk['likelihood'] ?? 2), ['1 – Peu vraisemblable', '2 – Vraisemblable', '3 – Très vraisemblable', '4 – Quasi certain']) ?></select></label>
    <label>Gravité<select name="impact"><?= scale_options((int) ($risk['impact'] ?? 2), ['1 – Mineure', '2 – Significative', '3 – Grave', '4 – Critique']) ?></select></label>
  </div></fieldset>
  <fieldset><legend>Traitement</legend><div class="row">
    <label>Option<select name="treatment"><?= options('treatment', $risk['treatment'] ?? 'reduire') ?></select></label>
    <label>Propriétaire du risque<input name="owner" value="<?= e($risk['owner'] ?? '') ?>" required maxlength="120"></label>
    <label>Échéance<input type="date" name="due_date" value="<?= e($risk['due_date'] ?? '') ?>"></label>
    <label>Statut<select name="status"><?php foreach (['ouvert', 'en_traitement', 'clos'] as $s): ?><option value="<?= $s ?>"<?= ($risk['status'] ?? 'ouvert') === $s ? ' selected' : '' ?>><?= e(label('status', $s)) ?></option><?php endforeach; ?></select></label>
  </div>
  <label>Mesures ISO 27002 associées <small class="muted">(Ctrl/Cmd + clic pour plusieurs)</small>
    <select name="controls[]" multiple size="8"><?php foreach ($controls as $c): ?><option value="<?= (int) $c['id'] ?>"<?= in_array((int) $c['id'], $linked, true) ? ' selected' : '' ?>><?= e($c['ref'] . ' ' . $c['title']) ?></option><?php endforeach; ?></select></label>
  </fieldset>
  <fieldset><legend>Risque résiduel (après mesures)</legend><div class="row">
    <label>Vraisemblance résiduelle<select name="residual_likelihood"><option value="">—</option><?= scale_options(isset($risk['residual_likelihood']) ? (int) $risk['residual_likelihood'] : null) ?></select></label>
    <label>Gravité résiduelle<select name="residual_impact"><option value="">—</option><?= scale_options(isset($risk['residual_impact']) ? (int) $risk['residual_impact'] : null) ?></select></label>
  </div></fieldset>
  <div class="form-actions"><button class="btn primary">Enregistrer</button> <a class="btn ghost" href="<?= e(url('risks')) ?>">Annuler</a></div>
</form>
