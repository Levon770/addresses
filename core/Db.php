<?php
namespace core;

class Db {

    /**
     * Connection credentials
     * @var array
     */
    public $db_params = [];

    /**
     * PDO connection
     * @var
     */
    public $db;

    /**
     * Db constructor.
     */
    public function __construct(){}

    /**
     * Checking table existanse
     * @param $tableName
     */
    public function check($tableName){
        $this->openConnection();
        if(!$this->checkTavleExist($tableName)){
            $this->createTable($tableName);
        };
        $this->closeConnection();
    }

    /**
     * Select a records from database
     * @param $query
     * @return mixed
     */
    public function select($query, $params =null){
        $this->openConnection();
        try {
            $q = $this->db->prepare($query);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE,\ PDO::ERRMODE_EXCEPTION);
            $q->execute($params);
        } catch (\PDOException  $e) {
            $this->closeConnection();
            print "Error!: " . $e->getMessage() . "<br/>"; exit;
        }

        $this->closeConnection();
        return $q->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Select multiple records from database
     * @param $query
     * @return mixed
     */
    public function selectAll($query, $params =null){
        $this->openConnection();
        try {
            $q = $this->db->prepare($query);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE,\ PDO::ERRMODE_EXCEPTION);
            $q->execute($params);
        } catch (\PDOException  $e) {
            $this->closeConnection();
            print "Error!: " . $e->getMessage() . "<br/>"; exit;
        }

        $this->closeConnection();
        return $q->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Inserting multiple records to database with transaction
     * @param $table
     * @param $data
     * @return bool
     */
    public function MultiInsert($table, $data){
        $this->openConnection();
        $queries = $this->prepareForTransaction($table, $data);
        $this->db->beginTransaction();

        try{
            foreach ($queries as $item){
                // Attemnt to insert data record
                $q= $this->db->prepare($item);
                $q->execute();
            }

            $this->db->commit();
        }catch(Exception $e){
            print_r($e->getMessage());
            //Rollback the transaction.
            $this->db->rollBack(); exit;
        }
        return true;
    }

    /**
     * Returning array of the Sql queries
     * @param $table
     * @param $data
     * @return array
     */
    private function prepareForTransaction($table, $data){
        $queries = [];
        foreach($data as $data_item){
            $queries[]= 'INSERT INTO `'.$table.'` (`addresses_id`, `addresses_address`, `addresses_street`, 
            `addresses_street_name`, `addresses_street_type`, `addresses_adm`, `addresses_adm1`, `addresses_adm2`, `addresses_cord_x`, `addresses_cord_y`, `coords`) VALUES
            (
              '.$data_item->addresses_id.',
              "'.$data_item->addresses_address.'",
              "'.$data_item->addresses_street.'",
              "'.$data_item->addresses_street_name.'",
              "'.$data_item->addresses_street_type.'",
              "'.$data_item->addresses_adm.'",
              "'.$data_item->addresses_adm1.'",
              "'.$data_item->addresses_adm2.'",
              "'.$data_item->addresses_cord_x.'",
              "'.$data_item->addresses_cord_y.'",
              GeomFromText("POINT('.$data_item->addresses_cord_x.'  '.$data_item->addresses_cord_y.')")
            )';
        }
        return  $queries;
    }

    /**
     * Check if table exist in Database     *
     * @param $table
     * @return mixed
     */
    private function checkTavleExist($table){
        $query = 'SHOW TABLES LIKE "'.$table.'"';
        $q = $this->db->prepare($query);
        $q->execute();
        return  $q->fetch();
    }

    /**
     * Creating  table with Mysql Query     *
     * @param $table
     */
    private function createTable($table){

        $query = 'CREATE TABLE `'.$table.'` (
                      `addresses_id` int(11) NOT NULL PRIMARY KEY,
                      `addresses_address` varchar(250) NOT NULL,
                      `addresses_street` varchar(250) NOT NULL,
                      `addresses_street_name` varchar(250) NOT NULL,
                      `addresses_street_type` varchar(250) NOT NULL,
                      `addresses_adm` varchar(250) NOT NULL,
                      `addresses_adm1` varchar(250) NOT NULL,
                      `addresses_adm2` varchar(250) NOT NULL,
                      `addresses_cord_y` varchar(250) NOT NULL,
                      `addresses_cord_x` varchar(250) NOT NULL,
                      `coords`  POINT NULL DEFAULT NULL
                    )ENGINE=InnoDB default CHARSET=utf8';
        try {

            $q = $this->db->prepare($query);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE,\ PDO::ERRMODE_EXCEPTION);

            $q->execute();

        } catch (\PDOException  $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        $this->closeConnection();
    }


    /**
     * Opening PDO connection
     */
    private function openConnection(){
        try {
            $this->db = new \PDO('mysql:host='.$this->db_params['host'].';dbname='.$this->db_params['db'].';charset=utf8',$this->db_params['username'], $this->db_params['password']);

        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * Closes the currently active PDO connection
     */
    private function closeConnection(){
        $this->db =null;
    }

}