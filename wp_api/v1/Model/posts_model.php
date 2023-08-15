<?php
class posts_model{
    private PDO $conn;
    public static $pageper = 10;
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }
    
    public function totalpage()
    {
        $sql = "SELECT COUNT(*) FROM wp_posts";
        $stmt = $this->conn->query($sql);
        $count = $stmt->fetchColumn();
        return $count;
    }
    
    public function getAll(): array
    {
        $sql = "SELECT *
                FROM wp_posts";
                
        $stmt = $this->conn->query($sql);
        
        $data = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            //$row["is_available"] = (bool) $row["is_available"];
            
            $data[] = $row;
        }
        
        return $data;
    }
    public function get(string $id): array | false
    {
        $sql = "SELECT *
                FROM wp_posts
                WHERE ID = :id";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // if ($data !== false) {
        //     $data["is_available"] = (bool) $data["is_available"];
        // }
        
        return $data;
    }
    public function getpage(string $id) :array | false
    {
        $spl_mark_page = strpos($id, '?') ?? false;
       
        if(!empty($spl_mark_page)){
            $id_qt = explode('?',strval($id));
            $id_serch = explode('=',$id_qt[1]);
            if(count($id_serch)==2)
            {
                $sql = "SELECT *
                FROM wp_posts
                WHERE ID = :id";
                $stmt = $this->conn->prepare($sql);
                
                $stmt->bindValue(":id", $id_serch[1], PDO::PARAM_INT);
                
                $stmt->execute();
                
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // if ($data !== false) {
                //     //$data["is_available"] = (bool) $data["is_available"];
                // }
                
                return $data;
            }
            else
            {
                return false;
            }
            
        }
        else
        {
        $sql = "SELECT COUNT(*) FROM wp_posts";       
        $perPage = Self::$pageper;
        $stmt = $this->conn->query($sql);
        $total_results = $stmt->fetchColumn();
        $total_pages = ceil($total_results / $perPage);
        $page = empty($id) ? 1 : $id;
        //
        $starting_limit = (intval($page) - 1) * $perPage;
        $query = "SELECT * FROM wp_posts ORDER BY id DESC LIMIT $starting_limit,$perPage";
        // Fetch all users for current page
        $stmt = $this->conn->query($query);
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //$row["is_available"] = (bool) $row["is_available"];
            $data[] = $row;
        }
        return $data;
        }
    }
    public function update(array $data) {     
        $stmt = $this->conn->prepare("DESCRIBE wp_posts");
        $stmt->execute();
        $tableInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $field = [];
        foreach ($tableInfo as $column) {
            //print_r($column['Field']);
            array_push( $field,$column['Field']);     
        }
        
        $qu = array_intersect(
            array_map('strtolower', $field),
            array_map('strtolower', array_keys($data))
        );
        $sql = "UPDATE `wp_posts` SET ";
        foreach ($qu as $key => $value) {
            if(array_key_last($qu) == $key)
            {
                $sql .= "`".$field[$key]."` = :$field[$key] ";
            }
            else if(array_key_first($qu) == $key)
            {
                //$sql .= "`".$field[$key]."` = :$field[$key] ,";
            }
            else
            {
                $sql .= "`".$field[$key]."` = :$field[$key] ,";
            }
        }
      
        $sql .= " WHERE `wp_posts`.`ID` = :ID";
        $stmt = $this->conn->prepare($sql);
        foreach ($qu as $key => $value) {
            if(array_key_last($qu) == $key)
            {
                $stmt->bindValue(":$field[$key]",$data[$field[$key]] );
            }
            else if(array_key_first($qu) == $key)
            {
                //$sql .= "`".$field[$key]."` = :$field[$key] ,";
            }
            else
            {
                $stmt->bindValue(":$field[$key]",$data[$field[$key]] );
            }
        }

        //return id in where clouse
        $stmt->bindValue(":ID", $data["id"], PDO::PARAM_INT);
        
        $stmt->execute();

        if($stmt->rowCount() == 1)
        {
            $json = $this->get($data["id"]); 
            $token = Token::Sign($json, $_SERVER['HTTP_AUTHORIZATION'], 60*5);
            return $token;
        }
        else
        {
            $json = ["info"=> $data["id"].' Allready Update'];
            $token = Token::Sign($json, $_SERVER['HTTP_AUTHORIZATION'], 60*5);
            return $token;
        }
    }
}
?>