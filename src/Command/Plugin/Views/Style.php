<?php

namespace DrupalCodeGenerator\Command\Plugin\Views;

use DrupalCodeGenerator\Command\Plugin\PluginGenerator;

/**
 * Implements plugin:views:style command.
 */
final class Style extends PluginGenerator {

  protected $name = 'plugin:views:style';
  protected $description = 'Generates views style plugin';
  protected $alias = 'views-style';

  /**
   * {@inheritdoc}
   */
  protected function generate(array &$vars): void {
    $this->collectDefault($vars);
    $vars['configurable'] = $this->confirm('Make the plugin configurable?');

    $this->addFile('src/Plugin/views/style/{class}.php')
      ->template('style');

    $this->addFile('templates/views-style-{plugin_id|u2h}.html.twig')
      ->template('template');

    $this->addFile('{machine_name}.module')
      ->headerTemplate('_lib/file-docs/module')
      ->template('preprocess')
      ->appendIfExists()
      ->headerSize(7);

    if ($vars['configurable']) {
      $this->addSchemaFile()->template('schema');
    }

  }

}
