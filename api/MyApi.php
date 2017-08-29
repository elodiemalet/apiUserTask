<?php

require_once 'Rest.php';

class MyApi extends Rest_Api {
    
    public $pdo;

    public function __construct($request, $origin) {
        $this->pdo = SPDO::getInstance();
        parent::__construct($request);
    }

    /* Get user infos */
    protected function user() {
        switch ($this->verb) {
            case "get":
                if ($this->method == 'GET') {
                    $user = new User($this->pdo, $this->args[0]); 
                    if(!$user->getId()){
                        return array("status" => "error", "message" => "No user found");
                    }                   
                    return $user->getUserInfos();
                } 
                else {
                    
                    return array("status" => "error", "message" => "Only accepts GET requests");
                    
                }
                break;
            case "tasks" :
                if ($this->method == 'GET') {
                    $user = new User($this->pdo, $this->args[0]); 
                    if(!$user->getId()){
                        return array("status" => "error", "message" => "No user found");
                    }                   
                    return $user->getUserTasks();
                } 
                else {
                    
                    return array("status" => "error", "message" => "Only accepts GET requests");
                    
                }
                break;
            case "assign" :  
                if ($this->method == 'PUT') {
                    $datas = json_decode(file_get_contents("php://input"));
                    if($datas && isset($datas->task_id)) {

                        $user = new User($this->pdo, $this->args[0]);
                        if(!$user->getId()){
                            return array("status" => "error", "message" => "No user found");
                        }
                        
                        $assigned = $user->assignTask($datas->task_id);
                        
                        if($assigned){
                            return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                        } else {
                            return array("status" => "error", "message" => "Task already assigned or not found");
                        }
                    }
                    return array("status" => "error", "message" => "Datas are not valid");
                } 
                else {
                    return array("status" => "error", "message" => "Only accepts PUT requests");
                }
                break;
            case "post":
                if ($this->method == 'PUT') {
                    $datas = json_decode(file_get_contents("php://input"));
                    if($datas && isset($datas->name) && isset($datas->email) ) {

                        $user = new User($this->pdo);
                        
                        $user->setName($datas->name);
                        $user->setEmail($datas->email);
                        
                        $user->addUser();
                        
                        return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                    }
                    return array("status" => "error", "message" => "Datas are not valid");
                } 
                else {
                    return array("status" => "error", "message" => "Only accepts PUT requests");
                }
                break;
            case "delete":
                if ($this->method == 'DELETE') {
                    $user = new User($this->pdo, $this->args[0]);
                    
                    if(!$user->getId()){
                        return array("status" => "error", "message" => "No user found");
                    }   
                    $user->deleteUser();

                    return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                } 
                else {
                    return array("status" => "error", "message" => "Only accepts DELETE requests");
                }
                break;
            case "put":
                if ($this->method == 'POST') {
                    $datas = json_decode(file_get_contents("php://input"));

                    if($datas) {

                        $user = new User($this->pdo, $this->args[0]);
                        
                        if(!$user->getId()){
                            return array("status" => "error", "message" => "No user found");
                        }   

                        if(!empty($datas->name)) {
                            $user->setName($datas->name);
                        }
                        if(!empty($datas->email)) {
                            $user->setEmail($datas->email);
                        }
                        
                        $user->saveUserInfos();

                        return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                    }
                    return array("status" => "error", "message" => "Datas are not valid");                        
                } 
                else {
                    return array("status" => "error", "message" => "Only accepts POST requests");
                }
                break;
            default:
                return array("status" => "error", "message" => "Endpoint does not exists");        
                break;
        }
    }

    /* Get user infos */
    protected function task() {
        switch ($this->verb) {
            case "get":
                if ($this->method == 'GET') {
                    $task = new Task($this->pdo, $this->args[0]); 
                    if(!$task->getId()){
                        return array("status" => "error", "message" => "No task found");
                    }                   
                    return $task->getTaskInfos();
                } 
                else {
                    
                    return array("status" => "error", "message" => "Only accepts GET requests");
                    
                }
                break;                
            case "post":
                if ($this->method == 'PUT') {
                    $datas = json_decode(file_get_contents("php://input"));
                    if($datas && isset($datas->title) && isset($datas->desc) && isset($datas->status)) {

                        $task = new Task($this->pdo);
                        $task->setTitle($datas->title);
                        $task->setDesc($datas->desc);
                        $task->setStatus($datas->status);

                        $task->addTask();
                        
                        if(isset($datas->user_id)) {
                            $task->assignUser($datas->user_id);
                        }
                        
                        return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                    }
                    return array("status" => "error", "message" => "Datas are not valid");
                } 
                else {
                    return array("status" => "error", "message" => "Only accepts PUT requests");
                }
                break;
            case "delete":
                if ($this->method == 'DELETE') {
                    $task = new Task($this->pdo, $this->args[0]);
                    
                    if(!$task->getId()){
                        return array("status" => "error", "message" => "No task found");
                    }   
                    $task->deleteTask();

                    return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                } 
                else {
                    return array("status" => "error", "message" => "Only accepts DELETE requests");
                }
                break;
            case "put":
                if ($this->method == 'POST') {
                    $datas = json_decode(file_get_contents("php://input"));
                    
                    if($datas) {
                        $task = new Task($this->pdo, $this->args[0]);
                    
                        if(!$task->getId()){
                            return array("status" => "error", "message" => "No task found");
                        }
                        if(!empty($datas->title)) {
                            $task->setTitle($datas->title);
                        }
                        if(!empty($datas->desc)) {
                            $task->setDesc($datas->desc);
                        }
                        if(!empty($datas->status)) {
                            $task->setStatus($datas->status);
                        }
                        
                        if(isset($datas->user_id)) {
                            $task->assignUser($datas->user_id);
                        } 
                    }
                        
                    return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                } 
                else {
                    return array("status" => "error", "message" => "Only accepts POST requests");
                }
                break;
            default:
                return array("status" => "error", "message" => "Endpoint does not exists");
                break;
        }
    }

}