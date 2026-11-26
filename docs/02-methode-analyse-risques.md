# Méthode d'analyse des risques

Cadre : **ISO/IEC 27005:2022**, avec un vocabulaire inspiré d'**EBIOS Risk Manager** (sources de risque, scénarios). La méthode est volontairement simple pour être appliquée et tenue à jour dans Legrand GRC.

## 1. Démarche en 5 étapes

1. **Établir le contexte** : périmètre, enjeux métiers, exigences réglementaires (RGSSI, régulation bancaire, protection des données personnelles).
2. **Identifier les actifs** : actifs primordiaux (processus, informations) et actifs supports (applications, serveurs, réseaux, personnes, sites, fournisseurs), avec leurs besoins en **C**onfidentialité, **I**ntégrité et **D**isponibilité, notés de 1 à 4.
3. **Identifier les scénarios de risque** : *source de menace* → exploite une *vulnérabilité* → sur un *actif* → entraîne une *conséquence*.
4. **Évaluer** chaque scénario : vraisemblance × gravité.
5. **Traiter** le risque (réduire, accepter, transférer, éviter), relier les mesures de l'ISO 27002, réévaluer le risque résiduel et le faire accepter par la Direction.

## 2. Échelles

**Vraisemblance**

| Niveau | Libellé | Repère |
|---|---|---|
| 1 | Peu vraisemblable | Jamais observé dans le secteur, ou très difficile à réaliser |
| 2 | Vraisemblable | Déjà observé dans le secteur |
| 3 | Très vraisemblable | Observé plusieurs fois par an dans le secteur ou chez nous |
| 4 | Quasi certain | Tentatives quotidiennes ou vulnérabilité connue et exploitée |

**Gravité**

| Niveau | Libellé | Financier | Opérationnel | Réglementaire / image |
|---|---|---|---|---|
| 1 | Mineure | < 1 M FCFA | Gêne de quelques heures | Aucun |
| 2 | Significative | 1 à 50 M FCFA | Service dégradé moins d'une journée | Plainte client |
| 3 | Grave | 50 à 500 M FCFA | Arrêt d'un service de 1 à 3 jours | Observation du régulateur, presse locale |
| 4 | Critique | > 500 M FCFA | Arrêt du core banking de plus de 3 jours | Sanction, retrait d'agrément, presse nationale |

*Les seuils financiers sont à adapter à la taille de l'organisation.*

## 3. Niveau de risque et critères d'acceptation

**Niveau = Vraisemblance × Gravité**, de 1 à 16.

| Niveau | Score | Décision |
|---|---|---|
| Faible | 1–3 | Accepté, surveillé |
| Moyen | 4–6 | Traitement planifié, acceptation par le propriétaire du risque |
| Élevé | 8–9 | Plan de traitement obligatoire, suivi en comité sécurité |
| Critique | 12–16 | Traitement prioritaire ; tout maintien nécessite une **décision formelle de la DG** |

## 4. Traitement

- **Réduire** : appliquer des mesures de l'ISO 27002, reliées au risque dans l'outil.
- **Transférer** : assurance cyber, sous-traitance avec engagement contractuel (par exemple anti-DDoS chez l'opérateur).
- **Éviter** : supprimer l'activité ou la fonctionnalité à l'origine du risque.
- **Accepter** : décision tracée, avec date de réexamen.

Chaque risque a un **propriétaire** (responsable métier), une **échéance** et un **statut**. Le risque résiduel est réévalué une fois les mesures en place.

## 5. Livrables

- Registre des risques et cartographie brute et résiduelle (Legrand GRC → *Cartographie*).
- Déclaration d'applicabilité (Legrand GRC → *Mesures ISO 27002 / SoA* → export CSV).
- Plan de traitement des risques (risques en statut « En traitement » avec leurs échéances).
- Rapport à la Direction générale (Legrand GRC → *Rapport Direction générale* → impression en PDF).

## 6. Fréquence de mise à jour

Revue complète une fois par an, et mise à jour ciblée à chaque nouveau projet, incident majeur, résultat d'audit ou évolution réglementaire (par exemple une nouvelle version du RGSSI).
