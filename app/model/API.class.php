<?php

    use JetPHP\Model\DB;
    use JetPHP\Model\Start;
    use JetPHP\Model\JetLoad;
    use JetPHP\Model\Criptography;
    use GRAM\MVPGram;
    class API{
        protected static function getInstance(){
            return new API();
        }

        private static function load($view, $vars=null){
            $jt = new JetLoad();
            $jt->addVars($vars);
            $jt->view($view);
        } 
        
        public static function alterAllUserData(){
            if(isset($_POST['usernameChanged']) && isset($_POST['biografiaChanged'])){
                $username = Start::session("username");
                $usernameChanged = $_POST["usernameChanged"];
                $biografiaChanged = $_POST["biografiaChanged"];
                $qr = DB::execute("SELECT * FROM sdrow_users WHERE username = ?", [$usernameChanged]);
                if($qr->count() == 0){
                    $qr = DB::execute("UPDATE sdrow_users SET username = ?, biografia = ? WHERE username = ?", [
                        $usernameChanged,
                        $biografiaChanged,
                        $username
                    ]);
                    if($qr->count()>0){
                        $arr = [
                            'status' => true,
                            'msg' => 'Dados alterados'
                        ];
                    }else{
                        $arr = [
                            'status' => false,
                            'msg' => 'Erro ao alterar dados'
                        ];
                    }
                }else if($qr->count()>0){
                    $arr = [
                        'status' => false,
                        'msg' => 'Username indisponível!'
                    ];
                }
                return $arr;
            }else{
                return [
                    'status' => false,
                    'msg' => "Nenhum dado recebido via POST"
                ];
            }
        }

        public static function alterUserDataBiografia(){
            if(isset($_POST['biografiaChanged'])){
                $username = Start::session("username");
                $biografiaChanged = $_POST["biografiaChanged"];

                $qr = DB::execute("UPDATE sdrow_users SET biografia = ? WHERE username = ?", [
                    $biografiaChanged,
                    $username
                ]);

                if($qr->count()>0){
                    $arr = [
                        'status' => true,
                        'msg' => 'Dados alterados (bigrafia)'
                    ];
                }else{
                    $arr = [
                        'status' => false,
                        'msg' => 'Erro ao alterar dados (biografia)'
                    ];
                }
                return $arr;
            }else{
                return [
                    'status' => false,
                    'msg' => "Nenhum dado recebido via POST"
                ];
            }
        }

        public static function like(){
            $gram = new MVPGram();
            $username = Start::session('username');
            $id_post = Start::post("id_post");

            if(isset($_POST['id_post'])){
                $qr = DB::execute("SELECT * FROM sdrow_posts_likes WHERE username_usuario = ? and id_post = ?", [
                    $username, $id_post
                ]);
                if($qr->count()>0){
                    $qr_deslike = DB::execute('DELETE FROM sdrow_posts_likes WHERE username_usuario = ? and id_post = ?', [
                        $username, $id_post
                    ]);
                    if($qr_deslike->count()>0){
                        $qtdLike = $gram::getQtdLikes($id_post);
                        if($qtdLike !== false){
                            $arr = [
                                'status' => 'deslikeSuccess',
                                'qtdLikes' => $qtdLike
                            ];
                        }else{
                            $arr = [
                                'status' => 'deslikeError',
                                'qtdLikes' => 'qtdLikeError',
                            ];
                        }
                    }else{
                        $arr = [
                            'status' => 'deslikeError'
                        ];
                    }
                }else{
                    $qr_like = DB::execute('INSERT INTO sdrow_posts_likes(username_usuario, id_post) VALUES (?,?)', [
                        $username, $id_post
                    ]);
                    if($qr->count()>0){
                        $qtdLike = $gram::getQtdLikes($id_post);
                        if($qtdLike!=false){
                            $arr = [
                                'status' => 'likeSuccess',
                                'qtdLikes' => $qtdLike
                            ];
                        }else{
                            $arr = [
                                'status' => 'likeError',
                                'qtdLikes' => 'qtdLikeError'
                            ];
                        }
                        
                    }else{
                        $arr = [
                            'status' => 'likeError'
                        ];
                    }
                }
                return $arr;
            }else{
                return [
                    'status' => 'Nenhum id recebido'
                ];
            }
        }

        public static function followUser(){
            $gram = new MVPGram;
            if(isset($_POST['username'])){
                return $gram->followUser($_POST['username']);
            }else{
                return [
                    'status' => false,
                    'msg' => 'Nenhum parâmetro recebido'
                ];
            }
        }

        public static function sendPost(){
            $gram = new MVPGram;
            $username = Start::session('username');
            if(isset($_POST['path']) && isset($_POST["description"])){
                $qr = DB::execute('INSERT INTO sdrow_posts (username_usuario, imagem, descricao) VALUES (?,?,?)',[
                    $username,
                    $_POST['path'],
                    $_POST['description']
                ]);
                if($qr->count()>0){
                    $arr = [
                        'status' => true,
                        'msg' => 'Post feito com sucesso'
                    ];
                }else{
                    $arr = [
                        'status' => false,
                        'msg' => 'Erro no Post'
                    ];
                }
                return $arr;
            }else{
                return[
                    'status' => false,
                    'msg' => 'Nenhum parâmetro recebido'
                ];
            }
        }


        public static function loadPost(){
            $gram = new MVPGram;
            if(isset($_POST['id'])){
                $id = $_POST['id'];
                $qr = DB::execute("SELECT p.*, u.foto FROM sdrow_posts AS p INNER JOIN sdrow_users u on (p.username_usuario=u.username) WHERE p.id = ?", [$id]);
                if($qr->count()>0){
                    $dadosPost = $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
                    $qr = DB::execute("SELECT c.*, u.foto FROM sdrow_posts_comments AS c INNER JOIN sdrow_users u on (c.username_usuario=u.username) WHERE id_post = ?", [$id]);
                    if($qr->count()>0){
                        $dadosComentarios = $qr->generico()->fetchAll(\PDO::FETCH_OBJ);
                        $arr = [
                            'status' => true,
                            'dadosPost' => $dadosPost,
                            'dadosComentarios' => $dadosComentarios
                        ];
                    }else{
                        $arr = [
                            'status' => true,
                            'dadosPost' => $dadosPost,
                            'dadosComentarios' => 0
                        ];
                    }
                    return $arr;
                }else{
                    return [
                        'status' => false,
                        'msg' => "Publicação não encontrada"
                    ];
                }
            }else{
                return [
                    'status' => false,
                    'msg' => "Nenhum id fornecido!"
                ];
            }
        }

        public static function deletePost(){
            if(isset($_POST["id"])){
                $id = $_POST["id"];
                try{
                    $qr = DB::execute("DELETE FROM sdrow_posts_comments WHERE id_post = ?", [$id]);
                    $qr = DB::execute("DELETE FROM sdrow_posts_likes WHERE id_post = ?", [$id]);
                    $qr = DB::execute("SELECT imagem FROM sdrow_posts WHERE id = ?", [$id]);
                    $dadosPost = $qr->generico()->fetch(\PDO::FETCH_OBJ);
                    $pathAtual = getcwd();
                    $path = stripslashes($dadosPost -> imagem);
                    $path = $pathAtual . $path;
                    $qr = DB::execute("DELETE FROM sdrow_posts WHERE id = ?", [$id]);
                    if(file_exists($path)){
                        if(unlink($path)){
                            $arr = [
                                'status' => true,
                            ];
                        }else{
                            $arr = [
                                'status' => true,
                                'msg' => 'Erro na função unlink!' 
                            ];
                        }
                    }else{
                        $arr = [
                            'status' => false,
                            'msg' => 'Caminho não existe!'
                        ];
                    }  
                }catch(PDOException $e){
                    return [
                        'status' => false,
                        'msg' => $e->getMessage()
                    ];
                }
                return $arr;
            }else{
                return [
                    'status' => false,
                    'msg' => "Nenhum id fornecido!"
                ];
            }
        }

        public static function sendComment(){
            if(isset($_POST['idpost']) && isset($_POST['message']) && isset($_POST['username'])){
                $username = $_POST["username"];
                $mensagem = $_POST["message"];
                $idpost = $_POST["idpost"];

                try{
                    $qr = DB::execute("INSERT INTO sdrow_posts_comments (id_post,username_usuario,mensagem) VALUE (?,?,?)", [
                        $idpost,
                        $username,
                        $mensagem
                    ]);
                    if($qr->count()>0){
                        $arr = [
                            'status' => true,
                            'msg' => 'Comentário realizado com sucesso!'
                        ];
                    }else{
                        $arr = [
                            'status' => false,
                            'msg' => 'Erro ao realizar o comentário!'
                        ];
                    }
                }catch(PDOException $e){
                    return [
                        'status' => false,
                        'msg' => $e->getMessage()
                    ];
                }
                return $arr;
            }else{
                return [
                    'status' => false,
                    'msg' => 'Dados não recebidos via POST'
                ];
            }
        }
        
   } 