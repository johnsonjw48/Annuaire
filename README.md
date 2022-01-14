# Annuaire
L’objectif de ce projet est de créer un annuaire permettant aux étudiants de partager des posts et des photos les uns avec les autres, et de pouvoir accéder aux infos de profil de chacun des étudiants.

### Installation : 

Pour installer et tester notre projet: 


```
Composer install
```

Pour créer la base de donnée :

```
php bin/console doctrine:database:create
```

```
php bin/console doctrine:schema:update
```

Avant de lancer le projet, insérer manuellement au moins une entrée dans la table group en SQL:

Ex :

```
 INSERT INTO `group` (`id`, `name`, `promo`) VALUES (NULL, 'M1TL', '2020-2021')
```

Lancer ensuite le serveur Symfony pour lancer le projet :

```
symfony serve 
```

