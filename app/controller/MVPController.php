<?php
    use JetPHP\Model\Start;
    use JetPHP\Model\JetLoad;
    
    class MVPController extends Controle{
        public static function api(){
            header('Content-type: application/json');
            $p = Start::get('pagina');
            if(file_exists("../app/view/api/$p.php")){
                include "../app/view/api/$p.php";
            }
        }

        public static function login(){
            return self::view('sdrow.login', null, false, false);
        }

        public static function register(){
            return self::view('sdrow.cadastro', null, false, false);
        }

        public static function account(){
            $tipo = Start::get('tipo');

            switch($tipo){
                case 'login':
                    return self::login();
                case 'register':
                    return self::register();

                default:
                    header("Location:/");
            }
        }
    }