<?php

namespace DBhandler;

use DBhandler\DBhandler;

class ExecSQLstatement {

  // Properties are declared in constructor, given names by 
  // constants from DBHandler, to ensure that it is information expert.

  public function __construct(string $db, string $sql, string $tbl = null)
  {
    $this->{DBhandler::DATABASE} = $db;
    $this->{DBhandler::TABLE} = $tbl;
    $this->{DBhandler::INCSQL} = $sql;
  }

}