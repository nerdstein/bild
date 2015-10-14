<?php

/**
 * @file
 */

namespace Bild\Console\Command\Project;

use Bild\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 *
 */
class GenerateReleaseNotes extends BaseCommand {

  public $user = '';
  public $pass = '';
  public $project = '';
  public $repo = '';
  public $branch = '';
  public $since = '';
  public $limit = '';

  /**
   * @see http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
   */
  protected function configure() {

    parent::configure();

    $this
      ->setName('project:release-notes')
      ->setDescription('Generate release notes')
      ->addArgument(
        'user',
        InputArgument::REQUIRED,
        'Github username.'
      )
      ->addArgument(
        'pass',
        InputArgument::REQUIRED,
        'Github password.'
      )
      ->addArgument(
        'project',
        InputArgument::REQUIRED,
        'Github project name (e.g., acquia-pso).'
      )
      ->addArgument(
        'repo',
        InputArgument::REQUIRED,
        'Github repository name.'
      )
      ->addArgument(
        'branch',
        InputArgument::REQUIRED,
        'Project branch.'
      )
      ->addArgument(
        'since',
        InputArgument::REQUIRED,
        'Date to collect PRs from. (e.g., 4/10/2015)'
      )
      ->addOption(
        'limit',
        'l',
        InputOption::VALUE_OPTIONAL,
        'What is the limit for requesting PRs.',
        100
      );

  }


  /**
   * @{inheritdoc}
   *
   * @throws \RuntimeException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    $this->user = $input->getArgument('user');
    $this->pass = $input->getArgument('pass');
    $this->project = $input->getArgument('project');
    $this->repo = $input->getArgument('repo');
    $this->branch = $input->getArgument('branch');
    $this->since = $input->getArgument('since');
    $this->limit = $input->getOption('limit');

    if (empty($this->limit)) {
      $this->limit = 100;
    }

    date_default_timezone_set('America/New_York');

    // Create a date like 2014-12-23T00:00:00Z.
    $since_github = date('Y-m-d', strtotime($this->since)) . 'T00:00:00Z';

    // We can only get 100 results at a time, so we need to split the calls into chunks of 100.
    $calls = ceil($this->limit / 100);

    $url = 'https://api.github.com/repos/' . $this->project . '/' . $this->repo . '/pulls?state=closed&since=' . $since_github . '&per_page=' . $this->limit . '&base=' . $this->branch;

    for ($page = 1; $page <= $calls; $page++) {

      $prs = $this->fetch_pr($url . '&page=' . $page);

      // Print each Pull Request.
      foreach ($prs as $pr) {
        // We don't want to print PRs that are not merged.
        if (is_null($pr['merged_at'])) {
          continue;
        }

        // Check our date is within the time period.
        $closed_date = strtotime($pr['closed_at']);

        if ($closed_date < $this->since) {
          continue;
        }

        $this->print_pr($pr['title'], $closed_date, $pr['html_url']);
      }
    }
  }

  private function fetch_pr($url) {
    // Download the json file.
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->pass);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Acquia-PS');
    curl_setopt($ch, CURLOPT_URL, $url);

    $json_raw = curl_exec($ch);
    $chinfo = curl_getinfo($ch);

    // We bail if we don't get a successful connection.
    if ($chinfo['http_code'] !== 200) {
      print 'HTTP Error: ' . $chinfo['http_code'] . PHP_EOL;
      print 'URL: ' . $url . PHP_EOL;
      print $json_raw . PHP_EOL;
      exit;
    }

    curl_close($ch);

    // Decode the JSON.
    return json_decode($json_raw, TRUE);
  }

  private function print_pr($title, $date, $link) {
    // Print the PR Title.
    print '## ' . $title . PHP_EOL;
    // Print the PR Time and URL.
    print date("F j, Y", $date) . ' ([' . $link . ']' . '(' . $link . '))' . PHP_EOL . PHP_EOL;
  }
}
