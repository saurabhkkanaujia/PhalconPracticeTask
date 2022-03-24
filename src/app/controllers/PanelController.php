<?php

use Phalcon\Mvc\Controller;

class PanelController extends Controller{

    public function IndexAction(){

    }

    public function dashboardAction(){
        if ($this->session->loginUser == null) {
            $this->response->redirect('user/signin');
        } else {
            $this->view->time = $this->time;
        }
    }
    
}