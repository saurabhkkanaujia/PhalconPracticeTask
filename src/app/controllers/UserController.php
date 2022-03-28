<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Logger;


class UserController extends Controller
{

    public function IndexAction()
    {
    }

    public function signupAction()
    {
        if ($this->request->isPost()) {
            $user = new Users();
            $obj = new App\Components\Myescaper();

            $inputData = array(
                'email' => $obj->sanitize($this->request->getPost('email')),
                'password' => $obj->sanitize($this->request->getPost('password'))
            );

            $user->assign(
                $inputData,
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
                $this->signup_logger->error("Not Register succesfully due to following reason: <br>" . implode("<br>", $user->getMessages()));
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

                $obj = new App\Components\Myescaper();
                $email = $obj->sanitize($postData['email']);
                $password = $obj->sanitize($postData['password']);

                $user = Users::find([
                    'conditions' => 'email= :email: AND password = :password:',
                    'bind' => [
                        'email' => $email,
                        'password' => $password,
                    ]
                ]);

                if (count($user) == 0) {
                    $this->login_logger->error("Invalid Credentials");

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
                        // $response = new Response();
                        $this->response->setCookies($cookie);
                        // $this->response->send();
                    }
                    $this->session->loginUser = $user[0]->email;
                    $this->response->redirect('panel/dashboard');
                }
            }
        }
    }
}
