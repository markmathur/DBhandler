<?php

declare(strict_types = 1);
namespace DBhandler;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL ^ E_DEPRECATED);

// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_report(MYSQLI_REPORT_STRICT);


use DBhandler\DeletePostWithId;
use DBhandler\GetPostWithId;
use DBhandler\StorePost;
use mysqli;
use mysqli_driver;

require_once 'lib/miscRequireFunctions.php';

class DBhandler {

  // ABOUT
  // This is supposed to be an allround database handler which
  // can be used in all projects, using a MySQL server and PHP. 
  
  // The methods of this class requires incoming requests to pass objects of 
  // custom made classes (found in the same folder) which 
  // specify what data is needed. 

  // CONSTANTS
  const DATABASE = 'databaseName'; 
  const TABLE = 'tableName';
  const POSTDATA = 'dataAccToColumns';
  const ARRAYWITHID = "arrayWithId";
  const ARRAYWITHCRIT = 'arrayWithCriteria';
  const INCSQL = 'incomingSQLstatement';


  // PROPERTIES

  private $stmtHandler; 

  private string $incomingCriteriaColumn;
  private string $incomingCriteriaValue;
  private array $incomingUpdateDataAsArray; // This is used by updatePost(). It might be possible to switch it out for $stringOfColumns and $stringOfValues.

  private string $database;
  private string $table;

  private string $stringOfColumns;
  private string $stringOfValues;

  private array $postData;

  // SUPPORTING LIB

  function __construct()
  {
    $this->stmtHandler = new StmtHandler($this);
  }

  // *** PUBLIC METHODS ***
  function storePost(StorePost $dbParametersAndPostData): bool {
    try {
      $dbConn = $this->unpackDataAndOpenDBconnection($dbParametersAndPostData);
      $success = $this->stmtHandler->storePost($dbConn, $this->postData);
    }
    catch (\Exception $e){
      throw $e;
    }
   
    return $success; // Returns true or false.
  }

  // DEPRACATED - use getPostsByCriteria instead. 
  // function getPostWithId(GetPostWithId $dbParametersAndId): ?array {
  //   // Returns 1-level array with user properties as array items.
  //   // Or null;
  //   $dbConn = $this->unpackDataAndOpenDBconnection($dbParametersAndId);
  //   $result = $this->stmtHandler->getPostWithId($dbConn);

  //   return $this->handleResultOfSELECTdbCalls($result);
  // }

  function getPostsByCriteria(GetPostsByCriteria $dbParametersAndId): ?array {
    // This should not replace getPostWithId because getting with id
    // is the only reading method that guarantees only ONE post. 

    // Returns an array of assoc. arrays. Every assoc. array is a post.
    $dbConn = $this->unpackDataAndOpenDBconnection($dbParametersAndId);
    $result = $this->stmtHandler->getPostsByCriteria($dbConn, $this->postData);
    return $this->handleResultOfSELECTdbCalls($result);
  }

  function updatePost(UpdatePost $dbParametersAndUpdatedPost): bool {
    // This only updates one field. Let an interface handle a whole post.
    $dbConn = $this->unpackDataAndOpenDBconnection($dbParametersAndUpdatedPost);
    $success = $this->stmtHandler->updatePost($dbConn, $this->postData);

    return $success; // Returns true or false;
  }

  // Posts should not be deleted with any other identification than id, as 
  // that is the only guaranteed unique property.
  function deletePostWithId(DeletePostWithId  $dbParametersAndId): ?int {
    $dbConn = $this->unpackDataAndOpenDBconnection($dbParametersAndId);
    $result = $this->stmtHandler->deletePostWithId($dbConn);
    // $result can be false (failure), 0 (non-existant post) or 1 (deleted a post).
    return $result;
  }


  // *** PRIVATE SUPPORTING METHODS ***

  public function handleResultOfSELECTdbCalls($result): ?array {
    if($result === false)
      throw new \Exception('ERROR: DBhandler could not complete call to database.');

    if(is_array($result) && sizeof($result) === 0)
      return null;
    
    return $result;
  }

  private function unpackDataAndOpenDBconnection($incData) {
    try {
      $unpacker = new Unpacker($this);
      $unpacker->unpackIncomingDataArray($incData); 
      $dbConn = $this->connectToServer();
    }
    catch (\Exception $e){
      throw $e;
    }
    
    return $dbConn;
  }

  private function connectToServer() {
    $dbConn = mysqli_connect(\ENV::dbServer, \ENV::dbUsername, \ENV::dbPassword, $this->database);
  
    if(!$dbConn) {
      throw new \Exception('Can not establish connection to database.', 500);
    }

    return $dbConn;
  }

  private function performDBcall($dbConn, $sql) {
    try {
      
      $result = mysqli_query($dbConn, $sql); // For security reasons, mysqli_query will not execute multiple queries to prevent SQL injections.
      return $result;
    }
    catch (\Exception $e) {
      echo $e->getMessage();
    }
  }

  private function getPostAsArray($rawData) {
    // used by READING functions, after they have fetched the "row" from the database.
    if(mysqli_num_rows($rawData) > 0) {
      $postAsArray = mysqli_fetch_assoc($rawData);
      return $postAsArray;
    }
  }

  private function getAllPostsAsArray($rawData) {
    $allRowsArray = array();
    try {
      if($rawData !== false) {
        while ($row = mysqli_fetch_assoc($rawData)) {
          array_push($allRowsArray, $row);
        }  
      }
      else {
        return false;
      }
    }
    catch (\Exception $e) {
      return false;
    }
    

    return $allRowsArray;
  }

  // *** GETTERS AND SETTERS ***
  function setDatabase(string $dbName) {$this->database = $dbName;}
  function getDatabase() {return $this->database;}

  function setTable(string $tableName) {$this->table = $tableName;}
  function getTable() {return $this->table;}

  function setIncomingCritColumn(string $col) {
    $this->incomingCriteriaColumn = $col . \ENV::dbColSuffix; // This adds the encyption suffix to the column name.
  }
  function getIncomingCritColumn() {return $this->incomingCriteriaColumn;}

  function setIncomingCritValue(string $val) {$this->incomingCriteriaValue = $val;}
  function getIncomingCritValue() {return $this->incomingCriteriaValue;}

  function setIncomingUpdateDataAsArray(array $arr) {$this->incomingUpdateDataAsArray = $arr;}
  function getIncomingUpdateDataAsArray() {return $this->incomingUpdateDataAsArray;}

  function setStringOfColumns(string $soc) {$this->stringOfColumns = $soc;}
  function getStringOfColumns() {
    return $this->stringOfColumns;
  }

  function setStringOfValues( string $sov) {$this->stringOfValues = $sov;}
  function getStringOfValues() {return $this->stringOfValues; }

  function setPostData(array $pd) {$this->postData = $pd;}
  function getPostData() {return $this->postData;}


  // *** TEMPORARY FUNCTIONS FOR DEVELOPMENT. ***
  
  function getAllPosts(GetAllPosts $dbParameters): ?array {
    $dbConn = $this->unpackDataAndOpenDBconnection($dbParameters);
    $sql = "SELECT * FROM {$this->getTable()};";
    $rawData = $this->performDBcall($dbConn, $sql);
    $result = $this->getAllPostsAsArray($rawData);
    if(!$result) $result = null;
    $dbConn->close();

    return $this->handleResultOfSELECTdbCalls($result); // Returns an array of user arrays, or null.
  }

}

