<?php
    namespace GRAM;
    use JetPHP\Model\DB;
    use JetPHP\Model\Start;
    use JetPHP\Model\JetLoad;
    class MVPGram{
        protected static $dadosUsuario;

        public function __construct(){
            self::$dadosUsuario = $this->getDados();
        }

        public function alterUserPhoto($username, $path){
            $qr = DB::execute("SELECT foto FROM sdrow_users WHERE username = ?", [$username]);
            $result = $qr->generico()->fetch(\PDO::FETCH_OBJ);
            $foto = $result->foto;
            if($foto == ""){
                $qr = DB::execute("UPDATE sdrow_users SET foto = ? WHERE username = ?", [
                    $path,
                    $username
                ]);
            }else{
                $pathAtual = getcwd();
                $foto = $pathAtual . $foto;
                if(file_exists($foto)){
                    if(unlink($foto)){
                        $qr = DB::execute("UPDATE sdrow_users SET foto = ? WHERE username = ?", [
                            $path,
                            $username
                        ]);
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }
            if($qr->count()>0){
                return true;
            }else{
                return false;
            }
        }

        public function getLikesByUsername($username){
            $qr = DB::execute("SELECT pl.username_usuario, u.foto FROM sdrow_posts_likes AS pl INNER JOIN sdrow_posts p ON (pl.id_post=p.id) 
            INNER JOIN sdrow_users u ON (pl.username_usuario=u.username) WHERE p.username_usuario = ?",[$username]);
            if($qr->count()>0){
                $dadosLikes = $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
            }else{
                $dadosLikes = 0;
            }
            return $dadosLikes;
        }

        public function getCommentsByUsername($username){
            $qr = DB::execute("SELECT pc.username_usuario, u.foto FROM sdrow_posts_comments AS pc INNER JOIN sdrow_posts p ON (pc.id_post=p.id) 
            INNER JOIN sdrow_users u ON (pc.username_usuario=u.username) WHERE p.username_usuario = ?", [$username]);
            if($qr->count()>0){
                $dadosComments = $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
            }else{
                $dadosComments = 0;
            }
            return $dadosComments;
        }  

        public function getFollowersByUsername ($username){
            $qr = DB::execute("SELECT f.username_usuario AS username, u.foto FROM sdrow_users_follow AS f INNER JOIN sdrow_users u ON (f.username_usuario=u.username)  WHERE username_usuario_followed = ?", [$username]);
            if($qr->count()>0){
                $dadosFollowers = $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
            }else{
                $dadosFollowers = 0;
            }
            return $dadosFollowers;
        }

        

        private function getDados(){
            $qr = DB::execute("SELECT * FROM sdrow_users WHERE username = ?", [Start::session('username')]);
            if($qr->count() > 0){
                return $qr->list(\PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }

        public function getDadosByUsername($user){
            $qr = DB::execute("SELECT u.username, u.nome, u.email, u.telefone, u.biografia, u.foto, GROUP_CONCAT(p.id) as posts FROM sdrow_users u LEFT JOIN sdrow_posts p on (u.username=p.username_usuario) WHERE username = ?", [$user]);
            if($qr->count() > 0){
                return $qr->list(\PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }

        public static function getQtdLikes($idPost){
            $qr = DB::execute("SELECT COUNT(id_post) FROM sdrow_posts_likes WHERE id_post = ?",[$idPost]);
            if($qr->count() > 0){
                return $qr->generico()->fetchColumn();
            }else{
                return false;
            }
        }

        public static function getPosts(){
            $qr = DB::execute('SELECT p.*, u.nome, u.foto as imagemUsuario, u.username, pl.id as liked FROM sdrow_posts as p INNER JOIN sdrow_users u ON (u.username=p.username_usuario) LEFT JOIN sdrow_posts_likes pl ON 
            (pl.username_usuario = ?) and (pl.id_post=p.id) order by p.id', [
                Start::session('username')
            ]);
            if($qr->count() > 0){
                return $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }

        public static function getPostsByUsername($username){
            $qr = DB::execute('SELECT p.imagem, p.id FROM sdrow_posts as p INNER JOIN sdrow_users u ON (u.username=p.username_usuario) WHERE u.username = ? ORDER BY p.id', [
                $username
            ]);
            if($qr->count() > 0){
                return $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }

        public static function checkFollow($username){
            $qr = DB::getInstance()->execute('SELECT * FROM sdrow_users_follow WHERE username_usuario = ? AND 
            username_usuario_followed = ?', [
                Start::session('username'), 
                $username
            ]);
            if($qr->count()>0){
                return true;
            }else{
                return false;
            }
        }

        public static function followUser($username){
            if(self::checkFollow($username)){
                $qr = DB::getInstance()->execute('DELETE FROM sdrow_users_follow WHERE username_usuario = ? AND 
                username_usuario_followed = ?',[
                    Start::session('username'),
                    $username
                ]);
                if($qr->count()>0){
                    $qtdFollowers = self::getQtdFollowersByUsername($username);
                    $arr = [
                        'status' => true,
                        'msg' => 'unfollowed',
                        'qtdFollowers' => $qtdFollowers
                    ];
                }else{
                    $arr =[
                        'status' => false,
                        'msg' => 'unfollowError'
                    ];
                }
            }else{
                $qr = DB::getInstance()->execute("INSERT INTO sdrow_users_follow(username_usuario,username_usuario_followed) 
                VALUES (?,?)", [
                    Start::session("username"),
                    $username
                ]);
                if($qr->count()>0){
                    $qtdFollowers = self::getQtdFollowersByUsername($username);
                    $arr = [
                        'status' => true,
                        'msg' => 'followed',
                        'qtdFollowers' => $qtdFollowers
                    ];
                }else{
                    $arr =[
                        'status' => false,
                        'msg' => 'followError'
                    ];
                }
            }
            return $arr;
        }

        public static function getDadosUserByUsername($username){
            $qr = DB::execute("SELECT * FROM sdrow_users WHERE username = ?", [
                $username
            ]);
            if($qr->count() > 0){
                return $qr->list(\PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }

        public static function getPostsCurtidosByUsername($username){
            $qr = DB::execute("SELECT pl.username_usuario, pl.id_post, p.imagem FROM sdrow_posts_likes AS pl INNER JOIN sdrow_posts p ON (pl.id_post=p.id) WHERE pl.username_usuario = ? ORDER BY pl.id_post", [
                $username
            ]);
            if($qr->count() > 0){
                return $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }

        public static function getQtdFollowersByUsername($username){
            $qr = DB::execute("SELECT COUNT(username_usuario) AS followers FROM sdrow_users_follow WHERE username_usuario_followed = ?", [
                $username
            ]);
            if($qr->count() > 0){
                return $qr->generico()->fetchColumn();
            }else{
                return false;
            }
        }

        public static function getQtdFollowingByUsername($username){
            $qr = DB::execute("SELECT COUNT(username_usuario) AS qtdFollowing FROM sdrow_users_follow WHERE username_usuario = ?", [
                $username
            ]);
            if($qr->count() > 0){
                return $qr->generico()->fetchColumn();
            }else{
                return false;
            }
        }
    }