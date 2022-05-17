<?php

namespace DBhandler;

use DBhandler\DBhandler;

class GetAllPosts {

  // Properties are declared in constructor, given names by 
  // constants from DBHandler, to ensure that it is information expert.

  public function __construct(string $db, string $tbl)
  {
    $this->{DBhandler::DATABASE} = $db;
    $this->{DBhandler::TABLE} = $tbl;
  }

}