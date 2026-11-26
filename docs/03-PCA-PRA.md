# Plan de Continuité et de Reprise d'Activité (PCA / PRA) — ébauche

**Organisation :** Banque Mayombe (fictive) · **Version :** 0.9 (projet) · **Propriétaire :** RSSI, avec la DSI et la Direction des opérations

## 1. Bilan d'impact sur l'activité (BIA)

| Processus / service | Actifs supports | DMIA (RTO) | PDMA (RPO) | Priorité |
|---|---|---|---|---|
| Opérations de caisse et tenue de comptes | Core banking, base de données, réseau inter-agences | 4 h | 15 min | 1 |
| Interconnexion Mobile Money | Passerelle API, liaisons opérateurs | 4 h | 0 (journal des transactions) | 1 |
| Banque en ligne | Serveurs web, API, WAF | 8 h | 1 h | 2 |
| Messagerie | Messagerie, Active Directory | 24 h | 4 h | 3 |
| Reporting réglementaire | Entrepôt de données | 72 h | 24 h | 4 |

DMIA : durée maximale d'interruption admissible · PDMA : perte de données maximale admissible.

## 2. Scénarios couverts

1. **Coupure électrique prolongée** au siège (délestage, panne du groupe électrogène).
2. **Rançongiciel** chiffrant les serveurs et les postes.
3. **Perte de la salle serveurs** (incendie, inondation).
4. **Indisponibilité d'un fournisseur critique** (opérateur télécom, infogérant, opérateur Mobile Money).
5. **Indisponibilité du personnel clé** (absence simultanée des administrateurs).

## 3. Stratégie de continuité

| Domaine | Dispositif |
|---|---|
| Énergie | Onduleurs (30 min d'autonomie), groupe électrogène avec 48 h de carburant, test mensuel en charge |
| Données | Réplication asynchrone vers un site de secours (Pointe-Noire), sauvegardes 3-2-1 dont une copie hors ligne et immuable |
| Infrastructure | Site de secours « tiède » capable de faire tourner le core banking en mode dégradé |
| Réseau | Deux opérateurs télécoms différents pour les liaisons critiques |
| Mode dégradé | Procédures manuelles en agence (registre papier des opérations, plafonds de retrait réduits) |
| Communication | Arbre d'appel, messages préparés pour les clients, le régulateur et l'ANSSI |

## 4. Organisation de crise

- **Cellule de crise décisionnelle** : DG (pilote), RSSI, DSI, Directeur des opérations, Communication, Juridique et Conformité.
- **Cellule opérationnelle** : équipes DSI et SOC, prestataires.
- **Déclenchement** : par le RSSI ou la DSI dès qu'une DMIA risque d'être dépassée ; validation par la DG.

## 5. Procédure de reprise (PRA) — séquence type

1. Qualifier l'incident et décider de la bascule (cellule de crise).
2. Isoler les systèmes compromis ; en cas de rançongiciel, **ne pas restaurer avant d'avoir identifié et supprimé le vecteur d'attaque**.
3. Restaurer dans l'ordre de priorité du BIA : AD, base du core banking, core banking, passerelle Mobile Money, banque en ligne.
4. Vérifier l'intégrité des données (rapprochement des soldes, journal des transactions Mobile Money).
5. Rouvrir les services progressivement et renforcer la supervision.
6. Organiser le retour sur le site nominal, puis le retour d'expérience sous 15 jours.

## 6. Tests et maintien en condition opérationnelle

| Test | Fréquence |
|---|---|
| Test de restauration des sauvegardes | Trimestriel |
| Exercice sur table (scénario rançongiciel) | Semestriel |
| Bascule réelle sur le site de secours | Annuel |
| Mise à jour du PCA/PRA | Annuelle et après chaque changement majeur |

*Statut actuel : non testé — constat ouvert dans l'audit interne (voir Legrand GRC).*
