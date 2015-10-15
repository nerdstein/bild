<?php
$drush_major_version = '8';
if (!isset($drush_major_version)) {
  $drush_version_components = explode('.', DRUSH_VERSION);
  $drush_major_version = $drush_version_components[0];
}
// Site project, environment dev
$aliases['dev'] = array(
  'root' => '/var/www/html/project-name/docroot',
  'uri' => 'dev.project-name.com',
  'remote-host' => 'dev.project-name.com',
  'remote-user' => 'remote-user',
  'path-aliases' => array(
    '%drush-script' => 'drush' . $drush_major_version,
  )
);

// Site project, environment stage
$aliases['stage'] = array(
    'root' => '/var/www/html/project-name/docroot',
    'uri' => 'stage.project-name.com',
    'remote-host' => 'stage.project-name.com',
    'remote-user' => 'remote-user',
    'path-aliases' => array(
        '%drush-script' => 'drush' . $drush_major_version,
    )
);

if (!isset($drush_major_version)) {
  $drush_version_components = explode('.', DRUSH_VERSION);
  $drush_major_version = $drush_version_components[0];
}

// Site project, environment stage
$aliases['prod'] = array(
    'root' => '/var/www/html/project-name/docroot',
    'uri' => 'project-name.com',
    'remote-host' => 'project-name.com',
    'remote-user' => 'remote-user',
    'path-aliases' => array(
        '%drush-script' => 'drush' . $drush_major_version,
    )
);
