<?php $title = 'Mesure ' . $c['ref']; ?>
<h1><?= e($c['ref'] . ' — ' . $c['title']) ?></h1>
<p class="muted">Thème : <?= e($c['theme']) ?></p>
<?php foreach ($errors as $er): ?><div class="flash err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" class="card form">
  <?= csrf_field() ?>
  <div class="row">
    <label>Applicable ?<select name="applicable"><option value="1"<?= (string) $c['applicable'] === '1' ? ' selected' : '' ?>>Oui</option><option value="0"<?= (string) $c['applicable'] === '0' ? ' selected' : '' ?>>Non</option></select></label>
    <label>Statut de mise en œuvre<select name="status"><?php foreach (['non_commence', 'en_cours', 'implemente'] as $s): ?><option value="<?= $s ?>"<?= $c['status'] === $s ? ' selected' : '' ?>><?= e(label('status', $s)) ?></option><?php endforeach; ?></select></label>
    <label>Responsable<input name="owner" value="<?= e($c['owner']) ?>" maxlength="120"></label>
  </div>
  <label>Justification d'inclusion ou d'exclusion<textarea name="justification" rows="3" required maxlength="500"><?= e($c['justification']) ?></textarea></label>
  <label>Preuves / références documentaires<input name="evidence" value="<?= e($c['evidence']) ?>" maxlength="500" placeholder="ex. PSSI §4.2, procédure PR-SAV-01, rapport de scan du 12/11"></label>
  <div class="form-actions"><button class="btn primary">Enregistrer</button> <a class="btn ghost" href="<?= e(url('controls', ['theme' => $c['theme']])) ?>">Annuler</a></div>
</form>
