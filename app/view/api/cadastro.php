<?php
    use Sdrow\MVPUtils;
    if(isset($_POST['username']) and isset($_POST["senha"])){
        $utils = new MVPUtils();
        $registro  = $utils->register();
        $msg = 'Registrado com sucesso!';
        if(!$registro){
            $msg = 'Erro ao criar usuário';
        }
        if($registro === 2){
            $msg = 2;
            $registro = false;
        }else if ($registro === 3){
            $msg = 3;
            $registro = false;
        }else if($registro === 4){
            $msg = 4;
            $registro = false;
        }else if($registro === 5){
            $msg = 5;
            $registro = false;
        }
        echo json_encode([
            'status' => $registro,
            'msg' => $msg
        ]);
    }else{
        echo json_encode([
            'status' => false,
            'msg' => 'Nenhum dado recebido via POST'
        ]);
    }