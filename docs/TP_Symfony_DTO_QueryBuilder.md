# TP Symfony — DTO et requêtes personnalisées avec Doctrine QueryBuilder

**Public :** Bac+3/4 — développement web  
**Durée indicative :** 3 à 4 heures  
**Niveau :** progressif  
**Support :** diagramme de classes `Book`, `Category`, `User` fourni en cours

## Contexte

L'application gère des livres, leurs catégories et l'utilisateur éventuellement associé à chaque livre. Le diagramme présente :

- **Book** : `id`, `title`, `resume`, `publishAt`, `author`, `categories`, `user` ;
- **Category** : `id`, `name` ;
- **User** : `id`, `email`, `password`, `firstname`, `lastname`.

Un livre peut appartenir à plusieurs catégories et une catégorie peut contenir plusieurs livres (*ManyToMany*). Un livre peut être associé à zéro ou un utilisateur ; un utilisateur peut être associé à plusieurs livres.

> **Prérequis :** les entités, leurs relations, les migrations, les Repository et les templates Twig sont déjà fonctionnels. Adapter les noms de propriétés si l'implémentation du projet diffère du diagramme (`publishAt` notamment).

## Objectifs pédagogiques

À l'issue du TP, vous saurez :

- écrire des méthodes de recherche personnalisées dans les Repository ;
- utiliser `createQueryBuilder()`, `where()`, `andWhere()`, `setParameter()`, `orderBy()` et les jointures ;
- distinguer une entité Doctrine d'un DTO ;
- créer des DTO avec des propriétés typées ;
- transformer des entités en DTO ;
- encapsuler les critères d'une recherche dans un DTO ;
- afficher les résultats dans des templates Twig.

## Règles communes

- Écrire les requêtes Doctrine **dans les Repository**, pas dans les contrôleurs.
- Utiliser des paramètres liés (`setParameter()`) pour les valeurs provenant de l'utilisateur.
- Respecter les cardinalités du diagramme.
- Gérer les résultats vides et les associations facultatives.
- Ne pas modifier les entités uniquement pour faciliter l'affichage.
- Pour chaque exercice, tester au minimum un cas avec résultat et un cas sans résultat.

---

# Partie 1 — Requêtes personnalisées

## Exercice 1 — Rechercher des livres par titre

**Niveau : facile — 20 min**

**Objectif :** rechercher les livres dont le titre contient une chaîne de caractères.

### Travail demandé

1. Dans `BookRepository`, créer `findByTitle(string $title): array`.
2. Construire une requête avec `createQueryBuilder()`.
3. Utiliser `LIKE` et un paramètre nommé.
4. Trier les résultats par titre croissant.
5. Créer la route `GET /books/search` recevant le paramètre `title`.
6. Afficher les résultats dans un template Twig.

**Exemple :** `GET /books/search?title=Symfony`

**Résultat attendu :** afficher tous les livres dont le titre contient « Symfony ».

**Indices :** `where()`, `setParameter()`, `orderBy()`, `getQuery()`, `getResult()`.

## Exercice 2 — Filtrer par date de publication

**Niveau : facile — 20 min**

**Objectif :** afficher les livres publiés à partir d'une date donnée.

### Travail demandé

1. Dans `BookRepository`, créer `findPublishedAfter(\DateTimeInterface $date): array`.
2. Filtrer sur la propriété `publishAt` du diagramme.
3. Trier les résultats par date décroissante.
4. Créer la route `GET /books/recent` avec le paramètre `date` au format `AAAA-MM-JJ`.
5. Afficher le titre, l'auteur et la date de publication dans Twig.
6. Prévoir un traitement adapté si la date fournie est invalide.

**Exemple :** `GET /books/recent?date=2025-01-01`

**Bonus :** limiter l'affichage aux dix livres les plus récents.

## Exercice 3 — Rechercher par catégorie

**Niveau : intermédiaire — 30 min**

**Objectif :** exploiter la relation ManyToMany entre `Book` et `Category`.

### Travail demandé

1. Dans `BookRepository`, créer `findByCategory(int $categoryId): array`.
2. Utiliser `innerJoin()` pour joindre les catégories.
3. Filtrer sur l'identifiant de la catégorie.
4. Créer une route `GET /books/category/{id}`.
5. Afficher les livres correspondants, ainsi que leurs catégories, dans Twig.

**Exemple :** `GET /books/category/3`

**Bonus :** permettre une recherche sur plusieurs catégories.

---

# Partie 2 — Introduction aux DTO

## Exercice 4 — Créer un DTO de présentation

**Niveau : facile — 25 min**

**Objectif :** transmettre à la vue uniquement les données nécessaires à l'affichage d'un livre.

### DTO attendu

Créer `src/Dto/BookListDto.php` contenant :

| Propriété | Type |
| --- | --- |
| `id` | `int` |
| `title` | `string` |
| `author` | `string` |
| `publishAt` | `DateTimeInterface` |

Voici une structure de départ :

```php
<?php

namespace App\Dto;

final readonly class BookListDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public \DateTimeInterface $publishAt,
    ) {}
}
```

### Travail demandé

1. Créer le DTO.
2. Ajouter une méthode statique `fromEntity(Book $book): self` pour convertir une entité `Book` en DTO.
3. Dans un contrôleur, récupérer les livres à l'aide du Repository.
4. Transformer les entités en une collection de `BookListDto`.
5. Envoyer les DTO au template Twig et afficher leurs informations.

**Question de réflexion :** quel intérêt présente ce DTO par rapport à l'envoi direct des entités Doctrine à Twig ?

## Exercice 5 — DTO et relations entre entités

**Niveau : intermédiaire — 30 min**

**Objectif :** construire une représentation détaillée d'un livre avec ses relations.

### DTO attendu

Créer `BookDetailDto` :

| Propriété | Type |
| --- | --- |
| `id` | `int` |
| `title` | `string` |
| `resume` | `string` |
| `author` | `string` |
| `categories` | `array` |
| `userEmail` | `?string` |

### Travail demandé

1. Créer `BookDetailDto`.
2. Récupérer la liste des noms des catégories du livre.
3. Récupérer l'adresse email de l'utilisateur associé, s'il existe.
4. Créer une méthode de conversion à partir de l'entité `Book`.
5. Afficher le DTO dans une page `GET /books/{id}`.
6. Gérer le cas où le livre demandé n'existe pas.

**Attention :** l'association à `User` est facultative. Le DTO doit accepter `null` pour `userEmail`.

**Bonus :** créer un `CategoryDto` plutôt que d'utiliser un simple tableau de chaînes de caractères.

---

# Partie 3 — Combiner DTO et QueryBuilder

## Exercice 6 — Recherche multicritère avec un DTO

**Niveau : intermédiaire — 40 min**

**Objectif :** centraliser les paramètres d'une recherche avancée dans un DTO et construire une requête dynamique.

### DTO attendu

Créer `src/Dto/BookSearchDto.php` :

```php
<?php

namespace App\Dto;

final readonly class BookSearchDto
{
    public function __construct(
        public ?string $title = null,
        public ?string $author = null,
        public ?int $categoryId = null,
        public ?\DateTimeImmutable $publishedAfter = null,
    ) {}
}
```

### Travail demandé

1. Créer `BookSearchDto`.
2. Dans `BookRepository`, créer `search(BookSearchDto $criteria): array`.
3. Construire le QueryBuilder progressivement selon les critères renseignés.
4. Ne pas ajouter de condition pour un critère absent.
5. Utiliser `setParameter()` pour chaque valeur de filtrage.
6. Créer un formulaire de recherche utilisant la méthode HTTP `GET`.
7. Afficher les livres correspondant **à tous les critères fournis**.

**Exemple :** `GET /books/advanced-search?title=PHP&author=Martin&categoryId=2`

**Pour aller plus loin :** étudier `#[MapQueryString]` pour convertir les paramètres GET en DTO, puis ajouter des contraintes de validation adaptées.

## Exercice 7 — Statistiques par catégorie

**Niveau : intermédiaire — 30 min**

**Objectif :** produire des statistiques avec QueryBuilder et les transmettre dans des DTO.

### Travail demandé

1. Dans `CategoryRepository`, créer `countBooksByCategory(): array`.
2. Joindre les livres associés aux catégories.
3. Utiliser `COUNT()` et `GROUP BY`.
4. Prévoir les catégories ne contenant aucun livre.
5. Créer `CategoryStatsDto` avec `categoryName: string` et `bookCount: int`.
6. Transformer les résultats de la requête en DTO.
7. Afficher un tableau trié par nombre de livres décroissant.

**Exemple illustratif :**

| Catégorie | Nombre de livres |
| --- | ---: |
| Développement web | 12 |
| Bases de données | 8 |
| Architecture logicielle | 5 |

**Question :** pourquoi une jointure externe peut-elle être utile pour cet exercice ?

## Exercice 8 — Recherche avancée avec pagination

**Niveau : avancé — 40 min**

**Objectif :** améliorer la recherche de l'exercice 6 pour traiter une collection volumineuse.

### Travail demandé

1. Ajouter `page` et `limit` au DTO de recherche.
2. Valider les valeurs : `page >= 1` et `1 <= limit <= 50`.
3. Utiliser `setFirstResult()` et `setMaxResults()`.
4. Calculer le nombre total de résultats correspondant aux critères.
5. Créer `PaginatedBooksDto` contenant :
   - la liste des livres ;
   - la page courante ;
   - le nombre total de résultats ;
   - le nombre total de pages.
6. Afficher une navigation entre les pages dans Twig.
7. Conserver les critères de recherche lors du changement de page.

**Bonus :** ajouter un tri dynamique sur le titre ou la date de publication. Utiliser une liste fermée de champs autorisés, sans injecter directement un nom de colonne reçu depuis la requête HTTP.

**Point d'attention :** la pagination de requêtes Doctrine comportant des jointures sur des collections peut nécessiter un traitement spécifique pour éviter les doublons et obtenir un comptage correct.

---

# Synthèse

| Exercice | Notion principale | Niveau |
| --- | --- | --- |
| 1 | `LIKE`, `WHERE`, `ORDER BY` | Facile |
| 2 | Comparaison de dates | Facile |
| 3 | Jointures ManyToMany | Intermédiaire |
| 4 | DTO de sortie | Facile |
| 5 | DTO et associations | Intermédiaire |
| 6 | DTO d'entrée et filtres dynamiques | Intermédiaire |
| 7 | Agrégations, `COUNT`, `GROUP BY` | Intermédiaire |
| 8 | Pagination et validation | Avancé |

## Questions de bilan

1. Quelle est la responsabilité d'un Repository Doctrine ?
2. Quelle différence y a-t-il entre une entité et un DTO ?
3. Pourquoi utiliser `setParameter()` ?
4. Quelle différence y a-t-il entre `innerJoin()` et `leftJoin()` ?
5. Pourquoi peut-il être pertinent de distinguer un DTO de recherche d'un DTO de présentation ?
6. Quels problèmes faut-il anticiper avec la pagination et les relations ManyToMany ?

## Ressources

- [Symfony — Doctrine](https://symfony.com/doc/7.4/doctrine.html)
- [Symfony — Controllers](https://symfony.com/doc/7.4/controller.html)
- [Doctrine ORM — QueryBuilder](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/query-builder.html)

**Parcours conseillé :** exercices 1 à 6 pour tous ; exercices 7 et 8 en approfondissement pour les plus avancés.
