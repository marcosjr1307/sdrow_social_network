<?php
  use JetPHP\Model\Config;

  Config::add('DEBUG',true);
  Config::add('md5_salt','sdrow');
  Config::add('TITULO','Sdrow');
  Config::add('PASTA_PADRAO','/');
  Config::add('PASTA_ADMIN','manager/');
  Config::add('PASTA_MISC',Config::show('PASTA_PADRAO').'misc/');
  Config::add('ADMIN_MISC',Config::show('PASTA_PADRAO').'misc/manager/');
  Config::add('GOOGLE_ANALYTICS','');

  // Database configuration
  Config::add('DB_HOST','');
  Config::add('DB_USUARIO','');
  Config::add('DB_SENHA','');
  Config::add('DB_NOME','');
  
  // E-mail configuration
  Config::add('SMTP_HOST','smtp.exemplo.com');
  Config::add('SMTP_USUARIO','username');
  Config::add('SMTP_SENHA','password');
  Config::add('SMTP_EMAILPRINCIPAL','example@example.com');
  Config::add('SMTP_PORTA', 587);
?>
