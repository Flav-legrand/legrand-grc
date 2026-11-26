<?php $title = 'Rapport Direction générale';
$cmap = array_column($reqStats, 'n', 'compliance'); $totalReq = array_sum($cmap);
$fmap = array_column($findings, 'n', 'severity'); ?>
<header class="page-h no-print"><h1>Rapport de sécurité — Direction générale</h1><button class="btn primary js-print" type="button">Imprimer / PDF</button></header>
<article class="report">
  <div class="report-head"><img src="assets/logo.svg" alt="Legrand GRC"><div><strong><?= e(config('organisation')) ?></strong><br>Tableau de bord sécurité de l'information — <?= e(date('d/m/Y')) ?><br><small>Diffusion : Direction générale · Classification : Interne</small></div></div>
  <h2>1. Synthèse</h2>
  <ul>
    <li><strong><?= count($topRisks) ?></strong> risques prioritaires suivis ; <?= array_sum(array_map(fn($r) => (int) ($r['likelihood'] * $r['impact'] >= 12), $topRisks)) ?> au niveau critique avant traitement.</li>
    <li>Mesures ISO 27002 applicables : <strong><?= $applicable ?></strong> — implémentées : <strong><?= $implemented ?></strong> (<?= $applicable ? round(100 * $implemented / $applicable) : 0 ?> %), en cours : <?= $inProgress ?>.</li>
    <li>Conformité RGSSI : <?= (int) ($cmap['conforme'] ?? 0) ?> exigences conformes, <?= (int) ($cmap['partiel'] ?? 0) ?> partielles, <?= (int) ($cmap['non_conforme'] ?? 0) ?> non conformes, <?= (int) ($cmap['a_evaluer'] ?? 0) ?> à évaluer (sur <?= $totalReq ?>).</li>
    <li>Constats d'audit ouverts : <?= (int) ($fmap['critique'] ?? 0) ?> critiques, <?= (int) ($fmap['elevee'] ?? 0) ?> élevés, <?= (int) ($fmap['moyenne'] ?? 0) ?> moyens, <?= (int) ($fmap['faible'] ?? 0) ?> faibles — dont <strong><?= count($overdue) ?></strong> en retard.</li>
  </ul>
  <h2>2. Cartographie</h2>
  <div class="hm-pair"><?= heatmap($matrix, 'Risque brut') ?><?= heatmap($matrixRes, 'Risque résiduel') ?></div>
  <h2>3. Risques prioritaires</h2>
  <table><thead><tr><th>Réf.</th><th>Scénario</th><th>Brut</th><th>Traitement</th><th>Résiduel</th><th>Propriétaire</th></tr></thead><tbody>
  <?php foreach ($topRisks as $r): ?><tr><td><?= e($r['ref']) ?></td><td><?= e($r['title']) ?></td><td><?= risk_badge((int) $r['likelihood'] * (int) $r['impact']) ?></td><td><?= e(label('treatment', $r['treatment'])) ?></td><td><?= risk_badge(risk_score($r['residual_likelihood'] ? (int) $r['residual_likelihood'] : null, $r['residual_impact'] ? (int) $r['residual_impact'] : null)) ?></td><td><?= e($r['owner']) ?></td></tr><?php endforeach; ?>
  </tbody></table>
  <h2>4. Actions en retard</h2>
  <?php if (!$overdue): ?><p>Aucune action en retard.</p><?php else: ?>
  <table><thead><tr><th>Constat</th><th>Audit</th><th>Responsable</th><th>Échéance</th></tr></thead><tbody>
  <?php foreach ($overdue as $f): ?><tr><td><?= e($f['title']) ?></td><td><?= e($f['audit_title']) ?></td><td><?= e($f['action_owner']) ?></td><td class="late"><?= e($f['due_date']) ?></td></tr><?php endforeach; ?>
  </tbody></table><?php endif; ?>
  <h2>5. Décisions attendues de la Direction</h2>
  <ul><li>Valider les plans de traitement des risques résiduels ≥ 8 et accepter formellement les risques résiduels retenus.</li>
  <li>Arbitrer les ressources des actions en retard.</li><li>Valider le calendrier de mise en conformité RGSSI.</li></ul>
  <p class="sign">Le Responsable de la Sécurité des Systèmes d'Information</p>
</article>
