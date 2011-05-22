<?php
/**
 * dmDoctrine aggregate pager class.
 *
 * @package    bsdmCore
 * @subpackage pager
 * @author     Roel Sint <roel@beeldspraak.com>
 */
class dmDoctrineAggregatePager extends sfPager implements Serializable
{
  /**
   * Specify the model classes to aggregate the objects from
   * array(
   * 0 => 'TwitterTweet'
   * 1 => 'bsdmArticlePost'
   * )
   * 
   * @var array
   */
  private $classes = array();
  
  /**
   * Optionally add options per model class
   * array(
   * 'bsdmArticlePost' => array(
   * 'column' => 'type'
   * 'ucfirst' => true,
   * 'prefix' => '',
   * 'suffix' => 'Post',
   * )
   * )
   *
   * @var array
   */
  private $classOptions = array();
  
  /**
   * Column that all model classes share and used to sort the results 
   * 
   * @var string
   */
  private $sortColumn = '';
  
  /**
   * Direction of the sorting
   * 
   * @var string 
   */
  private $sortDirection = '';
  
  /**
   * Raw sql query
   * 
   * @var string
   */
  private $query = '';
  
  /**
   * Raw sql count query
   * 
   * @var string
   */
  private $countQuery = '';
  
  /**
   * Offset for sql query
   * 
   * @var string
   */
  private $offset = 0;
  
  /**
   * Limit for sql query
   * 
   * @var string
   */
  private $limit = 0;
  
  /**
   * PDO connection
   *
   * @var PDO
   */
  private $conn = null;

  /**
   * Constructor.
   *
   * @param integer $maxPerPage Number of records to display per page
   */
  public function __construct($maxPerPage = 10, PDO $conn = null)
  {
    $this->setMaxPerPage($maxPerPage);
    $this->parameterHolder = new sfParameterHolder();
    
    if ( is_null($conn) ) {
      $this->conn = Doctrine_Manager::connection()->getDbh();
    }
  }

  /**
   * Serialize the pager object
   *
   * @return string $serialized
   */
  public function serialize()
  {
    $vars = get_object_vars($this);
    unset($vars['query']);
    return serialize($vars);
  }

  /**
   * Unserialize a pager object
   *
   * @param string $serialized
   */
  public function unserialize($serialized)
  {
    $array = unserialize($serialized);
    
    foreach ($array as $name => $values) {
      $this->$name = $values;
    }
  }

  /**
   * Add a model class to aggregate, for table aggregation objects you can specify how the class name
   * must be generated, fe. bsdmBlogPost:
   * * column:	specify the column name from the result to use, fe. 'blog'
   * * filter: 	array specifying the value to filter the result by, fe. array('blog', 'news')
   * * prefix: 	add before the value from the column, fe. 'Bsdm'
   * * suffix:	add after the value from the column, fe. 'Post'
   * * ucfirst:	upper case the value from the column, fe. 'Blog'
   * 
   * @param string $name
   * @param array $options
   */
  public function addModel($name, array $options = array())
  {
    $this->classes[] = $name;
    
    $this->classOptions[$name] = $options;
  }

  /**
   * Set the column that all tables share to sort by and its sort direction
   * 
   * @param string $column
   * @param string $direction
   */
  public function setSortBy($column, $direction = 'DESC')
  {
    $this->sortColumn = $column;
    $this->sortDirection = $direction;
  }

  /**
   * @see sfPager
   */
  public function init()
  {
    $this->resetIterator();
    
    $this->createSqlQueries();
    $count = $this->conn->query($this->getCountSqlQuery())->fetchColumn();
    
    $this->setNbResults($count);
    
    if ( 0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults() ) {
      $this->setLastPage(0);
    } else {
      $this->offset = ( $this->getPage() - 1 ) * $this->getMaxPerPage();
      
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
      
      $this->limit = $this->getMaxPerPage();
    }
  }

  private function createSqlQueries()
  {
    if ( count($this->classes) == 0 ) {
      throw new sfDoctrineException(sprintf('No model classes configured for %s', __CLASS__));
    }
    
    // create query and count query for each model class
    $queries = array();
    $countQueries = array();
    foreach ($this->classes as $key => $name) {
      $table = Doctrine_Core::getTable($name);
      // check sort column
      if ( !$table->hasField($this->sortColumn) || ( $table->hasI18n() && !$table->getI18nTable()->hasColumn($this->sortColumn) ) ) {
        throw new sfDoctrineException(sprintf('The class "%s" does not have the "%s" field that is configured as sort column by for "%s"', $name, $this->sortColumn, __CLASS__));
      }
      
      $tableName = $table->getTableName();
      $where = false;
      
      // add column to use to fetch the object later
      if ( isset($this->classOptions[$name]['column']) ) {
        $column = "$tableName.{$this->classOptions[$name]['column']}";
        
        if ( isset($this->classOptions[$name]['filter']) ) {
          $filterValues = array();
          foreach ($this->classOptions[$name]['filter'] as $filterValue) {
            $filterValues[] = "'$filterValue'";
          }
          $filterValues = join(',', $filterValues);
          $where = "WHERE $column IN ($filterValues)";
        }
      
      } else {
        $column = "''";
      }
      
      if ( $table->hasI18n() ) {
        $i18nTableName = $table->getI18nTable()->getTableName();
        $sortColumn = "$i18nTableName.{$this->sortColumn}";
        $culture = myDoctrineRecord::getDefaultCulture();
        $translationJoin = "LEFT JOIN $i18nTableName ON $tableName.id = $i18nTableName.id AND $i18nTableName.lang = '{$culture}'";
      } else {
        $sortColumn = "$tableName.$this->sortColumn";
      }
      
      $queries[] = "SELECT $tableName.{$table->getPrimaryKey()} AS `id`, $sortColumn AS `sort`, {$key} AS `key`, $column AS `column` FROM $tableName" . ( $table->hasI18n() ? " $translationJoin" : '' ) . ( $where ? " $where" : '' );
      $countQueries[] = "SELECT COUNT({$table->getPrimaryKey()}) as `total` FROM {$table->getTableName()}" . ( $where ? " $where" : '' );
    }
    
    // union all queries
    $unionSqlTemplate = "SELECT ar.id, ar.key, ar.column FROM (%s) AS ar ORDER BY ar.sort {$this->sortDirection}";
    $unionCountSqlTemplate = "SELECT SUM(ar.total) AS `total` FROM (%s) AS ar;";
    $this->query = sprintf($unionSqlTemplate, join(' UNION ALL ', $queries));
    $this->countQuery = sprintf($unionCountSqlTemplate, join(' UNION ALL ', $countQueries));
  }

  /**
   * Get the sql query for the pager.
   *
   * @return string
   */
  public function getSqlQuery($offset = 0, $limit = 0)
  {
    
    // add offset and limit
    return ( $offset == 0 && $limit == 0 ) ? $this->query . ";" : $this->query . " LIMIT {$offset},{$limit};";
  }

  /**
   * Returns a sql query for counting the total results.
   *
   * @return string
   */
  public function getCountSqlQuery()
  {
    
    return $this->countQuery;
  }

  /**
   * Retrieve the object for a certain offset
   *
   * @param integer $offset
   *
   * @return Doctrine_Record
   */
  protected function retrieveObject($offset)
  {
    $result = $this->conn->query($this->getSqlQuery($offset - 1, 1))->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $this->fetchObject($result['id'], $result['key'], $result['column']) : false;
  }

  /**
   * Return the model class to fetch the object
   * 
   * @param integer $key 		key of class in $this->classes
   * @param string $column	
   * @return string
   */
  private function getClassForObject($key, $column = '')
  {
    // determine class
    if ( $column ) {
      $options = $this->classOptions[$this->classes[$key]];
      $class = '';
      if ( isset($options['prefix']) ) {
        $class .= $options['prefix'];
      }
      $class .= ( isset($options['ucfirst']) && $options['ucfirst'] === true ) ? ucfirst($column) : $column;
      if ( isset($options['suffix']) ) {
        $class .= $options['suffix'];
      }
    } else {
      $class = $this->classes[$key];
    }
    
    return $class;
  }

  /**
   * Get the hydrated object for  
   * 
   * @param mixed $id
   * @param integer $key
   * @param string $column
   * @return Doctrine_Record
   */
  private function fetchObject($id, $key, $column = '')
  {
    return Doctrine_Core::getTable($this->getClassForObject($key, $column))->find($id);
  }

  /**
   * Get all the results for the pager instance
   *
   * @return array
   */
  public function getResults()
  {
    
    if ( is_null($this->results) ) {
      $results = $this->conn->query($this->getSqlQuery($this->offset, $this->limit))->fetchAll(PDO::FETCH_ASSOC);
      
      $this->results = array();
      
      foreach ($results as $result) {
        $this->results[] = $this->fetchObject($result['id'], $result['key'], $result['column']);
      }
    }
    
    return $this->results;
  }
}