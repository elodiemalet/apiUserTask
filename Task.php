<?php

class task {
    private $_id;
    private $_title;
    private $_desc;
    private $_status;
    private $_creation_date;

    private $pdo;

    public function __construct($pdo, $id = null) {
        
        $this->pdo = $pdo;

        if(!is_null($id)) {       
            $sql = "SELECT * FROM task WHERE task_id = $id";
            $task_datas = $pdo->fetch($sql);
            
            if($task_datas) {
                $this->_id = $id;
                $this->_title = $task_datas->task_title;
                $this->_desc = $task_datas->task_desc;
                $this->_status = $task_datas->task_status;
                $this->_creation_date = $task_datas->task_creation_date;
            }
        }
    }

    public function getId() {
        return $this->_id;
    }

    public function setTitle($title) {
        $this->_title = $title;
    }

    public function setDesc($desc) {
        $this->_desc = $desc;
    }

    public function setStatus($status) {
        $this->_status = $status;
    }

    public function getTaskInfos() {

        return ['id' => $this->_id
        ,'title' => $this->_title
        ,'desc' => $this->_desc
        ,'status' => $this->_status
        ,'creation_date' => $this->_creation_date];
    }

    public function saveTaskInfos(){
        if($this->_id) {
            //edit
            $sql = "UPDATE task SET task_title = :title, task_desc = :desc, task_status = :status WHERE task_id = :id";
            $params = [':id' => $this->_id,':title' => $this->_title, ':desc' => $this->_desc, ':status' => $this->_status];
            return $this->pdo->query($sql, $params);
        }
        
    }

    public function deleteTask() {
        $sql = "DELETE FROM task WHERE task_id = :id";
        $params = [':id' => $this->_id];
        
        $sql_user_task = 'DELETE FROM user_task WHERE task_id = :id;';
        $this->pdo->fetch($sql_user_task, $params);

        return $this->pdo->query($sql, $params); 
    }

    public function addTask() {
        //add
        $sql = "INSERT INTO task (task_title, task_desc, task_status) VALUES (:title , :desc, :status);";
        $params = [':title' => $this->_title, ':desc' => $this->_desc,':status' => $this->_status];
        
        $this->pdo->query($sql, $params);
        
        $this->_id = $this->pdo->lastInsertId();

        return true;
    }

    public function assignUser($user_id) {
        
        $params = [':task_id' => $this->_id, ':user_id' => $user_id];
        
        $sql_user_exists = 'SELECT count(1) as cnt FROM user WHERE user_id = :user_id;';
        $user_exists = $this->pdo->fetch($sql_user_exists, [':user_id' => $user_id]);
        
        if(!$user_exists->cnt) {
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