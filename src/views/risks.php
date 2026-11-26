<?php $title = 'Registre des risques'; ?>
<header class="page-h"><h1>Registre des risques <small class="muted">ISO 27005</small></h1>
<?php if (can('write')): ?><a class="btn primary" href="<?= e(url('risk_edit')) ?>">+ Nouveau risque</a><?php endif; ?></header>
<div class="filters">
  <a class="chip <?= $filter === '' ? 'on' : '' ?>" href="<?= e(url('risks')) ?>">Tous</a>
  <?php foreach (['critique' => 'Critiques', 'eleve' => 'Élevés', 'moyen' => 'Moyens', 'faible' => 'Faibles'] as $k => $l): ?>
  <a class="chip <?= $filter === $k ? 'on' : '' ?>" href="<?= e(url('risks', ['level' => $k])) ?>"><?= $l ?></a>
  <?php endforeach; ?>
  <input class="js-filter search" data-target="#t-risks" placeholder="Filtrer…">
</div>
<table id="t-risks"><thead><tr><th>Réf.</th><th>Scénario de risque</th><th>Actif</th><th>Brut</th><th>Traitement</th><th>Résiduel</th><th>Propriétaire</th><th>Statut</th><th></th></tr></thead><tbody>
<?php foreach ($risks as $r): ?>
<tr><td><?= e($r['ref']) ?></td>
<td><strong><?= e($r['title']) ?></strong><br><small class="muted">Menace : <?= e($r['threat']) ?> — Vulnérabilité : <?= e($r['vulnerability']) ?></small></td>
<td><?= e($r['asset_name'] ?? '—') ?></td>
<td><?= risk_badge((int) $r['likelihood'] * (int) $r['impact']) ?></td>
<td><?= e(label('treatment', $r['treatment'])) ?></td>
<td><?= risk_badge(risk_score($r['residual_likelihood'] ? (int) $r['residual_likelihood'] : null, $r['residual_impact'] ? (int) $r['residual_impact'] : null)) ?></td>
<td><?= e($r['owner']) ?><?= $r['due_date'] ? '<br><small class="muted">échéance ' . e($r['due_date']) . '</small>' : '' ?></td>
<td><?= e(label('status', $r['status'])) ?></td>
<td class="actions"><?php if (can('write')): ?><a class="btn small" href="<?= e(url('risk_edit', ['id' => $r['id']])) ?>">Modifier</a><?= delete_button('risk', (int) $r['id']) ?><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table>
