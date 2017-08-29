<?php

class User {
    private $_id;
    private $_name;
    private $_email;

    private $pdo;

    public function __construct($pdo, $id = null) {
        
        $this->pdo = $pdo;

        if(!is_null($id)) {       
            $sql = "SELECT * FROM user WHERE user_id = $id";
            $user_datas = $pdo->fetch($sql);
            
            if($user_datas) {
                $this->_id = $id;
                $this->_name = $user_datas->user_name;
                $this->_email = $user_datas->user_email;
            }
        }
    }

    public function getId() {
        return $this->_id;
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function setEmail($email) {
        $this->_email = $email;
    }

    public function getUserInfos() {

        return ['id' => $this->_id
        ,'name' => $this->_name
        ,'email' => $this->_email];
    }

    public function getUserTasks() {

        $task_list = array();

        $sql = "SELECT * FROM user_task WHERE user_id = $this->_id";
        $tasks = $this->pdo->fetchAll($sql);
        
        foreach($tasks as $task) {
            $task_obj = new Task($this->pdo, $task['task_id']);
            if($task_obj->getId()) {
                $task_list[] = $task_obj->getTaskInfos();
            }
        }

        return $task_list;        
    }

    public function saveUserInfos(){
        if($this->_id) {
            //edit
            $sql = "UPDATE user SET user_name = :name, user_email = :email WHERE user_id = :id";
            $params = [':id' => $this->_id,':name' => $this->_name, ':email' => $this->_email];
            return $this->pdo->query($sql, $params);
        }
        
    }

    public function deleteUser() {
        $sql = "DELETE FROM user WHERE user_id = :id";
        $params = [':id' => $this->_id];
        
        $sql_user_task = 'DELETE FROM user_task WHERE user_id = :id;';
        $this->pdo->fetch($sql_user_task, $params);

        return $this->pdo->query($sql, $params); 
    }

    public function addUser() {
        //add
        $sql = "INSERT INTO user (user_name, user_email) VALUES (:name , :email);";
        $params = [':name' => $this->_name, ':email' => $this->_email];
        return $this->pdo->query($sql, $params);
    }

    public function assignTask($task_id) {
        
        $params = [':task_id' => $task_id, ':user_id' => $this->_id];
        
        $sql_task_exists = 'SELECT count(1) as cnt FROM task WHERE task_id = :task_id;';
        $task_exists = $this->pdo->fetch($sql_task_exists, [':task_id' => $task_id]);
        
        if(!$task_exists->cnt) {
            return false;
        }
        
        $sql_exists = 'SELECT count(1) as cnt FROM user_task WHERE user_id = :user_id AND task_id = :task_id;';
        $task_assigned = $this->pdo->fetch($sql_exists, $params);
        
        if(!$task_assigned->cnt) {

            $sql = "INSERT INTO user_task (user_id, task_id) VALUES (:user_id , :task_id);";
            $this->pdo->query($sql, $params);
            return true;
        }
        return false;
    }
}