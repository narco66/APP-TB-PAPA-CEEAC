# Manuel Utilisateur

## APP-TB-PAPA-CEEAC

Version de demonstration  
Date: 29 mars 2026

## 1. Objet de l'application

APP-TB-PAPA-CEEAC est une application de pilotage, de suivi et de tracabilite du Plan d'Action Prioritaire Annuel de la CEEAC. Elle permet de:

- planifier et suivre les PAPAs;
- piloter les actions prioritaires, objectifs, resultats, activites et taches;
- suivre les indicateurs et leur evolution;
- gerer les budgets et les engagements;
- centraliser les documents justificatifs;
- executer les workflows de validation;
- enregistrer les decisions institutionnelles;
- surveiller les alertes, risques et retards;
- tracer les actions via l'audit metier.

## 2. Acces a l'application

URL locale de demonstration:

`http://127.0.0.1:8000`

Acces espace membre:

- depuis la page d'accueil, cliquer sur `Espace membres`;
- ou ouvrir directement `http://127.0.0.1:8000/login`.

## 3. Comptes de demonstration

Comptes principaux disponibles:

- Super administrateur: `admin@ceeac-eccas.org`
- Mot de passe: `Admin@2025!`

Selon le jeu de donnees de demonstration, d'autres comptes institutionnels existent aussi pour:

- Presidence
- Vice-Presidence
- Secretariat General
- Commissaires
- Directions techniques
- Directions d'appui
- Chefs de service
- Points focaux
- Controle financier
- Agence comptable
- Auditeurs

## 4. Principes de navigation

L'application est organisee autour de:

- un tableau de bord adapte au profil de l'utilisateur;
- une barre laterale de navigation;
- une barre superieure pour les raccourcis, notifications et le profil;
- des ecrans de liste avec filtres;
- des fiches detaillees par objet metier.

Les menus visibles dependent des permissions de l'utilisateur connecte.

## 5. Tableau de bord

Le tableau de bord presente une synthese de pilotage:

- taux global d'execution;
- repartition par departement;
- budgets engages et consommes;
- activites en retard;
- alertes critiques;
- notifications recentes;
- tendances sur les KPI.

Selon le role, certains widgets sont plus orientes:

- Presidence: vue strategique et arbitrage;
- SG: coordination transversale;
- Directions: suivi operationnel;
- Admin: vision complete et administration.

## 6. Gestion des PAPAs

Un PAPA correspond a un plan annuel organise par annee, statut et contenu strategique.

### Cycle de vie

Un PAPA peut passer par plusieurs etats:

- brouillon;
- soumis;
- valide;
- archive.

### Actions possibles

Selon les permissions:

- creer un PAPA;
- consulter la liste;
- ouvrir la fiche detail;
- modifier un PAPA editable;
- soumettre pour validation;
- valider ou rejeter;
- archiver;
- cloner vers une nouvelle annee.

### Fiche PAPA

La fiche d'un PAPA permet notamment de consulter:

- les informations generales;
- les actions prioritaires;
- les workflows associes;
- les decisions associees;
- la tracabilite d'audit;
- l'etat general d'avancement.

## 7. Chaine de resultat

L'application structure le pilotage selon une logique RBM:

- Action prioritaire
- Objectif immediat
- Resultat attendu
- Indicateur
- Activite
- Tache

Chaque niveau peut etre relie au niveau suivant et contribue a la consolidation des taux d'execution.

## 8. Activites, taches et jalons

Les activites representent les actions concretes a executer. Elles peuvent contenir:

- dates prevues;
- statut d'avancement;
- pourcentage de realisation;
- taches associees;
- jalons;
- responsable;
- documents de preuve;
- observations.

Les taches permettent un suivi plus fin de l'execution operationnelle.

## 9. Indicateurs et performance

Les indicateurs servent a mesurer les resultats.

Pour chaque indicateur, l'application peut afficher:

- l'intitule;
- la definition;
- la valeur de reference;
- la cible;
- l'unite;
- la frequence;
- les valeurs periodiques;
- la tendance;
- le niveau de performance.

Les tableaux de bord exploitent ces donnees pour detecter les sous-performances.

## 10. Budget et financement

Le module budgetaire permet de suivre:

- les budgets PAPA;
- les lignes budgetaires;
- les sources de financement;
- les montants prevus;
- les engagements;
- la consommation;
- les ecarts.

Les ecarts budgetaires et les budgets critiques peuvent remonter en alerte.

## 11. Documents GED

Les documents servent de pieces justificatives et de support de tracabilite.

Exemples de documents:

- notes de cadrage;
- rapports techniques;
- proces-verbaux;
- fiches de suivi;
- justificatifs de mission;
- termes de reference;
- comptes rendus;
- pieces de validation.

Bonnes pratiques:

- joindre des documents pertinents a l'objet metier concerne;
- utiliser des titres explicites;
- verifier le statut du document;
- completer les commentaires utiles.

## 12. Workflow de validation

Le workflow permet de tracer les validations institutionnelles.

Fonctions disponibles:

- demarrer un workflow;
- consulter les etapes;
- approuver;
- rejeter avec motif;
- commenter;
- consulter l'historique des actions.

Chaque transition de workflow peut produire:

- une entree d'audit;
- une notification;
- un changement d'etat metier.

## 13. Decisions institutionnelles

Le module decisions permet d'enregistrer:

- arbitrages;
- validations;
- orientations;
- reaffectations budgetaires;
- reports;
- suspensions.

Une decision peut etre liee a:

- un PAPA;
- une action prioritaire;
- une activite;
- un budget;
- un document justificatif.

Une decision peut suivre un cycle tel que:

- brouillon;
- soumise;
- validee;
- executee;
- archivee.

## 14. Alertes et risques

L'application signale les situations sensibles comme:

- retard d'activite;
- sous-performance d'indicateur;
- absence de document de preuve;
- budget critique;
- blocage de validation.

Selon les permissions, l'utilisateur peut:

- consulter les alertes;
- les filtrer;
- les marquer comme vues;
- les resoudre.

## 15. Notifications

Les notifications informent l'utilisateur des evenements importants:

- workflow demarre;
- workflow rejete;
- decision validee;
- evenement critique;
- changement d'etat metier.

Fonctions disponibles:

- consulter la liste;
- filtrer `non lues` ou `lues`;
- marquer une notification comme lue;
- tout marquer comme lu.

## 16. Audit metier

Le journal d'audit metier conserve la trace des actions critiques.

L'ecran d'audit permet:

- de filtrer par module;
- de filtrer par niveau `info`, `warning`, `critical`;
- d'exporter en CSV;
- de voir l'objet source;
- de voir le PAPA associe;
- de revenir vers l'objet audite.

L'audit couvre notamment:

- les transitions de workflow;
- les decisions;
- certaines operations sur les PAPAs;
- les evenements critiques.

## 17. Administration

Le profil administrateur peut notamment:

- gerer les roles et permissions;
- consulter l'audit metier;
- consulter et modifier les regles de notification;
- acceder a une vue transversale de l'application.

## 18. Exports et filtres

Plusieurs ecrans de liste disposent de filtres. L'audit metier dispose en plus d'un export CSV.

Conseils:

- filtrer avant export pour produire un resultat plus lisible;
- verifier le contexte actif avant d'interpreter les chiffres;
- utiliser les raccourcis de module et de niveau pour gagner du temps.

## 19. Bonnes pratiques utilisateur

- travailler avec des statuts coherents;
- renseigner les motifs en cas de rejet;
- joindre les pieces justificatives utiles;
- consulter les notifications regulierement;
- verifier l'audit en cas d'anomalie de parcours;
- ne pas modifier un PAPA archive;
- privilegier le clonage pour preparer un nouveau cycle annuel.

## 20. Procedure rapide de demonstration

Scenario recommande:

1. Se connecter avec `admin@ceeac-eccas.org`.
2. Ouvrir le tableau de bord.
3. Consulter la liste des PAPAs.
4. Ouvrir un PAPA puis afficher son workflow.
5. Consulter les decisions associees.
6. Ouvrir les notifications.
7. Ouvrir l'audit metier et tester les filtres.
8. Exporter l'audit en CSV.

## 21. Depannage courant

### Je ne vois pas un menu

Verifier le role et les permissions du compte utilise.

### Une action est refusee

Certaines operations sont reservees a des profils specifiques ou a des objets dans un statut compatible.

### Un PAPA n'est pas modifiable

Un PAPA archive ou verrouille n'est pas editable.

### Je ne recois pas les memes ecrans qu'un autre utilisateur

Le tableau de bord et les menus s'adaptent au profil.

## 22. Support

Pour une demonstration locale:

- verifier que le serveur Laravel est demarre;
- verifier que la base de donnees est migree et peuplee;
- utiliser un compte disposant des permissions attendues.

---

Manuel genere pour APP-TB-PAPA-CEEAC.
