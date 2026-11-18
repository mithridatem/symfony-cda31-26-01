# TP Symfony — Découvrir les Twig Components

**Public :** Licence Bac+3/4 — Développement web  
**Durée indicative :** 2 h 30 à 3 h  
**Prérequis :** routes Symfony, contrôleurs, `render()`, templates Twig, héritage avec `base.html.twig`, boucles et conditions Twig.  
**Objectif général :** construire une interface réutilisable à l'aide de Symfony UX TwigComponent.

> **Consigne générale :** travaillez dans votre projet Symfony existant. Les exercices sont progressifs : conservez votre code d'un exercice à l'autre. Les extraits servent de points de départ, pas de corrections complètes.

## Préparation

Installer le composant Symfony UX :

```bash
composer require symfony/ux-twig-component
```

Vérifier que le projet contient les fichiers suivants (les créer si nécessaire) :

```text
src/
  Controller/
templates/
  base.html.twig
  components/
```

Avec Symfony Flex, la configuration habituelle permet de découvrir automatiquement les composants sous `templates/components/` et les classes PHP sous `src/Twig/Components/`.

**Rappel :** un *Twig Component* est un fragment d'interface réutilisable. Il peut être **anonyme** (uniquement un template Twig) ou être associé à une **classe PHP**. Les données fournies à un composant sont appelées des **props**.

---

## Exercice 1 — Une navbar réutilisable (niveau 1)

**Objectif :** créer un premier composant sans classe PHP, puis l'afficher dans toutes les pages.

### Travail demandé

1. Vérifiez que votre application possède trois routes nommées `app_home`, `app_about` et `app_contact` (créez les routes et leurs pages si nécessaire).
2. Créez le fichier `templates/components/Navbar.html.twig`.
3. Ajoutez une balise `<nav>` contenant les liens « Accueil », « À propos » et « Contact ».
4. Utilisez `path()` pour générer les URL des trois routes.
5. Appelez le composant une seule fois dans `templates/base.html.twig`, au-dessus du bloc `body`.
6. Vérifiez que la navbar apparaît sur les trois pages.

**Point de départ :**

```twig
{# templates/components/Navbar.html.twig #}
<nav aria-label="Navigation principale">
    <a href="{{ path('app_home') }}">Accueil</a>
    {# Ajouter les deux autres liens #}
</nav>
```

```twig
{# Dans templates/base.html.twig, à l'intérieur de <body> #}
<twig:Navbar />

{% block body %}{% endblock %}
```

**Critère de réussite :** une modification apportée à `Navbar.html.twig` s'applique automatiquement sur toutes les pages.

**Questions :** pourquoi placer la navbar dans `base.html.twig` ? Quelle différence voyez-vous entre un composant et du HTML copié dans chaque page ?

---

## Exercice 2 — Une carte avec des props (niveau 2)

**Objectif :** rendre un composant configurable grâce aux propriétés passées par son parent.

### Travail demandé

1. Créez `templates/components/InfoCard.html.twig`.
2. Déclarez deux props obligatoires : `title` et `description`.
3. Affichez le titre dans un `<h2>` et la description dans un `<p>`.
4. Dans la page d'accueil, utilisez le composant **trois fois** avec trois contenus différents.
5. Modifiez la mise en forme du composant : les trois cartes doivent être mises à jour sans modifier la page d'accueil.

**Point de départ :**

```twig
{# templates/components/InfoCard.html.twig #}
{% props title, description %}

<article>
    <h2>{{ title }}</h2>
    <p>{{ description }}</p>
</article>
```

```twig
{# Exemple dans templates/home/index.html.twig #}
<twig:InfoCard
    title="Symfony"
    description="Un framework PHP pour construire des applications web."
/>
```

**Critère de réussite :** les trois cartes partagent la même structure HTML mais affichent chacune un contenu différent.

**Question :** que se passe-t-il si vous oubliez la prop obligatoire `description` ? Essayez et expliquez l'erreur obtenue.

---

## Exercice 3 — Des props facultatives avec valeur par défaut (niveau 2)

**Objectif :** distinguer une prop obligatoire d'une prop facultative.

### Travail demandé

1. Créez `templates/components/Alert.html.twig`.
2. Déclarez une prop obligatoire `message`.
3. Déclarez une prop `type` avec la valeur par défaut `info`.
4. Affichez un message dans une balise `<div>` dont la classe CSS dépend de `type`.
5. Affichez trois alertes sur une page : information, succès et erreur.
6. Pour l'alerte d'information, ne fournissez pas la prop `type`.

**Point de départ :**

```twig
{# templates/components/Alert.html.twig #}
{% props message, type = 'info' %}

<div class="alert alert-{{ type }}" role="alert">
    {{ message }}
</div>
```

```twig
<twig:Alert message="Bienvenue sur le site !" />
<twig:Alert message="Enregistrement réussi." type="success" />
<twig:Alert message="Une erreur est survenue." type="danger" />
```

**À réaliser :** ajoutez des styles CSS pour distinguer visuellement les trois types d'alerte. Vous pouvez utiliser du CSS simple, sans framework graphique.

**Critère de réussite :** la première alerte adopte bien le type `info` sans que la prop soit explicitement transmise.

---

## Exercice 4 — Alimenter des composants depuis un contrôleur (niveau 3)

**Objectif :** faire circuler les données du contrôleur jusqu'aux composants affichés par une boucle Twig.

### Travail demandé

1. Dans l'action associée à la page d'accueil, créez un tableau PHP contenant au moins quatre formations. Chaque formation possède `title` et `description`.
2. Transmettez le tableau au template via `render()`.
3. Parcourez ce tableau avec une boucle `{% for %}`.
4. Pour chaque formation, affichez un composant `<twig:InfoCard />`.
5. Ajoutez une cinquième formation dans le tableau PHP : elle doit apparaître sans modifier le template Twig.

**Exemple de données PHP :**

```php
$formations = [
    [
        'title' => 'Développement PHP',
        'description' => 'Découvrir le développement côté serveur.',
    ],
    [
        'title' => 'Symfony',
        'description' => 'Structurer une application web en PHP.',
    ],
    // Compléter avec au moins deux autres formations.
];

return $this->render('home/index.html.twig', [
    'formations' => $formations,
]);
```

**Indice sur la syntaxe des props dynamiques :**

```twig
{% for formation in formations %}
    <twig:InfoCard
        :title="formation.title"
        :description="formation.description"
    />
{% endfor %}
```

Le préfixe `:` indique que la valeur est une **expression Twig**, et non une chaîne de caractères fixe.

**Critère de réussite :** les données restent définies dans le contrôleur, alors que le composant se charge uniquement de leur affichage.

**Question :** quel est le rôle respectif du contrôleur, du template de page et du composant `InfoCard` ?

---

## Exercice 5 — Un composant associé à une classe PHP (niveau 3)

**Objectif :** découvrir `#[AsTwigComponent]` et les propriétés PHP typées.

### Travail demandé

1. Créez le dossier `src/Twig/Components/` s'il n'existe pas.
2. Créez une classe PHP `Badge` dans ce dossier.
3. Ajoutez l'attribut `#[AsTwigComponent]` à la classe.
4. Déclarez une propriété publique `label` de type `string`, sans valeur par défaut.
5. Déclarez une propriété publique `variant` de type `string`, avec la valeur par défaut `secondary`.
6. Créez le template associé `templates/components/Badge.html.twig`.
7. Affichez plusieurs badges avec des valeurs différentes.

**Point de départ PHP :**

```php
<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Badge
{
    public string $label;

    public string $variant = 'secondary';
}
```

**Point de départ Twig :**

```twig
{# templates/components/Badge.html.twig #}
<span class="badge badge-{{ variant }}">
    {{ label }}
</span>
```

**Exemples d'utilisation :**

```twig
<twig:Badge label="Débutant" />
<twig:Badge label="Intermédiaire" variant="primary" />
<twig:Badge label="Avancé" variant="success" />
```

**Critère de réussite :** un même composant est réutilisé avec des valeurs différentes, et les props sont déclarées dans la classe PHP.

**Questions :** où déclare-t-on les props dans un composant anonyme ? Et dans un composant associé à une classe PHP ? Quel avantage apporte le typage des propriétés ?

---

## Exercice 6 — Une navbar dynamique et des composants imbriqués (niveau 4)

**Objectif :** rendre la navigation configurable et assembler plusieurs composants pour construire une page.

### Partie A — Navbar configurable

1. Modifiez votre composant anonyme `Navbar` pour recevoir une prop facultative `links` dont la valeur par défaut est un tableau vide.
2. Remplacez les liens codés en dur par une boucle `{% for %}`.
3. Pour chaque élément, utilisez son `label` comme texte et son `route` comme nom de route Symfony.
4. Transmettez le tableau des liens depuis `base.html.twig`.
5. Vérifiez que l'ajout d'un lien ne nécessite plus de modification de `Navbar.html.twig`.

**Structure de données attendue :**

```twig
{% set navigation = [
    {label: 'Accueil', route: 'app_home'},
    {label: 'À propos', route: 'app_about'},
    {label: 'Contact', route: 'app_contact'}
] %}

<twig:Navbar :links="navigation" />
```

**Indice pour le composant :**

```twig
{% props links = [] %}

<nav aria-label="Navigation principale">
    {% for link in links %}
        <a href="{{ path(link.route) }}">{{ link.label }}</a>
    {% endfor %}
</nav>
```

### Partie B — Composer l'interface

1. Enrichissez la liste des formations avec une propriété `level`.
2. Faites évoluer `InfoCard` pour recevoir une prop `level`, facultative ou obligatoire selon votre choix.
3. **Imbriquez** le composant `<twig:Badge />` dans le template de `InfoCard` pour afficher le niveau.
4. Affichez sur la page d'accueil le titre de la page, les cartes de formation et une alerte d'information.
5. Vérifiez que la navbar reste affichée grâce au template `base.html.twig`.

**Exemple d'imbrication :**

```twig
{# Dans InfoCard.html.twig, une fois la prop level définie #}
<twig:Badge :label="level" />
```

**Critère de réussite :** l'interface est assemblée à partir de plusieurs composants réutilisables, sans dupliquer les fragments de HTML.

---

## Bonus — Pour aller plus loin

Réalisez **un ou plusieurs** des défis suivants si vous avez terminé :

- **Navigation active :** ajoutez une classe CSS `active` au lien correspondant à la route courante. Indice : `app.request.attributes.get('_route')`.
- **Attributs HTML :** faites accepter une classe CSS ou un identifiant supplémentaire à `InfoCard` et affichez-les sur son élément racine grâce à `{{ attributes }}` ou à `{{ attributes.defaults({class: 'info-card'}) }}`.
- **Composant Button :** créez un composant anonyme `Button` avec une prop `variant` par défaut et un contenu placé entre les balises `<twig:Button>Enregistrer</twig:Button>`. Indice : `{% block content %}{% endblock %}`.
- **Organisation :** identifiez les composants purement visuels et ceux pour lesquels une classe PHP pourrait devenir utile. Justifiez vos choix.

---

## Auto-évaluation

À l'issue de l'activité, vous devez être capables de répondre **oui** aux questions suivantes :

- [ ] Je sais installer Symfony UX TwigComponent.
- [ ] Je sais créer et afficher un composant anonyme avec `<twig:Nom />`.
- [ ] Je sais déclarer des props obligatoires et facultatives avec `{% props %}`.
- [ ] Je sais transmettre une valeur dynamique à un composant.
- [ ] Je sais répéter un composant dans une boucle Twig.
- [ ] Je sais créer un composant lié à une classe PHP et à `#[AsTwigComponent]`.
- [ ] Je sais imbriquer un composant dans un autre.
- [ ] Je peux expliquer ce que cette approche améliore en matière de réutilisation et de maintenance.

## Ressources

- [Documentation officielle Symfony UX TwigComponent](https://symfony.com/doc/current/ux-twig-component/index.html)
- [Documentation Symfony sur les templates Twig](https://symfony.com/doc/current/templates.html)

**À retenir :** TwigComponent permet d'organiser l'interface en unités réutilisables. Une page fournit les données, un composant gère leur présentation, et une classe PHP peut être ajoutée lorsque le composant a besoin de davantage de logique.
