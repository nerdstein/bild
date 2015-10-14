# Bild Overrides

We leverage PSR-4 for class overrides.

To begin, from within your project root, create the following directory structure:

`bild/src/Bild/Console/Command`

## Adding custom commands to your project

Create a new class that extends `BaseCommand` within any of the following directories:

1. `Frontend`
1. `Initialize`
1. `Project`
1. `Repo`
1. `Testing`
1. `Travis`
1. `VM`

Run `bild` from the project root and verify your command shows up.

## Overriding existing commands in your project

Copy the existing class into the same directory within your project.

For instance, you wish to override the `bild project:build-make-file` command.

1. That file resides at `src/Bild/Console/Command/Project/BuildMakeFile.php` within the `Bild` repo
1. Copy that file to your project root within `bild/src/Bild/Console/Command/Project/BuildMakeFile.php`
1. Change the command within your project
1. Run `bild project:build-make-file` and ensure your changes exist