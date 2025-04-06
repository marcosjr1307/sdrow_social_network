<?php
    use JetPHP\Model\JetLoad;
    use JetPHP\Model\Start;
    use GRAM\MVPGram;

    class MVPGramController extends MVPGram{
        public function __construct(){
            parent::__construct();
        }

        private static function load($view, $vars=null){
            $jt = new JetLoad();
            $jt->addVars($vars);
            $jt->view($view);
        } 

        public static function dashboard(){
            return self::load('sdrow.logado.index', ['dadosUsuario' => self::$dadosUsuario, 'posts' => self::getPosts()]);
        }

        public static function perfil(){
            $usuarioAtual = Start::session('username');
            $username = Start::get('user');
            $mvpgram = new MVPGram();
            $dadosUsuario = $mvpgram->getDadosByUsername($username);
            if($dadosUsuario->username==''){
                return self::load('erro.404');
            }else{
                return self::load('sdrow.logado.perfil.perfil', ['dadosUsuario' => $dadosUsuario]);
            }
        }

        public static function uploadIMG() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = Start::session('username');
                $mvpgram = new MVPGram();
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $imagem = $_FILES['image'];
                    
                    if ($imagem['size'] > 2097152) { // 2MB
                        echo json_encode([
                            'status' => false,
                            'message' => 'Arquivo muito grande'
                        ]);
                        return; 
                    }
        
                    $pasta = "upload/perfil/";
                    $nome_imagem = $imagem['name'];
                    $extensao = strtolower(pathinfo($nome_imagem, PATHINFO_EXTENSION));
                    $nome_imagem_bd = uniqid() . '.' . $extensao;
                    $path = $pasta . $nome_imagem_bd;

                    if (!in_array($extensao, ['jpg', 'jpeg', 'png'])) {
                        echo json_encode(['status' => false, 'message' => "Tipo de arquivo não aceito"]);
                        return;
                    }
                    if (move_uploaded_file($imagem['tmp_name'], $path)) {
                        $path = "/" . $path;
                        $changed = $mvpgram->alterUserPhoto($username, $path);
                        if($changed){
                            echo json_encode(['status' => true, 'message' => "Sucesso ao enviar o arquivo"]);
                        }else{
                            echo json_encode(['status' => false, 'message' => "Erro ao salvar no banco de dados"]);
                        }
                        
                    } else {
                        echo json_encode(['status' => false, 'message' => "Erro ao mover o arquivo para o diretório"]);
                    }
                } else {
                    echo json_encode(['status' => false, 'message' => "Selecione a imagem novamente"]);
                }
            } else {
                echo json_encode(['status' => false, 'message' => 'Nenhum dado recebido via POST']);
            }
        }

        public static function curtidos(){
            $username = Start::get('user');
            $mvpgram = new MVPGram();
            $dadosUsuario = $mvpgram->getDadosByUsername($username);
            if($dadosUsuario->username==''){
                return self::load('erro.404');
            }else{
                return self::load('sdrow.logado.perfil.perfilCurtido', ['dadosUsuario' => $dadosUsuario]);
            }
        } 

        public static function configuracoes(){
            $username = Start::session('username');
            $mvpgram = new MVPGram();
            $dadosUsuario = $mvpgram->getDadosByUsername($username);
            return self::load('sdrow.logado.configuracoes',
                [
                    'dadosUsuario' => $dadosUsuario
                ]
            );
        }

        public static function changedData(){
            if(isset($_POST["username"])){
                $username = $_POST["username"];
                $_SESSION["username"] = $username;
                $mvpgram = new MVPGram();
                $dadosUsuario = $mvpgram->getDadosByUsername($username);
                return self::load('sdrow.logado.configuracoes',
                    [
                        'dadosUsuario' => $dadosUsuario
                    ]
                );
            }
        }



        public static function notificacoes(){
            $username = Start::session('username');
            $mvpgram = new MVPGram();
            $dadosUsuario = $mvpgram->getDadosByUsername($username);
            $dadosLikes = $mvpgram->getLikesByUsername($username);
            $dadosComments = $mvpgram->getCommentsByUsername($username);
            $dadosFollowers = $mvpgram->getFollowersByUsername($username);
            return self::load('sdrow.logado.notificacoes', 
            [
                'dadosUsuario' => $dadosUsuario, 
                'dadosLikes' => $dadosLikes,
                'dadosComments' => $dadosComments,
                'dadosFollowers' => $dadosFollowers
            ]);
        }

        
        public static function logout(){
            session_unset();
            session_destroy();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, '/');
            }
            if (!isset($_SESSION["username"])) {
                echo json_encode(['status' => true]);
            } else {
                echo json_encode(['status' => false]);
            }
        }
    }