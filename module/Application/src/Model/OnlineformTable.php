<?php


namespace Application\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Expression;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

/**
 * Description of OnlineformTable
 *
 * @author Erkin
 */
class OnlineformTable {
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

   
    function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
  $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
    

   
    public function fetchAllWithFilter($search, $orderby, $ordertype, $paginated = false) {

        //to get data from database table for pagination
        if ($paginated) {
            // create a new Select object for the table forms
            $sqlSelect = new Select('forms');
            $sqlSelect->columns(array("id", "title", "created_date", "status","hidden_url"));

            //for search parameter
            if ($search <> '') {
                $sqlSelect->where->like('forms.title', "%$search%");
            }

            $sqlSelect->join('respondents', 'respondents.form_id = forms.id', array('respond_count' => new \Laminas\Db\Sql\Expression('COUNT(respondents.id)')), 'left');

            //for ordering
            if ($orderby) {
                $sqlSelect->order("$orderby $ordertype");
            } else {
                $sqlSelect->order(array("title", "id"));
            }

            $sqlSelect->group('forms.id');
         
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Onlineform());
            $paginatorAdapter = new DbSelect(
                    $sqlSelect, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
           
            return $paginator;
        }
    }


   
    public function getFormElement($fieldname, $formid) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('forms');
        $select->order('id DESC');


        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

   
    public function updateFormElement($field_name, $form_id, $field_title, $order, $field_type) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->update();
        $select->table('form_elements');
        $select->set(['title' => $field_name, 'element_order' => $order, 'type' => $field_type]);
        $select->where(['element_name' => $field_name, 'form_id' => $form_id]);

        $selectString = $sql->prepareStatementForSqlObject($select);
        $results = $selectString->execute();
        return $results;
    }

   
    public function getFormElementsByForm($formid) {
        $adapter = $this->tableGateway->getAdapter();

        $id = (int) $formid;

        $qi = function($name) use ($adapter) {
            return $adapter->platform->quoteIdentifier($name);
        };
        $fp = function($name) use ($adapter) {
            return $adapter->driver->formatParameterName($name);
        };
        $sql = 'SELECT id,element_name FROM ' . $qi('form_elements') . ' WHERE ' . $qi('form_id') . ' = ' . $fp('form_id') . ' order by element_order';

        /** @var $statement Laminas\Db\Adapter\Driver\StatementInterface */
        $statement = $adapter->query($sql);
        $results = $statement->execute(['form_id' => $id]);
     
        return $results;
    }

   
    public function getForm($id) {
        $id = (int) $id;

        $rowset = $this->tableGateway->select(['id' => $id]);

        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                    'Could not find row with identifier %d', $id
            ));
        }

        return $row;
    }


   
    public function showFormActive($id,$hiddenurl) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id,'hidden_url'=>$hiddenurl, 'status' => 1]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                    'Could not find row with identifier %d', $id
            ));
        }

        return $row;
    }

   
    public function updateForm($id,$title, $form_html, $form_xml,$form_public) {
        $public= ($form_public?0:1);
        $data = [
            'title' => $title,
            'form_html' => $form_html,
            'form_xml' => $form_xml,
            'created_date' => date('Y-m-d G:i:s'),
            'source_ip' => $this->getRealIpAddr(),
            'public'=>$public
        ];
        try{
            $sonuc = $this->tableGateway->update($data, ['id' => $id]);
            return $sonuc;
        } catch (\Exception $ex) {
            return $ex;
        }
    }


   
    public function saveForm($title, $form_html, $form_xml,$form_public) {
        $public= (($form_public=='true') ? 1 : 0);
        $data = [
            'title' => $title,
            'form_html' => $form_html,
            'form_xml' => $form_xml,
            'created_date' => date('Y-m-d G:i:s'),
            'source_ip' =>$this->getRealIpAddr(),
            'public'=>$public
        ];
            $sonuc=$this->tableGateway->insert($data);
        if($sonuc){
        $id=$this->tableGateway->lastInsertValue;
        $randomurlpart1 = rand(100, 999);
        $randomurlpart2 = rand(10, 99);
        $hidden_url = $randomurlpart1 .  $id .$randomurlpart2. substr($title, 0, 1);
        $this->updateFormUrl($id, $hidden_url);
        }
        
        return $id;

    }

    public function updateFormUrl($id,$hidden_url) {
        $data = [
            'hidden_url' => $hidden_url
        ];
        $sonuc = $this->tableGateway->update($data, ['id' => $id]);
        return $sonuc;
    }

   
    public function addFormElements($form_id, $title, $order, $element_name, $type) {
        $adapter = $this->tableGateway->getAdapter();
        $qi = function($name) use ($adapter) {
            return $adapter->platform->quoteIdentifier($name);
        };
        $fp = function($name) use ($adapter) {
            return $adapter->driver->formatParameterName($name);
        };
        $sql = 'INSERT INTO ' . $qi('form_elements') . ' (' . $qi('form_id') . ',' . $qi('title') . ',' . $qi('element_order') . ',' . $qi('element_name') . ',' . $qi('type') . ') 
                    VALUES (' . $fp('form_id') . ',' . $fp('title') . ',' . $fp('element_order') . ',' . $fp('element_name') . ',' . $fp('type') . ')';


        $statement = $adapter->query($sql);
        $results = $statement->execute(array('form_id' => $form_id, 'title' => $title, 'element_order' => $order, 'element_name' => $element_name, 'type' => $type));

        return;
    }

   
    public function deleteForm($id) {
        $adapter = $this->tableGateway->getAdapter();
        $id = (int) $id;
       $sonuc = $this->tableGateway->delete(['id' => $id]);
        if ($sonuc == 0) {
            return 'error';
        }
        $qi = function($name) use ($adapter) {
            return $adapter->platform->quoteIdentifier($name);
        };
        $fp = function($name) use ($adapter) {
            return $adapter->driver->formatParameterName($name);
        };
        $sql = 'DELETE FROM ' . $qi('answers') . ' WHERE ' . $qi('element_id') . ' in (SELECT ' . $qi('id') . ' FROM ' . $qi('form_elements') . ' WHERE ' . $qi('form_id') . ' = ' . $fp('form_id') . ')';

        $statement = $adapter->query($sql);
        $statement->execute(['form_id' => $id]);
        $sql = 'DELETE FROM ' . $qi('form_elements') . ' WHERE ' . $qi('form_id') . ' = ' . $fp('form_id');

        $statement = $adapter->query($sql);
        $statement->execute(['form_id' => $id]);
        $sql = 'DELETE FROM ' . $qi('respondents') . ' WHERE ' . $qi('form_id') . ' = ' . $fp('form_id');

        $statement = $adapter->query($sql);
        $results = $statement->execute(['form_id' => $id]);
        return $results;
    }
   
    public function addRespondent($formid) {
        $adapter = $this->tableGateway->getAdapter();

        $qi = function($name) use ($adapter) {
            return $adapter->platform->quoteIdentifier($name);
        };
        $fp = function($name) use ($adapter) {
            return $adapter->driver->formatParameterName($name);
        };
        $ip=$this->getRealIpAddr();
        $sql = 'INSERT INTO ' . $qi('respondents') . ' (' . $qi('form_id') . ',' . $qi('ip') . ',' . $qi('answer_date') . ') 
                    VALUES (' . $fp('form_id') . ',' . $fp('ip') . ',' . $fp('answer_date') . ')';

        $statement = $adapter->query($sql);
        $results = $statement->execute(array('form_id' => $formid, 'ip' => $ip, 'answer_date' => date('Y-m-d G:i:s')));

        return $results->getGeneratedValue();
    }
   
    public function form_respondents_count($formid) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(['id']);
        $select->from('answers');
        $select->join('form_elements',
                new Expression('answers.element_id = form_elements.id and form_elements.form_id= ' . $formid), ['element_title'=>'title'],     // (optional) list of columns, same requirements as columns() above
                $select::JOIN_INNER 
        );
        $select->where->equalTo('form_elements.form_id', $formid);
        $select->group('answers.respondents_id');
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return $results->getResource()->fetchAll();
    }

   
    public function getAnswers($formid) {
        $adapter = $this->tableGateway->getAdapter();
		$setsqlmod="SET sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
		 $statement = $adapter->query($setsqlmod);
       $statement->execute();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(['value']);
        $select->from('answers');
        $select->join('form_elements', new Expression('form_elements.id = answers.element_id and form_elements.form_id = ' . $formid), array('element_title' => 'title'), 'inner');
        $select->join('forms', 'forms.id = form_elements.form_id ', array('form_title' => 'title'), 'left');
      $select->join('respondents', new Expression('answers.respondents_id=respondents.id and respondents.form_id = ' . $formid), array('answer_date' => 'answer_date','ipadres'=>'ip'), 'left');
        $select->order('answers.id, form_elements.element_order');
         $select->where->equalTo('form_elements.form_id', $formid);
        $select->group('answers.id');
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
         return $results->getResource()->fetchAll();
    }

   
    public function getElementCountForAnswers($formid) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(['id']);
        $select->from('answers');
        $select->join('form_elements', new Expression('form_elements.id = answers.element_id and form_elements.form_id = ' . $formid), array('element_title' => 'title'), 'right');
        $select->join('forms', 'forms.id = form_elements.form_id ', array('form_title' => 'title'), 'left');
        $select->where->equalTo('form_elements.form_id', $formid);
        $select->order('form_elements.element_order');
        $select->group('answers.element_id');
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return $results->getResource()->fetchAll();
    }

   
    public function addAnswer($nesneid, $value, $respondents_id) {
        $adapter = $this->tableGateway->getAdapter();
        $qi = function($name) use ($adapter) {
            return $adapter->platform->quoteIdentifier($name);
        };
        $fp = function($name) use ($adapter) {
            return $adapter->driver->formatParameterName($name);
        };
        $sql = 'INSERT INTO ' . $qi('answers') . ' (' . $qi('element_id') . ',' . $qi('value') . ',' . $qi('respondents_id') . ') 
                    VALUES (' . $fp('element_id') . ',' . $fp('value') . ',' . $fp('respondents_id') . ')';

        $statement = $adapter->query($sql);
        $statement->execute(array('element_id' => $nesneid, 'value' => $value, 'respondents_id' => $respondents_id));
        return;
    }
    
    public function updateStatus($id, $durum) {
        if ($durum) {
            $data = [
                'status' => 1
            ];
        } else {
            $data = [
                'status' => 0
            ];
        }
        $sonuc = $this->tableGateway->update($data, ['id' => $id]);

        return $sonuc;
    }
}
