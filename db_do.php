<?php

/*
author:kermit
date:2011-12-23
notes:the database and the database operator functions
email:kermit2011@126.com
*/

class db_do{

    //dabataseset if you modify your database you should modife the following settings appropriate

    private $db_dsn="mysql:host=localhost;dbname=webdata";

    private $db_username="root";

    private $db_password="123456";

    private $db_dotype=array(PDO::ATTR_PERSISTENT => true);

    private $db_querychar="set names utf8;";

    private $errmode='PDO::ERRMODE_EXCEPTION';

    //end the modify area 


    public  $dblink=null;

    private $sql,$sql_end,$result;

    private $table,$num,$i;

    private $array=array(),$result_array=array(),$sql_array=array();


    //the link database function for all other dbdo use

    public function db_link(){

        if($this->dblink!=null) return $this->dblink;

        try{ $this->dblink = new PDO($this->db_dsn,$this->db_username,$this->db_password,$this->db_dotype);

        $this->dblink->query($this->db_querychar);

        $this->dblink->setAttribute(PDO::ATTR_ERRMODE,$this->errmode);	

        }catch (PDOException $e){ 

        echo 'Connection failed: ' . $e->getMessage();exit();}

        return $this->dblink;

	}


    //select table and get back data  with a sql statement but not contain any other Parameter

    public function db_select($sql){

        if(!$sql){echo "the sql statement is null";

                  exit();}

        $dblink = $this->db_link();

        $result=$dblink->query($sql,PDO::FETCH_ASSOC);

        if($dblink->errorCode()!='00000'){

            var_dump($dblink->errorInfo());

            echo "<br><br>the error happened at the sqlstatement:$sql";

                  exit;} 

        return $result;	

	}


    //update or delete table data and only bace impact nums of line

    public function db_upandde($sql){

        if(!$sql){echo "the sql statement is null";

                  exit();}

        $dblink = $this->db_link();

        $result=$dblink->exec($sql);

        if($dblink->errorCode()!='00000'){

            print_r($dblink->errorInfo());		

            echo "<br><br>the error happened at the sqlstatement:$sql";

                  exit;} 		

        return $result;

	}


    //insert into the table and get the key column's value of the inserted row

    public function db_insertget($sql){

        if(!$sql){
            
            echo "the sql statement is null";

            exit();
        
        }

        $this->db_select($sql);

        return $this->dblink->lastInsertId();

	}


    //get all columns name from one table

    public function db_gettab_field($table){

        if(!$table){
            
            echo "you have not assign the table you want to do";

            exit();
                 
        }

        $dblink = $this->db_link();

        $result=$dblink->query(" select * from $table limit 1 ");

        $num=$result->columnCount();

        for($i = 0 ; $i < $num ; $i++ ){		

            $table=$result->getColumnMeta($i);

            $result_array[$table['name']]=$table['name'];		

        }

        return $result_array;

    }


    //insert into one table mutiful data

    public function db_insert_mutidata($table,$array){ 

		$result_array=$this->db_gettab_field($table);

		foreach($array AS $key=>$value){

            $valur = addslashes($value);

			if(!in_array($key,$result_array)){

				echo "<br><br>the column:$key is not in the table:$table";

				continue;
           
            }

			$sql_array[]="$key='$value'";
			
		}

		$sql="insert ignore into $table set ".implode(',', $sql_array);

		return $this->db_insertget($sql);

	}


    //update one table and change mutiful data

    public function db_update_mutidata($table, $array, $sql_end='1'){ 

		$result_array=$this->db_gettab_field($table);

		foreach($array AS $key=>$value){

            $value = addslashes($value);

			if(!in_array($key,$result_array)){

				continue;
                
            }

			$sql_array[]="$key='$value'";

		}

		$sql="update $table set ".implode(',',$sql_array)." where $sql_end";

		return $this->db_upandde($sql);

	}


    //return the array data; often used when get one record

    public function db_getone($sql){

        $result=$this->db_select($sql);	

        return $result->fetch();

    }

    
    //get record num

    public function db_getallnum($table,$sql='',$i='*'){

        if($table=='') exit('you have not assign the data table:');	

        $result=$this->db_getone("select count($i) as allnum from $table $sql ");

        return $result['allnum'];

	}


    //close dblink

    public function db_close(){
        
        @$this->dblink->closeCursor();

        unset($sql,$sql_end,$result,$table,$num,$i);

        unset($array,$result_array,$sql_array);

	}

}