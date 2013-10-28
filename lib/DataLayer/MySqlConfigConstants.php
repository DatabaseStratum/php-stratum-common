<?php
//----------------------------------------------------------------------------------------------------------------------
namespace DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/** @brief The class creates a file which contains all used constants with values.
 */
class MySqlConfigConstants
{
  /** Filename with columnnames, their widths, and constant names.
   */
  private $myConstsFilename;

  /** Template filename under which the file is generated with the constants.
   */
  private $myTemplateConfigFilename;

  /** Name of file that contains all constants.
   */
  private $myConfigFilename;

  /** All columns in the MySQL schema.
   */
  private $myColumns = array();

  /** Array with the previous column names, widths, and constant names (i.e. the content of @c $myConstsFilename upon
   *  starting this program).
   */
  private $myOldColumns = array();

  /** Array with all constants.
   */
  private $myConstants = array ();

  /** The prefix used for designations a unknown constants.
   */
  private $myPrefix;

  /**
   */
   private $myLabels = array();

  /** @name MySQL
     @{
     MySQL database settings.
   */

  /** Host name or addres.
   */
  private $myHostName;
  /** User name.
   */
  private $myUserName;
  /** Uesr password.
   */
  private $myPassword;
  /** Name used databae.
   */
  private $myDatabase;
  /** @} */

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns the value of a setting.
      @param $theSettings      The settings as returned by @c parse_ini_file.
      @param $theMandatoryFlag If set and setting @a $theSettingName is not found in section @a $theSectionName an
                               exception will be thrown.
      @param $theSectionName   The name of the section of the requested setting.
      @param $theSettingName   The name of the setting of the requested setting.
   */
  private function getSetting( $theSettings, $theMandatoryFlag, $theSectionName, $theSettingName )
  {
    // Test if the section exists.
    if (!array_key_exists( $theSectionName, $theSettings ))
    {
      if ($theMandatoryFlag)
      {
        set_assert_failed( "Section '%s' not found in configuration file.", $theSectionName );
      }
      else
      {
        return null;
      }
    }

    // Test if the setting in the section exists.
    if (!array_key_exists( $theSettingName, $theSettings[$theSectionName] ))
    {
      if ($theMandatoryFlag)
      {
        set_assert_failed( "Setting '%s' not found in section '%s' configuration file.", $theSettingName,
                                                                                         $theSectionName );
      }
      else
      {
        return null;
      }
    }

    return $theSettings[$theSectionName][$theSettingName];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Reads parameters from configuration @a $theConfigFilename
   */
  private function readConfigFile( $theConfigFilename )
  {
    $settings = parse_ini_file( $theConfigFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file" );

    $this->myHostName = $this->getSetting( $settings, true,  'database', 'host_name' );
    $this->myUserName = $this->getSetting( $settings, true,  'database', 'user_name' );
    $this->myPassword = $this->getSetting( $settings, true,  'database', 'password' );
    $this->myDatabase = $this->getSetting( $settings, true,  'database', 'database_name' );

    $this->myConstsFilename         = $this->getSetting( $settings, true,  'constants', 'columns' );
    $this->myPrefix                 = $this->getSetting( $settings, true,  'constants', 'prefix' );
    $this->myTemplateConfigFilename = $this->getSetting( $settings, true,  'constants', 'config_template' );
    $this->myConfigFilename         = $this->getSetting( $settings, true,  'constants', 'config' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get all columns with data from table in MySQL into @a myColumns.
   */
  private function getColumns()
  {
    $query = "
select table_name
,      column_name
,      data_type
,      character_maximum_length
,      numeric_precision
from   information_schema.COLUMNS
where  table_schema = database()
and    table_name  rlike '^[a-zA-Z0-9_]*$'
and    column_name rlike '^[a-zA-Z0-9_]*$'
order by table_name
,        ordinal_position";

    $rows = \SET_DL::executeRows( $query );
    foreach( $rows as $row )
    {
      $row['length'] = $this->deriveFieldLength( $row );
      $this->myColumns[$row['table_name']][$row['column_name']] = $row;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get the length of the field, depending on its type.
   */
  private function deriveFieldLength( $theColumn )
  {
    $ret = null;
    switch ($theColumn['data_type'])
    {
    case 'tinyint':
    case 'smallint':
    case 'mediumint':
    case 'int':
    case 'bigint':

    case 'decimal':
    case 'float':
    case 'double':
      $ret = $theColumn['numeric_precision'];
      break;

    case 'char':
    case 'varchar':
    case 'binary':
    case 'varbinary':

    case 'tinytext':
    case 'text':
    case 'mediumtext':
    case 'longtext':
    case 'tinyblob':
    case 'blob':
    case 'mediumblob':
    case 'longblob':
    case 'bit':
      $ret = $theColumn['character_maximum_length'];
      break;

    case 'timestamp':
      $ret = 16;
      break;

    case 'year':
      $ret = 4;
      break;

    case 'time':
      $ret = 8;
      break;

    case 'date':
      $ret = 10;
      break;

    case 'datetime':
      $ret = 16;
      break;

    case 'enum':
    case 'set':
      // Nothing to do. We don't assign a width to column of enum type.
      break;

    default:
      set_assert_failed( "Unknown type '%s'.", $theColumn['data_type'] );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Record constants and their values to the file @a myConstsFilename.
   */
  private function writeColumns()
  {
    $temp_filename = $this->myConstsFilename.'.tmp';
    $handle = fopen( $temp_filename, 'w' );
    if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $this->myConstsFilename );

    foreach( $this->myColumns as $table )
    {
      $width1 = 0;
      $width2 = 0;
      foreach( $table as $column )
      {
        $width1 = max( strlen( $column['column_name'] ), $width1 );
        $width2 = max( strlen( $column['length'] ),      $width2 );
      }

      foreach( $table as $column )
      {
        if (isset($column['length']))
        {
          if (isset($column['constant_name']))
          {
            $line_format = sprintf( "%%s.%%-%ds %%%dd %%s\n", $width1, $width2 );
            $n = fprintf( $handle, $line_format, $column['table_name'],
                                                 $column['column_name'],
                                                 $column['length'],
                                                 $column['constant_name'] );
            if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myConstsFilename );
          }
          else
          {
            $line_format = sprintf( "%%s.%%-%ds %%%dd\n", $width1, $width2 );
            $n = fprintf( $handle, $line_format, $column['table_name'], $column['column_name'], $column['length'] );
            if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myConstsFilename );
          }
        }
      }

      $n = fprintf( $handle, "\n" );
      if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myConstsFilename );
    }

     $err = fclose( $handle );
     if ($err===false) set_assert_failed( "Error closing file '%s'.", $this->myConstsFilename );

     $err = rename( $this->myConstsFilename.'.tmp', $this->myConstsFilename );
     if ($err===false) set_assert_failed( "Error: can't rename file '%s' to '%s'.", $temp_filename,
                                                                                    $this->myConstsFilename );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get old column with consatant from file @a myConstsFilename
   */
  private function getOldColumns()
  {
    if (file_exists( $this->myConstsFilename ))
    {
      $handle = fopen( $this->myConstsFilename, 'r' );
      if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $this->myConstsFilename );

      $line_number = 0;
      while ($line = fgets( $handle ))
      {
        $line_number++;
        if ($line!="\n")
        {
          $n = preg_match( "/^\s*([a-zA-Z0-9_]+).([a-zA-Z0-9_]+)\s+(\d+)\s*(\*|[a-zA-Z0-9_]+)?\s*$/", $line, $matches );
          if ($n===false) set_assert_failed( 'Internal error.' );

          if ($n==0)
          {
            set_assert_failed( "Illegal format at line %d in file '%s'.", $line_number, $this->myConstsFilename );
          }

          if (isset($matches[4]))
          {
            $table_name    = $matches[1];
            $column_name   = $matches[2];
            $length        = $matches[3];
            $constant_name = $matches[4];

            $this->myOldColumns[$table_name][$column_name] = array( 'table_name'    => $table_name,
                                                                    'column_name'   => $column_name,
                                                                    'length'        => $length,
                                                                    'constant_name' => $constant_name );
          }
        }
      }
      if (!feof($handle)) set_assert_failed( "Error reading from file '%s'.", $this->myConstsFilename );

      $ok = fclose( $handle );
      if ($ok===false) set_assert_failed( "Error closing file '%s'.", $this->myConstsFilename );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Definition a unknown constants.
   */
  private function enhanceColumns()
  {
    foreach( $this->myOldColumns as $table )
    {
      foreach( $table as $column )
      {
        $table_name  = $column['table_name'];
        $column_name = $column['column_name'];

        if ($column['constant_name']=='*')
        {
          $constant_name = strtoupper( $this->myPrefix.$column['column_name'] );
          $this->myOldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
        else
        {
          $constant_name = strtoupper( $this->myOldColumns[$table_name][$column_name]['constant_name']);
          $this->myOldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Merges the columns with the old and new constants.
   */
  private function mergeColumns()
  {
    foreach( $this->myOldColumns as $table_name => $table )
    {
      foreach( $table as $column_name => $column )
      {
        if (isset( $this->myColumns[$table_name][$column_name] ))
        {
          $this->myColumns[$table_name][$column_name]['constant_name'] = $column['constant_name'];
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get all lable from the MySQL.
   */
  private function getLabels()
  {
    $query_string = "
select t1.table_name  `table_name`
,      t1.column_name `id`
,      t2.column_name `label`
from       information_schema.columns t1
inner join information_schema.columns t2 on t1.table_name = t2.table_name
where t1.table_schema = database()
and   t1.extra        = 'auto_increment'
and   t2.table_schema = database()
and   t2.column_name like '%%\_label'";

    $tables = \SET_DL::executeRows( $query_string );
    foreach( $tables as $table )
    {
      $query_string = "
select `%s`  as `id`
,      `%s`  as `label`
from   `%s`
where   nullif(`%s`,'') is not null";

      $query_string = sprintf( $query_string, $table['id'], $table['label'], $table['table_name'], $table['label'] );
      $rows = \SET_DL::executeRows( $query_string );
      foreach ( $rows as $row )
      {
        $this->myLabels[$row['label']] = $row['id'];
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get all known constants into the array myConstants.
   */
  private function fillConstants()
  {
    foreach( $this->myColumns as $table_name => $table )
    {
      foreach( $table as $column_name => $column )
      {
        if (isset($this->myColumns[$table_name][$column_name]['constant_name']))
        {
          $this->myConstants[$column['constant_name']] = $column['length'];
        }
      }
    }

    foreach( $this->myLabels as $label => $id )
    {
      $this->myConstants[$label] = $id;
    }
    $ok = ksort( $this->myConstants );
    if ($ok===false) set_assert_failed( 'Internal error.' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Automatic generation the constants.
   */
  private function writeTargetConfigFile()
  {
    $source = file_get_contents( $this->myTemplateConfigFilename );
    if ($source===false) set_assert_failed( "Unable to open file '%s'.", $this->myTemplateConfigFilename );

    $width1 = 0;
    $width2 = 0;
    $constants = '';
    foreach( $this->myConstants as $constant => $value )
    {
      $width1 = max( strlen( $constant ), $width1 );
      $width2 = max( strlen( $value ),    $width2 );
    }

    $line_format = sprintf( "const %%-%ds = %%%dd; \n", $width1, $width2 );
    foreach( $this->myConstants as $constant => $value )
    {
      $constants .= sprintf( $line_format, $constant, $value );
    }

    $source = str_replace( '/* AUTO_GENERATED_CONSTS */', $constants, $source );

    $ok = file_put_contents( $this->myConfigFilename,  $source );
    if ($ok===false) set_assert_failed( "Unable to write to file '%s'.", $this->myTemplateConfigFilename );
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function run( $theConfigFilename )
  {
    $this->readConfigFile( $theConfigFilename );

    \SET_DL::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $this->getOldColumns();

    $this->getColumns();

    $this->enhanceColumns();

    $this->mergeColumns();

    $this->writeColumns();

    $this->getLabels();

    $this->fillConstants();

    $this->writeTargetConfigFile();

    \SET_DL::disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
}
