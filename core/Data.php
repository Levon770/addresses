<?php
namespace core;

use core\Db;
class Data
{
    public $db_params ;
    public $tableName = 'addresses';

    public function __construct($config){
        $this->db_params = $config['db'];
    }

    /**
     * Checking table existans
     */
    public function checkScheme(){
        $db = new Db();
        $db->db_params = $this->db_params;

        $db->check($this->tableName);
    }

    /**
     * Inserting data from xml file to Mysql Database
     * @param $xml
     * @return bool
     */
    public function import($xml){
        $db = new Db();
        $db->db_params = $this->db_params;

        return $db->MultiInsert($this->tableName, simplexml_load_string( file_get_contents($xml) ));
    }

    /**
     * Find Record By id
     * @param $id
     * @return mixed
     */
    public function findOne($id){
        $db = new Db();
        $db->db_params = $this->db_params;

        $query = 'SELECT addresses_id, addresses_address, addresses_street, addresses_street_name, addresses_street_type,addresses_adm,addresses_adm1,addresses_adm2, x(coords) as lat, y(coords) as lng FROM '.$this->tableName.'
                  WHERE addresses_id = :id ';

        $res = $db->select($query, [':id'=>$id]);
        return  $res;
    }

    /**
     * Finding record with  Distance < 5 Km
     * Using haversines formula
     * @param $lat
     * @param $lng
     * @return mixed
     */
    public function findSmaller($lat, $lng){
        $db = new Db();
        $db->db_params = $this->db_params;
        $query = 'SELECT addresses_id, addresses_address, addresses_street, addresses_street_name, (
                      6371 * acos(
                        cos(radians('.$lat.')) * cos(radians(x(coords))) * cos(radians(y(coords)) - radians('.$lng.'))
                        +
                        sin(radians('.$lat.')) * sin(radians(x(coords)))
                      )
                    ) AS distance
                    FROM addresses
                    HAVING distance < 5
                    ORDER BY distance ASC;
                    ';
        $res = $db->selectAll($query);
        return  $res;
    }

    /**
     * Finding record with  Distance > 5 Km AND <30 Km
     * Using haversines formula
     * @param $lat
     * @param $lng
     * @return mixed
     */
    public function findMiddle($lat, $lng){
        $db = new Db();
        $db->db_params = $this->db_params;
        $query = 'SELECT addresses_id, addresses_address, addresses_street, addresses_street_name, (
                      6371 * acos(
                        cos(radians('.$lat.')) * cos(radians(x(coords))) * cos(radians(y(coords)) - radians('.$lng.'))
                        +
                        sin(radians('.$lat.')) * sin(radians(x(coords)))
                      )
                    ) AS distance
                    FROM addresses
                    HAVING distance >= 5 AND distance < 30
                    ORDER BY distance ASC;
                    ';
        $res = $db->selectAll($query);
        return  $res;
    }

    /**
     * Finding record with  Distance > 30 Km
     * Using haversines formula
     * @param $lat
     * @param $lng
     * @return mixed
     */
    public function findHigher($lat, $lng){
        $db = new Db();
        $db->db_params = $this->db_params;


        $query = 'SELECT addresses_id, addresses_address, addresses_street, addresses_street_name, (
                      6371 * acos(
                        cos(radians('.$lat.')) * cos(radians(x(coords))) * cos(radians(y(coords)) - radians('.$lng.'))
                        +
                        sin(radians('.$lat.')) * sin(radians(x(coords)))
                      )
                    ) AS distance
                    FROM addresses
                    HAVING distance >=  30
                    ORDER BY distance ASC;
                    ';

        $res = $db->selectAll($query);
        return  $res;
    }


    /**
     * Searching records in Database
     * @param $txt
     * @return array
     */
    public function search($txt){
        $numeric = [];
        $text=[];

        $txt =  preg_replace('/\s\s+/', ' ', $txt); //droping white spaces
        $txt_arr = explode(' ',$txt); // converting to array

        /** Finding only word without numeric to query at street name
        *   Ex. drop the "1-я" in query  WHERE criteria
        *   Only words like: "Владимирская", "Тверская-Ямская"
        */
        foreach($txt_arr as $t){
            if(preg_match("/\\d/", $t) == 0){
                $text=$t;
            }else{
                $numeric[] = $t; // dropped part will be checked in below iteraction
            };
        }

        $db = new Db();
        $db->db_params = $this->db_params;

        $query = 'SELECT addresses_id, addresses_address, addresses_street, addresses_street_name FROM '.$this->tableName.'
                  WHERE addresses_street_name LIKE "%'.$text.'%"';


        // Select records from databse matching only "word" posted
        $res = $db->selectAll($query);

        // extra filter numeric contained words from $numberic array
        $global_result=[];
        foreach($res as $r){

            // if needed extra filter
            if(count($numeric)>0){
                for($i=0; $i<count($numeric);$i++){
                    if(preg_match("/$numeric[$i]/", $r['addresses_address']) > 0 || preg_match("/$numeric[$i]/", $r['addresses_street']) > 0){
                        $global_result[]=$r;
                    }
                }
            }else{
                //if no extra filter needed put record to array which will be return as data
                $global_result[]=$r;
            }
        }

        return $global_result;
    }

}