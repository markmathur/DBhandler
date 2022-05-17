<?php

namespace DBhandler;

use DBhandler\DBhandler;

class Mother_targetPostWithOneCriteria {

  // The properties are not declared beforehand. The design below $this->{DBhandler::DATABASE}
  // requires that they are declared at instantiation. 

  public function __construct(string $db, string $tbl, array $arrId)
  {
    $this->{DBhandler::DATABASE} = $db;
    $this->{DBhandler::TABLE} = $tbl;

    // Id is sent as key-value pair in array, because som 
    // databases might have e.g. "user_id" or "kundnr" as 
    // incrementing database key. So we need the key and mot just the value.
    $this->{DBhandler::ARRAYWITHID} = $arrId; 
  }

}