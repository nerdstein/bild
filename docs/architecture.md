# Architecture

## Commands

Bild is built on a Symfony Console application. This leverages Symfony Commands registered within `src/engine.php`.

## Base Command

Bild commands extend a base command that automatically loads the project configuration based on the target directory.
This base command is found at `src/Bild/Console/Command/BaseCommand.php` and all commands should extend this.

## Categories

Bild comes packaged with some sample command categories intended to bundle like commands. Categories  include Frontend,
Initialize, Project, Repo, Testing, Travis, and VM. These leverage PSR-4 namespacing within the
`src\Bild\Console\Command` directory.

## Executable

Bild is an executable that wraps the Symfony Console application, found at `src/engine.php`.

## Targets

Bild runs on a target directory, intended to be a project root. This assumption then supports project-specific
configuration, project-specific commands, and project-specific overrides

## Configuration

Bild must be configured for your project. A sample config.yml is provided under the `configuration` directory and must
be configured for your project needs.

## Command registry and overrides

By default, Bild comes shipped with it's own commands that are held in a registry. This registry exists within
`src/engine.php`. Projects can have their own commands or override Bild commands.

## Composer

Bild leverages Composer for dependencies. You must run `composer install` when initializing and `composer update` when pulling down
Bild updates.