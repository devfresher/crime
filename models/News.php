<?php
require_once MODEL_DIR.'Utility.php';

class News extends Utility {

    function __construct($db) {
        $this->db = $db;
        $this->table = new stdClass();
        $this->table->news = 'news';
    }

    public function getNews($newsId) {
        $result = $this->db->getSingleRecord($this->table->news, "*", " AND id = '$newsId'");

        if ($result != NULL) {
            $this->responseBody = $this->arrayToObject($result);
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }

    public function getAllNews() {

        $result = $this->db->getAllRecords($this->table->news, "*");

        if (count($result) > 0) {
            $this->responseBody = $this->arrayToObject($result);
        }else {
            $this->responseBody = false;
        }
        
        return $this->responseBody;
    }
}