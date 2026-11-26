<?php $title = "Déclaration d'applicabilité"; ?>
<header class="page-h"><h1>Mesures ISO/IEC 27002:2022 — Déclaration d'applicabilité</h1>
<a class="btn" href="<?= e(url('soa_export')) ?>">Exporter la SoA (CSV)</a></header>
<div class="filters">
  <a class="chip <?= $theme === '' ? 'on' : '' ?>" href="<?= e(url('controls')) ?>">Toutes (93)</a>
  <?php foreach (['Organisationnelle' => '5 · Organisationnelles', 'Humaine' => '6 · Humaines', 'Physique' => '7 · Physiques', 'Technologique' => '8 · Technologiques'] as $k => $l): ?>
  <a class="chip <?= $theme === $k ? 'on' : '' ?>" href="<?= e(url('controls', ['theme' => $k])) ?>"><?= $l ?></a>
  <?php endforeach; ?>
  <input class="js-filter search" data-target="#t-ctl" placeholder="Filtrer…">
</div>
<table id="t-ctl"><thead><tr><th>Réf.</th><th>Mesure</th><th>Applicable</th><th>Justification</th><th>Statut</th><th>Responsable</th><th>Risques</th><th></th></tr></thead><tbody>
<?php foreach ($controls as $c): ?>
<tr class="<?= $c['applicable'] ? '' : 'dim' ?>"><td><?= e($c['ref']) ?></td><td><?= e($c['title']) ?></td><td><?= $c['applicable'] ? 'Oui' : 'Non' ?></td>
<td><small><?= e($c['justification']) ?></small></td><td><span class="badge st-<?= e($c['status']) ?>"><?= e(label('status', $c['status'])) ?></span></td>
<td><?= e($c['owner']) ?></td><td><?= (int) $c['risk_count'] ?></td>
<td><?php if (can('write')): ?><a class="btn small" href="<?= e(url('control_edit', ['id' => $c['id']])) ?>">Évaluer</a><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table>
