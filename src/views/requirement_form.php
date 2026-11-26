<?php $title = !empty($req['id']) ? 'Exigence ' . ($req['ref'] ?? '') : 'Nouvelle exigence'; ?>
<h1><?= e($title) ?></h1>
<?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?>
  <div class="row">
    <label class="narrow">Référentiel<input name="framework" value="<?= e($req['framework'] ?? 'RGSSI') ?>" maxlength="30"></label>
    <label class="narrow">Référence<input name="ref" value="<?= e($req['ref'] ?? '') ?>" required maxlength="30"></label>
    <label>Domaine<input name="domain" value="<?= e($req['domain'] ?? '') ?>" required maxlength="80"></label>
  </div>
  <label>Intitulé de l'exigence<textarea name="title" rows="2" required maxlength="300"><?= e($req['title'] ?? '') ?></textarea></label>
  <div class="row">
    <label>Niveau de conformité<select name="compliance"><?= options('compliance', $req['compliance'] ?? 'a_evaluer') ?></select></label>
    <label>Commentaire / écart constaté<input name="comment" value="<?= e($req['comment'] ?? '') ?>" maxlength="500"></label>
  </div>
  <label>Mesures ISO 27002 qui couvrent l'exigence
    <select name="controls[]" multiple size="8"><?php foreach ($controls as $c): ?><option value="<?= (int) $c['id'] ?>"<?= in_array((int) $c['id'], $linked, true) ? ' selected' : '' ?>><?= e($c['ref'] . ' ' . $c['title']) ?></option><?php endforeach; ?></select></label>
  <div class="form-actions"><button class="btn primary">Enregistrer</button> <a class="btn ghost" href="<?= e(url('requirements')) ?>">Annuler</a></div>
</form>
