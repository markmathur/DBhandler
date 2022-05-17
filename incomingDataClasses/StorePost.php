<?php

namespace DBhandler;

use DBhandler\DBhandler;

class StorePost {

  // Properties are declared in constructor, given names by 
  // constants from DBHandler, to ensure that it is information expert.

  public function __construct(string $db, string $tbl, array $dt)
  {
    $this->{DBhandler::DATABASE} = $db;
    $this->{DBhandler::TABLE} = $tbl;
    $this->{DBhandler::POSTDATA} = $dt;
  }

}