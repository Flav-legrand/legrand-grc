<?php $title = 'Exigences RGSSI'; ?>
<header class="page-h"><h1>Conformité au RGSSI <small class="muted">ANSSI Congo</small></h1>
<?php if (can('write')): ?><a class="btn primary" href="<?= e(url('requirement_edit')) ?>">+ Nouvelle exigence</a><?php endif; ?></header>
<div class="flash warn">Les exigences préchargées sont une <strong>trame provisoire par domaine</strong>. Remplacez-les par les articles du texte officiel du RGSSI publié par l'ANSSI Congo ; chaque exigence se relie aux mesures ISO 27002 qui permettent de la satisfaire.</div>
<input class="js-filter search" data-target="#t-req" placeholder="Filtrer…">
<table id="t-req"><thead><tr><th>Réf.</th><th>Domaine</th><th>Exigence</th><th>Mesures ISO 27002</th><th>Conformité</th><th>Commentaire</th><th></th></tr></thead><tbody>
<?php foreach ($reqs as $r): ?>
<tr><td><?= e($r['ref']) ?><br><small class="muted"><?= e($r['framework']) ?></small></td><td><?= e($r['domain']) ?></td><td><?= e($r['title']) ?></td>
<td><small><?= e(implode(', ', $map[$r['id']] ?? [])) ?></small></td>
<td><span class="badge c-<?= e($r['compliance']) ?>"><?= e(label('compliance', $r['compliance'])) ?></span></td><td><small><?= e($r['comment']) ?></small></td>
<td class="actions"><?php if (can('write')): ?><a class="btn small" href="<?= e(url('requirement_edit', ['id' => $r['id']])) ?>">Évaluer</a><?= delete_button('requirement', (int) $r['id']) ?><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table>
