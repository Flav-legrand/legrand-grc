<?php $title = $audit['title']; ?>
<header class="page-h"><h1><?= e($audit['title']) ?></h1>
<div><?php if (can('audit_write')): ?><a class="btn" href="<?= e(url('audit_edit', ['id' => $audit['id']])) ?>">Modifier</a> <a class="btn primary" href="<?= e(url('finding_edit', ['audit' => $audit['id']])) ?>">+ Constat</a> <?= delete_button('audit', (int) $audit['id'], "Supprimer l'audit") ?><?php endif; ?></div></header>
<div class="card"><dl class="dl">
  <dt>Type</dt><dd><?= e($audit['type']) ?></dd><dt>Auditeur</dt><dd><?= e($audit['auditor']) ?></dd>
  <dt>Périmètre</dt><dd><?= e($audit['scope']) ?></dd><dt>Période</dt><dd><?= e($audit['start_date']) ?> → <?= e($audit['end_date'] ?: '…') ?></dd>
  <dt>Statut</dt><dd><?= e(label('status', $audit['status'])) ?></dd></dl></div>
<h2>Constats et plan d'action</h2>
<table><thead><tr><th>#</th><th>Constat</th><th>Gravité</th><th>Mesure</th><th>Recommandation</th><th>Responsable</th><th>Échéance</th><th>Statut</th><th></th></tr></thead><tbody>
<?php foreach ($findings as $i => $f): ?>
<tr><td><?= $i + 1 ?></td><td><?= e($f['title']) ?></td><td><span class="badge s-<?= e($f['severity']) ?>"><?= e(label('severity', $f['severity'])) ?></span></td>
<td><?= e($f['control_ref'] ?? '—') ?></td><td><small><?= e($f['recommendation']) ?></small></td><td><?= e($f['action_owner']) ?></td>
<td class="<?= ($f['due_date'] && $f['due_date'] < date('Y-m-d') && in_array($f['status'], ['ouvert', 'en_cours'], true)) ? 'late' : '' ?>"><?= e($f['due_date']) ?></td>
<td><?= e(label('status', $f['status'])) ?></td>
<td class="actions"><?php if (can('audit_write')): ?><a class="btn small" href="<?= e(url('finding_edit', ['id' => $f['id']])) ?>">Modifier</a><?= delete_button('finding', (int) $f['id']) ?><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table>
