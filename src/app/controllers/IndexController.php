<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        
        
        // return '<h1>Hello World!</h1>';
    }

    public function signoutAction() {
        $this->session->destroy();
        $this->cookies->get('remember-me')->delete();
        
        $this->response->send();
        
        $this->response->redirect('user/signin');
    }
}