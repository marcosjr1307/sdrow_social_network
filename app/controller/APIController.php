<?php
    use JetPHP\Model\Start;
    use Sdrow\MVPUtils;
    use JetPHP\Model\JetLoad;
    class APIController extends API{
        public static function index(){
            $p = Start::get('p');
            $utils = new MVPUtils;
            if($utils->isOnline()){
                if(method_exists(self::getInstance(), $p)){
                    echo json_encode(self::$p());
                }else{
                    echo json_encode([
                            'status'=> false,
                            'msg' => 'Rota inexistente'
                        ]
                    );
                }
            }else{
                if($p == 'login'){
                    self::load('sdrow.login');
                }else if($p == 'register'){
                    self::load('sdrow.cadastro');
                }else{
                    exit;
                }
            }
        }

        private static function load($view, $vars=null){
            $jt = new JetLoad();
            $jt->addVars($vars);
            $jt->view($view);
        } 

    }