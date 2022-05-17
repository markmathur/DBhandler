<?php

namespace DBhandler;

use DBhandler\DBhandler;

class UpdatePost {

  // Properties are declared in constructor. They are  given names by 
  // constants from DBHandler, to ensure that DBhandler is information expert.

  public function __construct(string $db, string $tbl, array $idArr, array $dt)
  {
    $this->{DBhandler::DATABASE} = $db;
    $this->{DBhandler::TABLE} = $tbl;
    $this->{DBhandler::ARRAYWITHID} = $idArr;
    $this->{DBhandler::POSTDATA} = $dt;
  }

}