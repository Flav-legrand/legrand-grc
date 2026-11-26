<?php $title = 'Audits'; ?>
<header class="page-h"><h1>Audits de sécurité &amp; plans d'action</h1>
<?php if (can('audit_write')): ?><a class="btn primary" href="<?= e(url('audit_edit')) ?>">+ Nouvel audit</a><?php endif; ?></header>
<table><thead><tr><th>Audit</th><th>Type</th><th>Auditeur</th><th>Période</th><th>Statut</th><th>Constats</th><th></th></tr></thead><tbody>
<?php foreach ($audits as $a): ?>
<tr><td><a href="<?= e(url('audit_view', ['id' => $a['id']])) ?>"><strong><?= e($a['title']) ?></strong></a><br><small class="muted"><?= e($a['scope']) ?></small></td>
<td><?= e($a['type']) ?></td><td><?= e($a['auditor']) ?></td><td><?= e($a['start_date']) ?><?= $a['end_date'] ? ' → ' . e($a['end_date']) : '' ?></td>
<td><?= e(label('status', $a['status'])) ?></td><td><?= (int) $a['nb'] ?> <small class="muted">(<?= (int) $a['open_nb'] ?> ouverts)</small></td>
<td class="actions"><a class="btn small" href="<?= e(url('audit_view', ['id' => $a['id']])) ?>">Ouvrir</a></td></tr>
<?php endforeach; ?></tbody></table>
