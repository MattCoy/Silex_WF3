<?php

namespace WF3\DAO;

class ArticleDAO extends DAO
{
    

    public function findALLOrderByDate($desc=''){
        $desc = $desc == 'DESC' ? 'DESC' : '';
        $sql = 'SELECT * FROM '. $this->getTableName() . ' ORDER BY date_publi ' . $desc;
        $result = $this->getDb()->fetchAll($sql);
        
        
        // Convert query result to an array of domain objects
        $objects = array();
        foreach ($result as $row) {
            $objects[$row['id']] = $this->buildObject($row);
        }
        return $objects;
    }

    
    
}