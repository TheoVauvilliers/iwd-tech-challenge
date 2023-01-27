# IWD Tech Challenge - Junior Integrator Developer

## Requirements

- PHP >= 8.1
- Composer >= 2.0

## Installation

- Clone this repository
- Configure your .env.local file (or .env if you use this application in production mode) with your Shortcut API
  key (API_SHORTCUT_TOKEN)
- Execute this command :

```bash
$ cd iwd-tech-challenge
$ composer install
```

## Usage

### Init Epics

First, you have to initialize the epics as specified in the technical challenge <br/>
You must create 3 epics:

- Setup
- Code
- Spike

### CSV Files

You must put your csv file to import into Shortcut in the public/uploads directory

### Commands

#### Init workspace

The first command initializes the workspace.<br/>
It should only be used once, and only once, before executing any other command. Then no longer need to use it

```bash
$ php bin/console iwd:shortcut:init-project
OR
$ php bin/console iwd:s:ip
OR
$ php bin/console i:s:ip
```

#### Parse csv

This command allows you to parse your csv file in order to turn the data into stories and create dependencies between
them if necessary <br/>
You must add after the command the name of the csv file located in public/uploads that you want to import <br/>
For example: stories for the stories.csv file

```bash
$ php bin/console iwd:shortcut:parse-csv
OR
$ php bin/console iwd:s:pc
OR
$ php bin/console i:s:pc
```

## Explications et axes d'améliorations possibles

### Choix technique

Dans un premier temps, j'ai réfléchi à faire du NodeJS mais j'ai écarté cette technologie à cause du côté asynchrone
qui m'aurait potentiellement posé problème avec la conception que je me suis imaginé. J'avais déjà fait un exercice
similaire avec du NodeJS et j'avais au final changé de langage à cause de quelques soucis.

J'ai ensuite réfléchi à faire du Python, un langage qui aurait pu être adapté à ce genre d'app. J'ai donc creusé le
sujet et j'ai trouvé une libraire intéressante permettant de faire une CLI. Le souci est que cette libraire permet de
faire une et une seule commande sur le script. Par très pratique en terme de scalabilité. De plus, le dernier point
optionnel du challenge est de faire une UI, pas très pratique non plus en Python.

Le choix final s'est donc porté sur du PHP et plus précisément Symfony. Je suis parti de la base la plus vide possible
et j'y ai ajouté les composants nécessaires afin d'avoir une app relativement légère.

### Difficultés rencontréss

Le point le plus ennuyant auquel j'ai été confronté est la doc de l'API Shortcut... Elle est incomplète ce qui m'a fait
perdre beaucoup de temps à devoir trouver ce qui n'allait pas dans mes calls APIs. <br>
Exemple, pour la création d'une story, sur la doc de l'API, il est marqué que seul le nom est obligatoire, ce qui est
faux car il faut aussi un id de projet.

Un autre point où je suis souvent retourné dessus est l'architecture de mon projet, j'ai toujours eux du mal à créer des
architectures. Si vous prêtez attention aux commits sur le repo, il y en a plusieurs pour refaire et/ou modifier
l'architecture.

### Améliorations

Une feature qui pourrait être très pratique est qu'au lieu de passer le nom du fichier en paramètres de la commande,
celle-ci liste tous les fichiers csv du dossier public/uploads et qu'on puisse choisir avec les flèches
directionnelles

Une amélioration que j'aurais faite si la commande avait pour vocation d'être utilisé, ce serait de trouver le moyen de
n'afficher que mes commandes custom sans avoir toutes les commandes de Symfony lors du `php bin/console`. J'ai à un
moment envisagé de faire un projet PHP from scratch et embarquant seulement les composants Symfony afin de me
débarrasser des autres commandes.

Et en dernier point, revoir l'abstraction du code afin d'épurer la commande CsvToStories et de faire du code plus
réutilisable partout afin de faciliter la scalabilité de celle-ci pour les futures autres commandes.
