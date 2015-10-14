# Overview

Bild is a set of tasks for consistent Drupal project operations. This is intended to promote consistency
across local systems, CI tools, and remote servers.

## Goals

Bild aims to meet the following goals:

* To standardize development operations
* To improve operations and development velocity
* To provide a reusable set of common tasks that can be used on any project with minimal effort
* To be easily extensible add own tasks and contribute maintained tasks back to the team
* To be agnostic to tools and environments at the discretion of projects
* To avoid introducing additional tools the team needs to learn while leveraging the skills they already have

## Purpose

The task engine serves the following purposes:

* Not fighting specific implementations across different platforms (Travis, Jenkins, Phing, Ant, etc.)
* Leverage development skills the team already has (PHP, Composer, Symfony)
* Leverage some of the best modern PHP frameworks (D8, Drupal Console, Drush)
* Provide an extensible development platform to enable and empower the developers (Composer and Symfony)

## Case Study - Drush, Console, and Bild

All three tools serve a distinct purpose.

1. Drush performs operations on a live Drupal site.
1. Console is a set of commands for code generation and code management tool for a Drupal codebase.
1. Bild is for project-level operations to promote consistency in best practices for code workflows,
project initialization, automated testing, and environment management.