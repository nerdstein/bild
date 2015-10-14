# Contributing to tasks

Please feel free to add new commands or update the existing ones for bugs under the `/src/Bild/Console/Command`
directory. To add new commands, follow these steps:

  1. Add a new file (or copy an existing) into `/src/Bild/Console/Command/[CATEGORY]`
  1. Name the file the same name as the class
  1. Define the command and the arguments in the class `configure` function
  1. Define the command executable in the class `execute` function
  1. Register your command in the engine, `/engine.php`

A list of common utilities shared across tasks can be found at `/src/Bild/Console/Command/BaseCommand.php`. All
contributed commands should extend this base command.

Documentation can be found [here](http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command).


