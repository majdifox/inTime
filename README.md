# Cahier des Charges : Application Web de Transport - **inTime**

---

## 1. Contexte et Objectifs

Dans un contexte de forte croissance des mobilités urbaines et face à l’évolution des besoins des citoyens en matière de transport, ce projet vise à développer une application web innovante. L’objectif est de proposer une plateforme simple et efficace permettant de connecter des passagers et des chauffeurs pour faciliter les déplacements urbains. Inspirée des modèles comme **Uber** ou **InDrive**, cette application ambitionne d'offrir une solution locale adaptée aux besoins des utilisateurs.

---

## 2. Description Fonctionnelle

### 2.1 Inscription et Connexion
- Les utilisateurs peuvent créer un compte en renseignant un **email** et un **mot de passe**.
- Trois types de profils seront disponibles :
  - **Admin** : Responsable de la gestion globale de la plateforme.
  - **Chauffeur** : Inscrit pour proposer ses services de transport.
  - **Passager** : Inscrit pour demander des trajets.
- Possibilité de se connecter à un compte existant.

### 2.2 Demande de Trajet (Passager)
- Le passager saisit sa **position de départ** et sa **destination** via un formulaire.
- Une **estimation du tarif** est affichée en fonction de la distance.
- La plateforme affiche les **chauffeurs disponibles** à proximité.
- Le passager peut choisir un **chauffeur spécifique** pour effectuer le trajet.

### 2.3 Gestion des Trajets (Chauffeur)
- Les chauffeurs reçoivent les **demandes de trajet** des passagers proches.
- Ils peuvent **accepter** ou **refuser** une demande.
- Une fois le trajet effectué, le chauffeur peut marquer le trajet comme **terminé**.

### 2.4 Historique des Trajets
- **Passager** : Accès à une liste des trajets effectués, avec les détails de chaque trajet (chauffeur, destination, tarif, date).
- **Chauffeur** : Suivi des trajets réalisés, avec des informations similaires.
- **Admin** : Vue globale des trajets effectués sur la plateforme.

### 2.5 Gestion Admin
- Gestion des utilisateurs (**ajout**, **modification**, **suppression**).
- Accès à des **statistiques** sur les trajets, les utilisateurs actifs, et les revenus estimés.
- **Modération** des contenus ou comportements non conformes.

---

## 3. Contraintes

### 3.1 Simplicité et Accessibilité
- L’application doit être **intuitive** pour tous les utilisateurs, quel que soit leur niveau de compétence technique.
- Le design sera **simple**, **clair**, et adapté aux **appareils mobiles** et **ordinateurs**.

### 3.2 Performance et Réactivité
- La plateforme doit être **rapide à charger** et performante même en cas de forte affluence.
- Les opérations critiques (**connexion**, **demande de trajet**) doivent être traitées en **temps réel**.

### 3.3 Confidentialité et Sécurité
- Les données des utilisateurs (**emails**, **mots de passe**, **historiques de trajets**) doivent être protégées.
- Mise en place de mesures pour empêcher l’accès non autorisé et garantir la conformité à la réglementation.

### 3.4 Scalabilité
- La solution doit être conçue pour pouvoir accueillir un **nombre croissant d’utilisateurs** sans perte de qualité.

---

## 4. Planning

### Phase de Conception (15 jours)
- Collecte des besoins des parties prenantes.
- Conception des **maquettes** de l’interface utilisateur.

### Phase de Développement (3 mois)
- Implémentation des **fonctionnalités principales**.
- Mise en place des **bases de données** et des systèmes de gestion des utilisateurs.

### Phase de Tests (15 jours)
- Tests des fonctionnalités par les utilisateurs et correction des **bogues**.
- Validation de la conformité aux attentes.

### Phase de Lancement (10 jours)
- Mise en ligne de l’application.
- Formation des **administrateurs** et support initial aux utilisateurs.

---

**Un projet ambitieux, conçu pour révolutionner les transports urbains et offrir une expérience utilisateur inégalée !**

