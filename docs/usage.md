# Usage

Tasks are distributed and intended to run within a project or outside of it. Tasks are generic.
The goal is to provide consistency across routine tasks. And, to evolve them over time.

To build the task engine, run `composer install --working-dir=tasks` from the root directory.

## View Available Tasks

All commands are documented within engine.php.

You can also see this from the command line by running `bild`.

## Install A New Project

  1. Run project create task: `bild project:create`
  1. Edit your project-specific configuration found in `/path/to/new/project/config.yml`
  1. [Optional] Update your make file found in `/path/to/new/project/scripts/project.make.yml`
  1. Run initialize project task: `bild project:initialize`

Installation is completed and you can follow the "Post-installation Tasks" defined below to set up your project.

## Common Dev Ops tasks

Some tasks are intended to be run once per project (e.g. `project:create`, `project:initialize`,
`project:initialize-documentation`). Others are intended to be run over time and as needed, per environment.

Current tasks include:

  1. Run Drush Make task: `bild project:build-make-file`
  1. Generate documentation (table of contents): `bild project:generate-docs`
  1. Add repo hooks (useful for developers): `bild repo:add-hooks`
  1. Add repo remotes (useful for developers): `bild repo:add-remotes`
  1. Load Composer dependencies: `bild project:load-composer-dependencies`


## Run a task on an existing project

Existing projects may want to leverage these tasks on their project.

Tasks only have one dependency: a project-specific config.yml (base found at example.config.yml) configured for your project
needs.

For example, adding your own VM would require the steps:

  1. Copy project template `example.config.yml` to `/path/to/existing/project/config.yml`
  1. Edit your project-specific configuration found in `/path/to/existing/project/config.yml`
  1. Run Add VM task: `bild vm:add`
  1. Run Configure VM task: `bild vm:configure`
  1. Verify your system supports the VM: `bild vm:check-dependencies`
  1. Bootstrap VM: `bild vm:bootstrap`

## Scope

Bild is meant to be run from many platforms (CI systems, task runners, local dev, etc).

### Cloud hooks

Like Drush, Bild can be invoked from within cloud hooks for use within Acquia Cloud.

### TravisCI configuration

Bild can be called from TravisCI configuration found at `/.travis.yml` for relevance to your project. Invoke the task
engine and its tasks for your needs.

### Jenkins / CloudBees configuration

TBD
