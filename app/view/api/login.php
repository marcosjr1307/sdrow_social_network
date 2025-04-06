<?php
    use Sdrow\MVPUtils;
    if(isset($_POST['username']) and isset($_POST["senha"])){
        $utils = new MVPUtils();
        $login  = $utils->login();
        $msg = 'Logado com sucesso!';
        if(!$login) $msg = 'Usuário e/ou senha incorretos';
        echo json_encode([
            'status' => $login,
            'msg' => $msg
        ]);
    }else{
        echo json_encode([
            'status' => false,
            'msg' => 'Nenhum dado recebido via POST'
        ]);
    }