<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class UserController extends Controller
{

    public function IndexAction()
    {
    }

    public function signupAction()
    {
        if ($this->request->isPost()) {
            $user = new Users();
            $user->assign(
                $this->request->getPost(),
                [
                    'email',
                    'password'
                ]
            );

            $success = $user->save();

            $this->view->success = $success;

            if ($success) {
                $this->response->redirect('user/signin');
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $user->getMessages());
            }
        }
    }

    public function signinAction()
    {
        // echo $this->cookies->has('remember-me');
        // die();
        if ($this->cookies->has('remember-me')) {
            $this->session->loginUser  = json_decode($this->cookies->get('remember-me')->getValue())->email;
            $this->response->redirect('panel/dashboard');
        } else {
            if ($this->request->isPost()) {
                $postData = $this->request->getPost();
    
                $user = Users::find([
                    'conditions' => 'email= :email: AND password = :password:',
                    'bind' => [
                        'email' => $postData['email'],
                        'password' => $postData['password'],
                    ]
                ]);
    
                if (count($user) == 0) {
                    $this->error;
                    die();
                } else {
                    if (isset($postData['checkBox'])) {
    
                        $cookie = $this->cookies;
                        $cookie->set(
                            'remember-me',
                            json_encode(
                                [
                                    'email' => $user[0]->email,
                                    'password' => $user[0]->password
                                ]
                            ),
                                time() + 3600
                        );
                        $response = new Response();
                        $response->setCookies($cookie);
                        $response->send();
                    }
                    $this->session->loginUser = $user[0]->email;
                    $this->response->redirect('panel/dashboard');
                }
            }
        }
    }
}
