<?php
  use JetPHP\Model\Route;
  use Sdrow\MVPUtils;

  $utils = new MVPUtils();
  if($utils->isOnline()){
    Route::add('/', 'dashboard@MVPGramController');  
    Route::add('curtidos/:user', 'curtidos@MVPGramController');
    Route::add('perfil/:user', 'perfil@MVPGramController');
    Route::add('notificacoes','notificacoes@MVPGramController');
    Route::add('configuracoes','configuracoes@MVPGramController');
    Route::add('changedData','changedData@MVPGramController');
    Route::add('uploadIMG','uploadIMG@MVPGramController');
    Route::add('logout', 'logout@MVPGramController');
  }else{
    Route::add('/','login@MVPController');
    Route::add('api2/:pagina', "api@MVPController");
    Route::add("accounts/:tipo", "account@MVPController");
  }

  Route::add('api/:p', 'index@APIController')
?>
