<?php
// engine.php

// Symfony dependencies.
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

// Load all of the src code.
require __DIR__ . '/vendor/autoload.php';

// Bootstrap application.
$application = new Application();

// Load commands from running.
$finder = new Finder();
$loaded_classes = [];
/**
 * We check if the '/bild/src/' folder exists. If it does,
 * we can load additional classes.
 */
if (file_exists(exec('pwd') . '/bild/src/')) {
  foreach ($finder->in(exec('pwd') . '/bild/src/') as $file) {
    if ($file->isFile()) {

      $path_info = pathinfo($file->getRelativePath().'/'.$file->getFilename());
      if ($path_info['extension'] == 'php') {
        $loaded_class = implode('\\', explode('/', $path_info['dirname']));
        $loaded_class = '\\' . $loaded_class . '\\' . $path_info['filename'];
        $application->add(new $loaded_class);
        require $file->getRealPath();
        $loaded_classes[] = $loaded_class;
      }
    }
  }
}

// Register all Bild Commands.

// To run: php engine.php project:add-drush-alias .
$application->add(new \Bild\Console\Command\Initialize\AddDrushAliases());

// To run without a remote origin: engine.php repo:add .
// To run with a remote origin: engine.php repo:add  git@github.com:[USER]/[REPO].git.
$application->add(new \Bild\Console\Command\Repo\AddRepository());

// To run: php engine.php repo:add-hooks .
$application->add(new \Bild\Console\Command\Repo\AddRepositoryHooks());

// To run: php engine.php repo:add-remotes .
$application->add(new \Bild\Console\Command\Repo\AddRepositoryRemotes());

// To run: php engine.php vm:add .
$application->add(new \Bild\Console\Command\Vm\AddVm());

// To run: php engine.php vm:bootstrap .
$application->add(new \Bild\Console\Command\Vm\BootstrapVm());

// To run: php engine.php frontend:build .
$application->add(new \Bild\Console\Command\Frontend\BuildFrontend());

// To run: php engine.php project:build-make-file .
$application->add(new \Bild\Console\Command\Project\BuildMakeFile());

// To run: php engine.php vm:check-dependencies .
$application->add(new \Bild\Console\Command\Vm\CheckVmDependencies());

// To run: php engine.php project:configure-testing .
$application->add(new \Bild\Console\Command\Project\ConfigureTestingFramework());

// To run: php engine.php vm:configure .
$application->add(new \Bild\Console\Command\Vm\ConfigureVm());

// To run: php engine.php project:create .
$application->add(new \Bild\Console\Command\Project\CreateProject());

// To run: php engine.php project:deploy  @site.dev v1.0.0.
$application->add(new \Bild\Console\Command\Project\DeployProject());

// To run: php engine.php vm:destroy .
$application->add(new \Bild\Console\Command\Vm\DestroyVm());

// To run: php engine.php project:initialize-documentation .
$application->add(new \Bild\Console\Command\Project\InitializeDocumentation());

// To run: php engine.php frontend:install .
$application->add(new \Bild\Console\Command\Frontend\InitializeFrontendTools());

// To run: php engine.php project:initialize .
$application->add(new \Bild\Console\Command\Project\InitializeProject());

// To run: php engine.php travis:initialize .
$application->add(new \Bild\Console\Command\Travis\InitializeTravis());

// To run: php engine.php project:install .
$application->add(new \Bild\Console\Command\Project\InstallSite());

// To run: php engine.php project:load-composer-dependencies .
$application->add(new \Bild\Console\Command\Project\LoadComposerDependencies());

// To run: php engine.php project:publish  v1.0.0 origin master hosting master --process-tag.
$application->add(new \Bild\Console\Command\Project\PublishRepo());

// To run: php engine.php project:rebuild-site.
$application->add(new \Bild\Console\Command\Project\RebuildSite());

// To run: php engine.php project:release-notes  user pass project repo branch 01/01/2015 --limit=100.
$application->add(new \Bild\Console\Command\Project\GenerateReleaseNotes());

// To run: php engine.php project:run-phpcbf.
$application->add(new \Bild\Console\Command\Project\RunPHPCBF());

// To run: php engine.php project:setup-local.
$application->add(new \Bild\Console\Command\Project\SetupLocal());

// To run: php engine.php project:sync hosting master.
$application->add(new \Bild\Console\Command\Project\SyncRepos());

// To run: php engine.php testing:automated-tests .
$application->add(new \Bild\Console\Command\Testing\RunAutomatedTests());

// To run: php engine.php testing:behat .
$application->add(new \Bild\Console\Command\Testing\TestBehat());

// To run: php engine.php testing:phpunit .
$application->add(new \Bild\Console\Command\Testing\TestPHPUnit());

// To run: php engine.php testing:security-updates .
$application->add(new \Bild\Console\Command\Testing\TestSecurityUpdates());

// To run: php engine.php testing:validate .
$application->add(new \Bild\Console\Command\Testing\RunValidationTests());

// To run: php engine.php testing:validate-blacklist .
$application->add(new \Bild\Console\Command\Testing\ValidateBlacklist());

// To run: php engine.php repo:validate-commit .
$application->add(new \Bild\Console\Command\Repo\ValidateCommit());

// To run: php engine.php testing:validate-makefile .
$application->add(new \Bild\Console\Command\Testing\ValidateMakefile());

// To run: php engine.php testing:validate-phpcs .
$application->add(new \Bild\Console\Command\Testing\ValidatePHPCS());

// To run: php engine.php testing:validate-phpmd .
$application->add(new \Bild\Console\Command\Testing\ValidatePHPMD());

// To run: php engine.php testing:validate-phpsyntax .
$application->add(new \Bild\Console\Command\Testing\ValidatePHPSyntax());

// To run: php engine.php testing:validate-blacklist .
$application->add(new \Bild\Console\Command\Testing\ValidateBlacklist());


// Run the application to accept the command and parameters.
$application->run();