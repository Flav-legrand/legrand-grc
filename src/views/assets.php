<?php $title = 'Actifs'; ?>
<header class="page-h"><h1>Inventaire des actifs <small class="muted">ISO 27002 — 5.9</small></h1>
<?php if (can('write')): ?><a class="btn primary" href="<?= e(url('asset_edit')) ?>">+ Nouvel actif</a><?php endif; ?></header>
<input class="js-filter search" data-target="#t-assets" placeholder="Filtrer…">
<table id="t-assets"><thead><tr><th>Actif</th><th>Catégorie</th><th>Propriétaire</th><th>C</th><th>I</th><th>D</th><th>Criticité</th><th>Risques</th><th></th></tr></thead><tbody>
<?php foreach ($assets as $a): $crit = asset_criticality($a); ?>
<tr><td><strong><?= e($a['name']) ?></strong><br><small class="muted"><?= e($a['description']) ?></small></td><td><?= e($a['category']) ?></td><td><?= e($a['owner']) ?></td>
<td><?= (int) $a['confidentiality'] ?></td><td><?= (int) $a['integrity'] ?></td><td><?= (int) $a['availability'] ?></td>
<td><span class="badge crit-<?= $crit ?>"><?= $crit ?>/4</span></td><td><?= (int) $a['risk_count'] ?></td>
<td class="actions"><?php if (can('write')): ?><a class="btn small" href="<?= e(url('asset_edit', ['id' => $a['id']])) ?>">Modifier</a><?= delete_button('asset', (int) $a['id']) ?><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table>
<p class="tiny muted">C/I/D : besoins de Confidentialité, Intégrité, Disponibilité de 1 (faible) à 4 (vital). Criticité = maximum des trois.</p>
