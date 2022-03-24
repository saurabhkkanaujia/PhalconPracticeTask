<?php

use Phalcon\Mvc\Controller;

class PanelController extends Controller{

    public function IndexAction(){

    }

    public function dashboardAction(){
        // // if ($this->session->loginUser == null) {
        //     print_r(isset(json_decode($this->cookies->get('remember-me')->getValue())->email));
        //     die();
        if (json_decode($this->cookies->get('remember-me')->getValue())->email == null) {
            $this->response->redirect('user/signin');
        } else {
            $this->view->time = $this->time;
        }
    }
    
}