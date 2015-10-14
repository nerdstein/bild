# Assumptions

## Composer

Bild is composer driven. You must run `composer install` when initializing and `composer update` when pulling down
Bild updates.

## Docroot

Bild requires a `docroot` directory that holds your Drupal docroot.

## Config.yml

All projects must have a config.yml. A sample is provided under `config` which must exist in the root of your repo and
copied for your project needs.

## Forking

Currently, you must place a copy of this repo in your project. We intend to make this available from Composer in the
near future. The provided .travis.yml file assumes this fork exists in the `tasks` directory.

## Documentation

All documentation should be found in the `/docs` directory.

All included documentation should be added to the projects config.yml under `docs`. New documents
should be added and can run the Generate Documentation task to rebuild the project table of contents.

## Project-provided make file

The project make file exists within `scripts/project.make.yml` and should be updated to meet your needs.

Once completed, and ongoing, you can run the Build Make File task from the task engine to deploy changes to `/docroot`.

## Git hooks

All Git Hooks for your project are found in the `/git` directory.

New hooks or updates to these hooks require all developers to run the Add Repository Hooks task to rebuild the
Git hooks.

## Virtual Machine

The VM leverages [Drupal VM](https://github.com/geerlingguy/drupal-vm) and all of it's dependencies.