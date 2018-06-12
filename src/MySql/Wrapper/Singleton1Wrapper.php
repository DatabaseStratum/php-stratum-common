<?php

namespace SetBased\Stratum\MySql\Wrapper;

/**
 * Class for generating a wrapper method for a stored procedure that selects 1 row having a single column only.
 */
class Singleton1Wrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getDocBlockReturnType()
  {
    return $this->routine['return'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeResultHandler()
  {
    $routine_args = $this->getRoutineArgs();

    if ($this->routine['return']=='bool')
    {
      $this->codeStore->append('return !empty(self::executeSingleton1(\'CALL '.$this->routine['routine_name'].'('.$routine_args.')\'));');
    }
    else
    {
      $this->codeStore->append('return self::executeSingleton1(\'CALL '.$this->routine['routine_name'].'('.$routine_args.')\');');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobFetchData()
  {
    $this->codeStore->append('$row = [];');
    $this->codeStore->append('self::bindAssoc($stmt, $row);');
    $this->codeStore->append('');
    $this->codeStore->append('$tmp = [];');
    $this->codeStore->append('while (($b = $stmt->fetch()))');
    $this->codeStore->append('{');
    $this->codeStore->append('$new = [];');
    $this->codeStore->append('foreach($row as $value)');
    $this->codeStore->append('{');
    $this->codeStore->append('$new[] = $value;');
    $this->codeStore->append('}');
    $this->codeStore->append('$tmp[] = $new;');
    $this->codeStore->append('}');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->imports[] = 'SetBased\Stratum\Exception\ResultException';

    $this->codeStore->append('if ($b===false) self::mySqlError(\'mysqli_stmt::fetch\');');
    $this->codeStore->append('if (count($tmp)!=1) throw new ResultException(\'1\', count($tmp), $query);');
    $this->codeStore->append('');

    if ($this->routine['return']=='bool')
    {
      $this->codeStore->append('return !empty($tmp[0][0]);');
    }
    else
    {
      $this->codeStore->append('return $tmp[0][0];');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
