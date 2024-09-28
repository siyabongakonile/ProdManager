<?php 
declare(strict_types = 1);

namespace App\Controllers;

use App\Crypto;
use App\Database;
use App\Models\UserModel;
use App\Request;
use App\Response;
use App\Session;

class AuthController extends BaseController{
    protected UserModel $model;

    public function __construct(){
        parent::__construct();
        $this->model = new UserModel(Database::getInstance());
    }

    public function getLogin(Request $request, Response $response){
        $this->render('auth/login');
    }

    public function login(Request $request, Response $response){
        $username = $request->getPOST()['username'] ?? "";
        $password = $request->getPOST()['password'] ?? "";

        if($username == "" || $password == ""){
            $this->renderLoginWithErrors($username);
            return;
        }

        $user = $this->model->getUserByUsername($username);
        if($user == null){
            $this->renderLoginWithErrors($username);
            return;
        }

        if(!Crypto::verify($password, $user->getPassword())){
            $this->renderLoginWithErrors($username);
            return;
        }

        $session = Session::getInstance();
        $session->setIsLoggedIn($user->getId());
        $session->set('userlevel', $user->getLevel()->value);

        $response->sendToHomePage();
    }

    public function logout(Request $request, Response $response){
        session_unset();
        session_destroy();
        $response->sendToHomePage();
    }

    protected function renderLoginWithErrors(string $username){
        $this->render(
            'auth/login', 
            [
                'errors' => ["Invalid username/password"], 
                'username' => $username
            ]
        );
    }
}