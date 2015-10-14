# Project Name

**Release** [![Integration Build Status](https://magnum.travis-ci.com/namespace/project.svg?token=&branch=release)](https://magnum.travis-ci.com/namespace/project)

**Master** [![Master Build Status](https://magnum.travis-ci.com/namespace/project.svg?token=&branch=master)](https://magnum.travis-ci.com/namespace/project)

This document outlines technical onboarding for the Project Name project.

## Architectural Summary
* Drupal 8
* [Install profile](docroot/profiles/profile_name)
* [Base Theme](docroot/themes/theme_name)
* [Sub Theme](docroot/themes/theme_name)
* Hosting Environment Summary

## Resources
* [Github](https://github.com/namespace/project)
* [TravisCI](https://magnum.travis-ci.com/namespace/project)
* [Task Engine](tasks/readme.md)
* [Backlog](http://jira.com/PROJECT)
* [Hosting Dashboard](https://dashboard.com)
* [Docs](https://drive.google.com/drive/folders/whatever)
* [VM Quick Start](https://github.com/geerlingguy/drupal-vm#quick-start-guide)
* [Using the VM](https://github.com/geerlingguy/drupal-vm#using-drupal-vm)
* [TA Doc](docs/technical_architecture.md)
* Slack Channel - namespace/project (private group by invite-only)

## Environments

### Repositories
* Development - git@github.com/namespace/project.git
* Hosting - project@git.project.com/project.git

### Local
* Web Server: [http://project-name.local](http://project-name.local)
* XHProf: [http://xhprof.project-name.local](http://xhprof.project-name.local)
* Pimp My Log: [http://pimpmylog.project-name.local](http://pimpmylog.project-name.local)
* SOLR: [http://project-name.local:8443/solr](http://project-name.local:8443/solr)

### Acquia Cloud
* Dev: [http://project-name.dev](http://project-name.dev)
* Staging: [http://project-name.stage](http://project-name.stage)
* Prod: [http://project-name.prod](http://project-name.prod)

## Development Prerequisites
This section covers all aspects of day-to-day development.

### Development Tools
* Git [download](https://git-scm.com/downloads)
* Drush 8.x [install](https://github.com/drush-ops/drush/blob/master/docs/install.md#composer---one-drush-for-all-projects)
* Composer [install](https://getcomposer.org/doc/00-intro.md#globally)

### Local System Requirements
1. Download and install [VirtualBox](https://www.virtualbox.org/wiki/Downloads) (Drupal VM also works with Parallels or VMware, if you have the [Vagrant VMware integration plugin](http://www.vagrantup.com/vmware)).
1. Download and install [Vagrant](http://www.vagrantup.com/downloads.html).
1. [Mac/Linux only] Install [Ansible](http://docs.ansible.com/intro_installation.html).
1. Download and install [nodejs](https://nodejs.org/download/)

### Onboarding
* Fork repo to your own repository
* Load task engine dependencies, `composer install --working-dir=tasks`
* View available tasks, `bild`
* Load git remotes, `bild repo:add-remotes`
* Load git hooks, `bild repo:add-hooks`
* Load Drush aliases, `bild project:add-drush-alias`
* Check VM dependencies, `bild vm:check-dependencies`
* Load VM, `bild vm:bootstrap`

### Drupal Administrator Account
* Login locally as admin using drush uli with the local alias `drush @project-name.project-name.local uli`

### Dev Standards
* [Automated test](tests/behat/features) all the things!
* All contrib and patches must be in the [make file](scripts/project.make.yml)
* Custom patches must be in the [patches](patches) directory and in the [make file](scripts/project.make.yml)
* Audit the [project configuration](config.yml) for adjustments
* Review and update the [TA Doc](docs/technical_architecture.md)
* Consider documenting how-to tips/tricks within the Feature Notes below
* Squash extraneous commits before submitting a PR, e.g. "Bug fixing ..." or "Merging ..."
* Rebase with upstream, do not `git pull` and create extraneous commits
* Validation will occur automatically with Git commit hooks and TravisCI, a developer will be responsible for keeping
code up to known standards

### Starting A Ticket
* Pull down most recent code from `upstream integration` 
* Create a new branch with the ticket number assigned
* Get your geek on

### Dev Complete
* Verify a project rebuild, `bild project:rebuild-site`
* Test locally, attach link to PR, screenshot (front-end) or testing criteria (back-end) to ticket
* Mark ticket as development complete
* Create pull request from your fork to upstream, integration branch
* Assign to release manager

## Release Management
Release management will be handled by the TA or Dev Lead. The process is summarized below.

### Repository Tags
* Tags will follow [semantic versioning 2.0 standards](http://semver.org/), major.minor.patch convention
* The tags will be generated using github's tagging UI
* Development releases will be tagged in the form `vx.x.x-dev` to identify releases to dev
* Release candidates will be tagged in the form `vx.x.x-rc` to identify releases to staging
* A full release will not have -dev or -rc e.g., `vx.x.x`.
* When a tag is created Travis CI will push the tag up to the hosting repository.

### Release Notes
* Release notes should be created and committed prior to a full release being tagged.
* See [Release notes](docs/developer-guide.md#generate-release-notes)

### Deployments
* Deployments require proper local set up of [Acquia Cloud Drush integration](https://docs.acquia.com/cloud/drush-aliases).
* The release manager can add the project specific drush aliases using this command:
`bild project:add-drush-alias`
* The release manager will handle deployments manually, due to limitations with TravisCI and Acquia Cloud Drush commands
* Dev release example command, `bild project:deploy @project-name.dev v1.0.0-dev`
* Staging release example command, `bild project:deploy @project-name.staging v1.0.0-rc`
* Production release command, `bild project:deploy @project-name.prod v1.0.0-rc`
* A future iteration may look to handle this from within Cloudbees/Jenkins

## Technical Features
This section is to define tips, tricks, and how-tos for core features of the site. This is 
developer focused and intended to help with handoffs if someone needs to do work on a feature
built by another developer.

### Search
Search is built on top of Acquia Search (Apache SOLR based)
* The local administration interface is available at `http://solr.project-name.local/solr/core0/admin/`
* Starting the local SOLR instance: `sudo service tomcat6 start`
* Stopping the local SOLR instance: `sudo service tomcat6 stop`
* Enable the local SOLR index in Drupal: `drush solr-set-env-url --id=local`
