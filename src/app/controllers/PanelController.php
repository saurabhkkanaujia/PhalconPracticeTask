<?php

use Phalcon\Mvc\Controller;

class PanelController extends Controller{

    public function IndexAction(){

    }

    public function dashboardAction(){
        if (json_decode($this->cookies->get('remember-me')->getValue())->email != null || $this->session->has('loginUser')) {
            $this->view->time = $this->time;
        } else {
            $this->response->redirect('user/signin');
        }
    }
    
}