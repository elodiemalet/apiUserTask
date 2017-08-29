  <?php

  class SPDO {
    /**
    * Instance de la classe PDO
    *
    * @var PDO
    * @access private
    */ 
    private $PDOInstance = null;

    /**
    * Instance de la classe SPDO
    *
    * @var SPDO
    * @access private
    * @static
    */ 
    private static $instance = null;

    /**
    * Constante: nom d'utilisateur de la bdd
    *
    * @var string
    */
    const DEFAULT_SQL_USER = 'tasktool';

    /**
    * Constante: hôte de la bdd
    *
    * @var string
    */
    const DEFAULT_SQL_HOST = 'localhost';

    /**
    * Constante: hôte de la bdd
    *
    * @var string
    */
    const DEFAULT_SQL_PASS = 'Task Tool';

    /**
    * Constante: nom de la bdd
    *
    * @var string
    */
    const DEFAULT_SQL_DTB = 'tasktool';

    /**
    * Constructeur
    *
    * @param void
    * @return void
    * @see PDO::__construct()
    * @access private
    */
    private function __construct() {
        $this->PDOInstance = new PDO('mysql:dbname='.self::DEFAULT_SQL_DTB.';host='.self::DEFAULT_SQL_HOST,self::DEFAULT_SQL_USER ,self::DEFAULT_SQL_PASS);    
      }

    /**
    * Crée et retourne l'objet SPDO
    *
    * @access public
    * @static
    * @param void
    * @return SPDO $instance
    */
    public static function getInstance() {  
        if(is_null(self::$instance)) {
            self::$instance = new SPDO();
        }
        return self::$instance;
    }

    public function query($query, $params = array())
    {
      if(!empty($params)) {

        $sth = $this->PDOInstance->prepare($query);     
        foreach($params as $k => $v) {
          $sth->bindValue($k, $v);
        }
        
        return $sth->execute();
      } else {

        return $this->PDOInstance->query($query);
      }
    }

    public function fetch($query, $params = array())
    {
      $sth = $this->PDOInstance->prepare($query);

      foreach($params as $k => $v) {
        $sth->bindValue($k, $v);
      }

      $sth->execute();
      return $sth->fetch(PDO::FETCH_OBJ);
    }

    public function lastInsertId() {
      return $this->PDOInstance->lastInsertId();
    }

    public function fetchAll($query, $params = array())
    {
      $sth = $this->PDOInstance->prepare($query);

      foreach($params as $k => $v) {
        $sth->bindValue($k, $v);
      }

      $sth->execute();
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
  }