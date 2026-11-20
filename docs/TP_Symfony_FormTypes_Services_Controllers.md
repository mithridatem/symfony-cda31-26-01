# TP Symfony — FormTypes, Services et Controllers

**Public :** CDA Bac+3/4  
**Projet de départ :** [symfony-cda31-26-01](https://github.com/mithridatem/symfony-cda31-26-01) — branche `main`  
**Durée indicative :** 4 à 6 heures, selon les bonus  
**Objectif :** créer des fonctionnalités complètes à partir des entités existantes, en séparant la gestion HTTP, la logique métier et la persistance.

## Contexte

L'application de bibliothèque permet déjà de consulter des livres et des catégories. L'ajout d'une catégorie avec `CategoryType`, `CategoryController` et `CategoryService` a été réalisé en cours : **ne le refaites pas**. Servez-vous-en comme exemple.

Le projet dispose notamment des entités :

- `Category` : `id`, `name` ;
- `Book` : `id`, `title`, `resume`, `publishAt`, `author`, `categories` (ManyToMany), `user` (ManyToOne, facultatif) ;
- `User` : `id`, `email`, `password`, `firstname`, `lastname`, `roles`.

Les classes `BookController`, `CategoryController`, `BookService`, `CategoryService`, `CategoryType` et `RegistrationFormType` existent. Les vues Twig des listes et des détails existent également. **Vous ne devez pas créer de nouvelles entités pour ce TP.**

## Compétences travaillées

- Construire et utiliser une classe `FormType` liée à une entité.
- Récupérer et traiter un formulaire dans un Controller (`Request`, `createForm`, `handleRequest`, `isSubmitted`, `isValid`).
- Mettre les opérations métier et la persistance dans un Service.
- Utiliser les Repository et Doctrine à bon escient.
- Transmettre les données à une vue avec `render()` et afficher des formulaires avec Twig.
- Rediriger après succès et afficher un message flash.
- Réutiliser des formulaires pour la création et la modification.

## Consignes communes

1. **Ne pas faire de `persist()` ou `flush()` dans les Controllers.** Les Controllers orchestrent la requête HTTP ; les Services prennent en charge les opérations métier et les écritures en base.
2. Utiliser les services et les méthodes existants lorsqu'ils conviennent, et les enrichir seulement si nécessaire.
3. Pour tout formulaire, traiter l'envoi uniquement si `isSubmitted()` **et** `isValid()` valent `true`.
4. Après une écriture réussie, afficher un message flash et **rediriger** vers une route appropriée.
5. Conserver les templates et les conventions de l'application. Vous pouvez ajouter des liens dans la navigation ou les pages de liste.
6. Ne pas utiliser `make:crud` : l'objectif est de comprendre les responsabilités de chaque couche.
7. Vérifier le fonctionnement avec une saisie valide, une saisie invalide et un accès direct à une URL incorrecte.
8. **Pour les actions de gestion**, conserver une restriction `ROLE_ADMIN` cohérente avec l'exemple d'ajout d'une catégorie. La modification du profil concerne uniquement l'utilisateur connecté.

> **Point d'attention sur l'exemple du cours :** dans `CategoryController::new()`, le traitement teste actuellement seulement `isSubmitted()`. Pour ce TP, utilisez aussi `isValid()` avant d'enregistrer. Vous pouvez corriger l'exemple après l'avoir relu.

---

## Exercice 1 — Modifier une catégorie

**Niveau : facile | 25–35 min**

### Objectif

Réutiliser un `FormType` déjà existant pour modifier un objet récupéré depuis la base de données.

### Travail demandé

1. Dans `CategoryController`, créer une nouvelle action associée à `GET|POST /category/{id}/edit` ; nom de route suggéré : `app_category_edit`.
2. Récupérer la catégorie grâce à `CategoryService::getCategoryById()`.
3. Si la catégorie n'existe pas, retourner une réponse HTTP **404**.
4. Réutiliser `CategoryType` pour afficher le nom actuel de la catégorie.
5. Récupérer la soumission du formulaire et vérifier sa validité.
6. Ajouter dans `CategoryService` une méthode permettant d'enregistrer la modification (ou expliciter comment réutiliser proprement `saveCategory()`).
7. Afficher un message de confirmation et rediriger vers la liste des catégories.
8. Créer `templates/category/category_edit.html.twig` et placer un lien « Modifier » depuis la liste des catégories.

### Résultat attendu

Une catégorie créée précédemment peut être modifiée. Son nom est prérempli, la modification est conservée après redirection et un identifiant inconnu affiche une 404.

**Indices :** `createNotFoundException()`, `createForm(CategoryType::class, $category)`, `render()`, `redirectToRoute()`.

**Bonus :** adapter le libellé du bouton pour afficher « Modifier » au lieu de « Ajouter », sans dupliquer tout le formulaire.

---

## Exercice 2 — Ajouter un livre avec un formulaire

**Niveau : intermédiaire | 40–50 min**

### Objectif

Créer un `FormType` complet et ajouter la logique d'enregistrement dans `BookService`.

### Travail demandé

1. Créer `src/Form/BookType.php`, associé à l'entité `Book`.
2. Ajouter les champs suivants (sans les relations pour l'instant) :

   | Propriété | Type de champ suggéré |
   | --- | --- |
   | `title` | `TextType` |
   | `author` | `TextType` |
   | `resume` | `TextareaType` |
   | `publishAt` | `DateType` |

3. Configurer correctement la propriété `data_class`.
4. Dans `BookService`, ajouter une méthode `saveBook(Book $book): void` réalisant la persistance via `EntityManagerInterface`.
5. Dans `BookController`, créer `GET|POST /books/new` ; nom de route suggéré : `app_book_new`.
6. Instancier un livre, créer le formulaire, traiter la requête, vérifier la soumission et la validité.
7. Appeler le Service, afficher un flash et rediriger vers la liste des livres.
8. Créer `templates/book/book_form.html.twig` et un lien « Ajouter un livre » dans la liste.
9. Restreindre l'accès à cette action à `ROLE_ADMIN`.

### Résultat attendu

Le formulaire permet d'ajouter un livre avec un titre, un auteur, un résumé et une date de publication. Le livre apparaît ensuite dans la liste et dans sa page de détail.

**Indices :** `make:form` (facultatif), `DateType`, `EntityManagerInterface`, `form_start`, `form_row`, `form_end`.

**Attention :** `publishAt` attend un `DateTimeImmutable` dans `Book`. Vérifiez la configuration et la transformation de la date par Symfony.

---

## Exercice 3 — Associer des catégories au livre

**Niveau : intermédiaire | 30–40 min**

### Objectif

Compléter le formulaire du livre pour manipuler la relation **ManyToMany** entre `Book` et `Category`.

### Travail demandé

1. Reprendre `BookType` créé à l'exercice 2.
2. Ajouter un champ `categories` basé sur `EntityType`.
3. Configurer la classe cible (`Category`) et le champ utilisé comme libellé (`name`).
4. Autoriser la sélection de plusieurs catégories.
5. Vérifier que les catégories choisies sont bien enregistrées en base lors de la création d'un livre.
6. Afficher les catégories du livre dans `show_book_id.html.twig`.
7. Tester la création d'un livre ayant zéro, une et plusieurs catégories.

### Résultat attendu

Le formulaire affiche les catégories déjà disponibles en base. Plusieurs catégories peuvent être sélectionnées, sauvegardées et retrouvées dans le détail du livre.

**Indices :** `EntityType`, `class`, `choice_label`, `multiple`, `expanded`, `by_reference` ; méthodes `addCategory()` et `removeCategory()` déjà présentes dans l'entité.

**Bonus :** choisir entre une liste déroulante multiple et des cases à cocher, puis justifier le choix d'ergonomie.

---

## Exercice 4 — Modifier un livre existant

**Niveau : intermédiaire | 30–40 min**

### Objectif

Réutiliser le même formulaire en **création** et en **modification**.

### Travail demandé

1. Ajouter `GET|POST /books/{id}/edit` dans `BookController` ; nom de route suggéré : `app_book_edit`.
2. Récupérer le livre existant via `BookService::getBookById()` et retourner 404 si nécessaire.
3. Réutiliser **le même `BookType`** et, si possible, **le même template** que pour l'ajout.
4. Préremplir les champs avec les valeurs enregistrées, y compris les catégories.
5. Enregistrer les modifications via `BookService`.
6. Ajouter un lien « Modifier » sur la page de détail du livre.
7. Afficher un flash puis rediriger vers le détail du livre.
8. Restreindre l'accès à `ROLE_ADMIN`.

### Résultat attendu

Un livre existant peut être modifié sans création d'un doublon. Les nouvelles valeurs et les catégories sont visibles après redirection.

**Question de réflexion :** pourquoi une même méthode de sauvegarde peut-elle, sous certaines conditions, servir à la création et à la modification d'une entité Doctrine ?

---

## Exercice 5 — Supprimer un livre avec confirmation

**Niveau : avancé | 35–45 min**

### Objectif

Réaliser une suppression en respectant la séparation des responsabilités et les protections HTTP usuelles.

### Travail demandé

1. Dans `BookService`, créer une méthode `deleteBook(Book $book): void`.
2. Dans `BookController`, ajouter une route `POST /books/{id}/delete` ; nom suggéré : `app_book_delete`.
3. Charger le livre via le Service, puis retourner 404 s'il n'existe pas.
4. Dans la page de détail, afficher un petit formulaire HTML avec un bouton « Supprimer » et une confirmation utilisateur.
5. Utiliser un **jeton CSRF** pour protéger l'action. Le vérifier côté Controller avant d'appeler le Service.
6. Après suppression, afficher un message flash et rediriger vers la liste.
7. Restreindre l'accès à `ROLE_ADMIN`.
8. Vérifier qu'une requête `GET` ne permet jamais de supprimer un livre.

### Résultat attendu

La suppression fonctionne uniquement via un `POST` autorisé et protégé par un jeton CSRF valide. Un livre supprimé ne figure plus dans la liste.

**Indices :** `remove()`, `flush()`, `csrf_token()`, `isCsrfTokenValid()`.

**Bonus :** afficher une confirmation côté navigateur, sans considérer cette confirmation JavaScript comme une protection de sécurité.

---

## Exercice 6 — Modifier son profil utilisateur

**Niveau : avancé | 40–50 min**

### Objectif

Créer un formulaire de modification de profil et introduire un Service dédié à l'entité `User`.

### Travail demandé

1. Créer `src/Service/UserService.php`, avec une méthode de sauvegarde d'un utilisateur.
2. Créer `src/Form/ProfileType.php`, associé à l'entité `User`.
3. Ajouter uniquement les champs `firstname` et `lastname`.
4. **Ne pas exposer** les propriétés `roles`, `password` ou `id` dans ce formulaire.
5. Dans un Controller dédié ou existant, ajouter `GET|POST /profile/edit`.
6. Exiger que l'utilisateur soit authentifié (`ROLE_USER`).
7. Récupérer **l'utilisateur connecté**, plutôt qu'un identifiant fourni dans l'URL.
8. Créer le formulaire prérempli, traiter la soumission valide, puis appeler `UserService`.
9. Afficher un flash et rediriger vers la page de profil, à créer si nécessaire.
10. Ajouter un lien « Mon profil » dans la navigation lorsque l'utilisateur est connecté.

### Résultat attendu

Un utilisateur connecté peut modifier son prénom et son nom. Il ne peut pas modifier les informations d'un autre utilisateur ni ses propres rôles via ce formulaire.

**Indices :** `getUser()`, `#[IsGranted('ROLE_USER')]`, `createForm()`, `UserService`.

**Question de réflexion :** pourquoi n'est-il pas souhaitable de reprendre intégralement `RegistrationFormType` pour modifier un profil ?

---

## Vérifications à réaliser pour chaque exercice

- [ ] La route est présente dans `php bin/console debug:router`.
- [ ] Le formulaire s'affiche correctement et préremplit les données lors d'une modification.
- [ ] Le traitement n'a lieu que si le formulaire est soumis **et valide**.
- [ ] L'opération de persistance est dans le Service, pas dans le Controller.
- [ ] La vue utilise `render()` et reçoit uniquement les données nécessaires.
- [ ] La réussite entraîne une redirection et un message flash.
- [ ] L'identifiant inexistant renvoie une 404 (lorsqu'une route contient un `id`).
- [ ] Les restrictions d'accès fonctionnent pour les routes concernées.

## Livrables

- Le code PHP et les templates Twig nécessaires aux exercices réalisés.
- Une application fonctionnelle avec les nouvelles routes.
- Un commit Git par exercice (ou par fonctionnalité), avec un message explicite.
- Un court compte rendu listant les exercices terminés, les difficultés rencontrées et les tests manuels réalisés.

## Défi facultatif — Faire évoluer l'inscription

Dans le projet initial, `RegistrationController` utilise directement `EntityManagerInterface` pour enregistrer un utilisateur et `RegistrationFormType` propose un formulaire d'inscription. **Après les six exercices**, déplacer la partie enregistrement et hachage du mot de passe dans un Service adapté, tout en conservant la validation, la gestion HTTP et la redirection dans le Controller. Vérifier que les mots de passe ne sont jamais stockés en clair.

> **Pour aller plus loin :** expliquer oralement quelles tâches appartiennent au `FormType`, au Controller, au Service, au Repository et à Twig, en prenant comme exemple l'ajout d'un livre.
