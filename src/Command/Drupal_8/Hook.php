<?php

namespace DrupalCodeGenerator\Command\Drupal_8;

use DrupalCodeGenerator\Command\BaseGenerator;
use DrupalCodeGenerator\Utils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Implements d8:hook command.
 */
class Hook extends BaseGenerator {

  protected $name = 'd8:hook';
  protected $description = 'Generates a hook';
  protected $alias = 'hook';

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $questions = Utils::defaultQuestions();
    $questions['hook_name'] = [
      'Hook name',
      NULL,
      function ($value) {
        if (!in_array($value, $this->supportedHooks())) {
          return 'This hook is not supported.';
        }
      },
      $this->supportedHooks(),
    ];

    $vars = $this->collectVars($input, $output, $questions);

    $install_hooks = [
      'install',
      'uninstall',
      'schema',
      'requirements',
      'update_N',
      'update_last_removed',
    ];

    $file_type = in_array($vars['hook_name'], $install_hooks) ? 'install' : 'module';

    $header = $this->render("d8/file-docs/$file_type.twig", $vars);
    $content = $this->render('d8/hook/' . $vars['hook_name'] . '.twig', $vars);

    $this->files[$vars['machine_name'] . '.' . $file_type] = [
      'content' => $header . "\n" . $content,
      'merge_type' => 'append',
      'header_height' => 7,
    ];
  }

  /**
   * Returns list of supported hooks.
   */
  protected function supportedHooks() {
    return array_map(function ($file) {
      return pathinfo($file, PATHINFO_FILENAME);
    }, array_diff(scandir($this->templatePath . '/d8/hook'), ['.', '..']));
  }

}