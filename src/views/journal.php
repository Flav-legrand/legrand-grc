<?php $title = "Journal d'audit"; ?>
<h1>Journal d'audit applicatif <small class="muted">ISO 27002 — 8.15</small></h1>
<input class="js-filter search" data-target="#t-log" placeholder="Filtrer…">
<table id="t-log"><thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Objet</th><th>Détail</th><th>IP</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr class="<?= in_array($r['action'], ['connexion_echec', 'connexion_bloquee', 'acces_refuse'], true) ? 'alert' : '' ?>"><td><?= e($r['created_at']) ?></td><td><?= e($r['email'] ?? '—') ?></td><td><?= e($r['action']) ?></td><td><?= e($r['entity']) ?><?= $r['entity_id'] ? ' #' . (int) $r['entity_id'] : '' ?></td><td><small><?= e($r['details']) ?></small></td><td><?= e($r['ip']) ?></td></tr>
<?php endforeach; ?></tbody></table>
