# Comment démarrer le projet

Nous vous fournissons une méthode utilisant Docker. Libre à vous de l'utiliser ou non.

Si vous ne souhaitez pas l'utiliser, veuillez gérer vous même l'installation et le lancement des tests.
Ne pas utiliser Docker rend toutes les commandes du Makefile inutiles.

```sh
cp .env.example .env

# Modifier les variables UID et GID dans le .env si besoin
# La commande `id` affiche le UID et GID

make install
# L'application est accéssible via http://127.0.0.1:8000
# Vous pouvez changer le port par défaut en modifiant FRONTEND_PORT dans votre .env
# Lancez `docker-compose restart` pour prendre en compte les changements
```

# Description du projet

Vous venez de cloner le dépôt Git d'un projet fictif en cours de developpement.
Le but de ce projet est de fournir une interface utilisateur permettant de rechercher des dépôt Git à l'aide des APIs de Github, Gitlab etc.

Pour le moment, une esquisse a été mise en place et ne permet de rechercher les dépôts que depuis le provider Github.

Le point d'entrée de l'application est accessible via `App\Http\Controllers\RepositoryController::search()`. Cette méthode est enregistrée dans les routes api de Laravel (accessible depuis /api/repository?q=)

L'ensemble des tests permettant de valider les features attendues pour le projet ont déjà été codées (cf. `Tests\Feature\RepositoryControllerTest`.

Le code écrit souffre cependant de plusieurs problèmes:
1. Les "bonnes pratiques" de développement moderne n'ont pas été strictement suivies. (Injection de dépendances, validations propres, code style, strict typing etc.)
2. Le controlleur a de trop grandes responsabilités (penser SOLID, KISS)
3. Le code n'est pas facilement portable et souple. En effet, il va être compliqué en l'état d'intégrer un second provider tel que Gitlab.
4. Certains tests fonctionnels ne passent pas en succès car le code ne répond pas aux spécifications techniques définies dans les tests.

# Votre mission (Vous n'avez pas de front à faire)

1. Effectuer un refactoring afin de rendre le code plus propre en respectant les bonnes pratiques
2. Faire en sorte d'intégrer un connecteur Gitlab (Une architecture orientée objet sera de mise. Pensez **Interface**)
3. Pour terminer, faire en sorte que notre endpoint /repository puisse renvoyer à la fois les résultats de la recherche Github et ceux de la recherche Gitlab.
4. Veillez à ce que tous les tests passent au vert.
5. (Bonus) S'il vous reste un peu de temps, écrire des **tests unitaires** permettant de valider le bon fonctionnement de votre connecteur Github

:warning: Veuillez une fois le test terminé, a bien commit l'intégralité de votre travail. (Vous pouvez commit au fur et a mesure de votre avancement si vous le jugez utile)

Nous vous conseillons de lancer les tests régulièrement afin de vérifier que vous ne partez pas dans la mauvaise direction.
De même, les tests sont triés par ordre de priorité. Essayez donc de les faire passer au vert dans l'ordre dans lequel ils sont écrits.

# Informations utiles

## Ai-je le droit d'installer des bibliothèques tierces ?
Non. L'ensemble des dépendances dont vous avez besoin se trouvent dans le projet.

## Qu'ai-je le droit de modifier dans le projet ?
Absolument tout ce qui vous paraît pertinent (à l'exception des les tests déjà écrits).

## Comment utiliser la commande `artisan` ?
Vous pouvez rentrer dans le docker PHP afin d'avoir accès à artisan.
```sh
make exec
php artisan
```

## Comment lancer les tests ?
```sh
make test
make test filter=nom_du_test_a_lancer
```

## Information relatives aux APIs
- [Github](https://docs.github.com/en/free-pro-team@latest/rest/reference/search#search-repositories)
- [Gitlab](https://docs.gitlab.com/ee/api/projects.html#search-for-projects-by-name)

Pour Gitlab les champs suivants de la réponse API peuvent être utilisés:
- name
- path_with_namespace
- description
- namespace.path

Toujours pour le connecteur Gitlab, nous vous demandons de spécifier 2 filtres en plus lors de vos appels API:
- &order_by=id
- &sort=asc

:warning: Pour des raisons techniques, merci de **ne pas oublier de spécifier le nombre d'élements par page** lors de vos appels aux API (**a régler sur 5**).

Des limites d'accès aux API existent (~ 10 call par minutes).
Si vous recevez des erreurs étrange, vous être peut-être bloqué par l'API. Il faut alors attendre une minute.
