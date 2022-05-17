<?php

namespace DBhandler;

class Extractor {
  private DBhandler $dbh;
  private $incData;
  private string $stringOfColumns;
  private string $stringOfValues;

  function __construct($dBhandler, $incomingDataFromRequest) {
    
    $this->dbh = $dBhandler;
    $this->incData = $incomingDataFromRequest;
    $this->stringOfColumns = '';
    $this->stringOfValues = '';

  }

  // *** PUBLIC METHODS ***

  public function extractDBparameters() {

    $this->setDBnameInDBHandler();
    $this->setTableNameInDBHandler();  
  
  }

  public function extractColumns($incData) {
    foreach($this->incData->{$this->dbh::POSTDATA} as $col => $val) {
      $this->stringOfColumns .= "{$col}, ";
    }

    $this->takeAwayTrailingComa($this->stringOfColumns);
    
    $this->dbh->setStringOfColumns($this->stringOfColumns);
  }

  public function extractPostDataAsArray($incData) {

    $this->dbh->setPostData($incData->{$this->dbh::POSTDATA});
  
  }

  // public function extractColumnsToArray($incData) {

  // }

  public function extractValues($incData) {
    foreach($incData->{$this->dbh::POSTDATA} as $col => $val) {
      $this->stringOfValues .= "'{$val}', ";
    }

    $this->takeAwayTrailingComa($this->stringOfValues);

    $this->dbh->setStringOfValues($this->stringOfValues);
  }

  public function setIdColumnNameAndValue($incData){
    
    $idArray = $this->getIdArrayFrom($incData); 
    
    if($this->arrayHasMaxOneItem($idArray) == false)
      throw new \Exception("DBhandler received to long array. Only id is needed.");
    
    $this->dbh->setIncomingCritColumn(array_keys($idArray)[0]);
    $this->dbh->setIncomingCritValue(array_values($idArray)[0]);
    
  }

  // *** PRIVATE METHODS ***

  private function setDBnameInDBHandler() {
    $this->dbh->setDatabase($this->incData->{$this->dbh::DATABASE});
  }

  private function setTableNameInDBHandler() {
    $this->dbh->setTable($this->incData->{$this->dbh::TABLE});
  }

  public static function takeAwayTrailingComa(&$str) {
    $str = rtrim($str, ", ");
  }

  private function getIdArrayFrom($incData){
    return $incData->{$this->dbh::ARRAYWITHID};
  }

  private function arrayHasMaxOneItem(array $arr) {
    return sizeof($arr) == 1;
  }

}