<?php

namespace DrupalCodeGenerator\Commands\Drupal_7\Component;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DrupalCodeGenerator\Commands\BaseGenerator;

/**
 * Implements d7:component:template.php command.
 */
class TemplatePhp extends BaseGenerator {

  protected $name = 'd7:component:template.php';
  protected $description = 'Generate Drupal 7 template.php file';

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {

    $questions = [
      'name' => ['Module name', [$this, 'defaultName']],
      'machine_name' => ['Module machine name', [$this, 'defaultMachineName']],
    ];

    $vars = $this->collectVars($input, $output, $questions);

    $this->files['template.php'] = $this->render('d7/template.php.twig', $vars);

  }

}
