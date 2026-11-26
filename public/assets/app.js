// Confirmation des actions destructrices (aucun script inline : compatible CSP stricte)
document.addEventListener('submit', function (ev) {
  var f = ev.target;
  if (f.classList && f.classList.contains('js-confirm') && !window.confirm(f.dataset.confirm || 'Confirmer ?')) {
    ev.preventDefault();
  }
});
// Impression du rapport
document.addEventListener('click', function (ev) {
  if (ev.target.classList && ev.target.classList.contains('js-print')) { window.print(); }
});
// Filtre rapide des tableaux
document.addEventListener('input', function (ev) {
  var el = ev.target;
  if (!el.classList || !el.classList.contains('js-filter')) return;
  var t = document.querySelector(el.dataset.target), v = el.value.toLowerCase();
  if (!t) return;
  t.querySelectorAll('tbody tr').forEach(function (tr) {
    tr.hidden = tr.textContent.toLowerCase().indexOf(v) === -1;
  });
});
