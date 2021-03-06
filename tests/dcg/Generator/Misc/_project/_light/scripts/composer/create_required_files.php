#!/usr/bin/env php
<?php

// -- settings.php file.
$default_settings_file = './sites/default/default.settings.php';
$settings_file = './sites/default/settings.php';

if (!file_exists($settings_file) && file_exists($default_settings_file)) {
  $content = file_get_contents($default_settings_file);

  // Allow local development configuration.
  $current_code = <<<'EOS'
#
# if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
#   include $app_root . '/' . $site_path . '/settings.local.php';
# }
EOS;
  $new_code = str_replace(["#\n", '# '], '', $current_code);
  $content = str_replace($current_code, $new_code, $content);

  if (!@file_put_contents($settings_file, $content)) {
    file_put_contents('php://stderr', "Could not create $settings_file file.\n", FILE_APPEND);
  }
}

// -- settings.local.php file.
$example_local_settings_file = './sites/example.settings.local.php';
$local_settings_file = './sites/default/settings.local.php';
if (!file_exists($local_settings_file) && file_exists($example_local_settings_file)) {
  if (@copy($example_local_settings_file, $local_settings_file)) {
    chmod($settings_file, 0666);
  }
  else {
    file_put_contents('php://stderr', "Could not create $local_settings_file file.\n", FILE_APPEND);
  }
}

// -- files directory.
$default_dir = './sites/default';
$files_dir = $default_dir . '/files';
if (file_exists($default_dir) && !file_exists($files_dir)) {
  $original_umask = umask(0);
  if (!@mkdir($files_dir, 0777)) {
    file_put_contents('php://stderr', "Could not create $files_dir directory.\n", FILE_APPEND);
  }
  umask($original_umask);
}
