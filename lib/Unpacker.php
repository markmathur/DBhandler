<?php

namespace DBhandler;

class Unpacker {
  
  private DBhandler $dbh;
  private string $stringOfColumns = '';
  private string $stringOfValues = '';

  function __construct(DBhandler $dBhandler)
  {
    $this->dbh = $dBhandler;
  }

  function unpackIncomingDataArray($incData) {
    
    $xtractor = new Extractor($this->dbh, $incData);

    $xtractor->extractDBparameters();

    if($this->incDataIsOf_StorePostClass($incData)) {
      // Creating a new post
      $xtractor->extractColumns($incData);
      $xtractor->extractValues($incData);
      $xtractor->extractPostDataAsArray($incData);

    }
    else if($this->incDataIsOf_UpdatePostClass($incData)) {
      // Updating a current post
      $xtractor->setIdColumnNameAndValue($incData);
      $this->dbh->setIncomingUpdateDataAsArray($incData->{$this->dbh::POSTDATA});
      $xtractor->extractPostDataAsArray($incData);
    }
    else if($this->itIsAnId($incData)) {
      // Reading or deleting a post
      $xtractor->setIdColumnNameAndValue($incData);
      
    }
    else if($this->itIsAsearchWithMultipleCiteria($incData)){
      $xtractor->extractColumns($incData);
      $xtractor->extractValues($incData);
      $xtractor->extractPostDataAsArray($incData);
    }
      
  }

  private function incDataIsOf_StorePostClass($incData) {
    return get_class($incData) == STR::INCNAMESPACE."StorePost";
  }

  private function incDataIsOf_UpdatePostClass($incData) {
    return get_class($incData) == STR::INCNAMESPACE . "UpdatePost";
  }

  private function itIsAnId($incData) {
    return isset($incData->{$this->dbh::ARRAYWITHID});
  }

  private function itIsAsearchWithMultipleCiteria($incData) {
    return get_class($incData) == STR::INCNAMESPACE."GetPostsByCriteria"; 
  }

  public static function takeAwayTrailingComa(&$str) {
    $str = rtrim($str, ", ");
  }

}




