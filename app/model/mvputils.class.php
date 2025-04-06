<?php
namespace Sdrow;
use JetPHP\Model\DB;
use JetPHP\Model\Start;
use JetPHP\Model\Criptography;

class MVPUtils {
    private $logado = false;

    public function login() {
        $username = Start::post('username');
        $pass = Criptography::md5(Start::post('senha'));
        
        $query = DB::execute("SELECT username as id FROM sdrow_users WHERE username = ? and senha = ?", [$username, $pass]);
        if ($query && $query->count() > 0) {
            $_SESSION['username'] = $query->list()->id;
            return true;
        } else {
            return false;
        }
    }

    public function register() {
        $nome = Start::post('nome');
        $email = Start::post('email');
        $telefone = Start::post('telefone');
        $idade = Start::post('idade');
        $username = Start::post('username');
        $pass = Start::post('senha');
        $confirmarSenha = Start::post('confirmarSenha');

        if($pass != $confirmarSenha){
            return 2; //As senhas informadas são diferentes
        }
        
        $queryVerifica = DB::execute("SELECT * FROM sdrow_users WHERE email = ?", [$email]);
        if($queryVerifica->count()>0){
            return 3; //Este email já está cadastrado
        }

        $queryVerifica = DB::execute("SELECT * FROM sdrow_users WHERE telefone = ?", [$telefone]);
        if($queryVerifica->count()> 0){
            return 4; //Este telefone já está cadastrado
        }

        $queryVerifica = DB::execute("SELECT * FROM sdrow_users WHERE username = ?", [$username]);
        if($queryVerifica->count()>0){
            return 5;
        }

        $query = DB::execute('INSERT INTO sdrow_users (nome, username, idade, telefone, email, senha) VALUES (?,?,?,?,?,?)', [
            $nome,
            $username,
            $idade,
            $telefone,
            $email,
            Criptography::md5($pass)
        ]);

        if ($query && $query->count() > 0) {
            $query2 = DB::execute('SELECT username AS id FROM sdrow_users WHERE username = ?', [$username]);
            if ($query2 && $query2->count() > 0) {
                $_SESSION["username"] = $query2->list()->id;
            }
            return true;
        } else {
            return false;
        }
    }

    public function isOnline() {
        if (isset($_SESSION["username"])) {
            $this->logado = true;
        }
        return $this->logado;
    }
}