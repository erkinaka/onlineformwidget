<?php
/**
 * Description
 *
 * @author Erkin
 */

namespace Application\Controller;

use Laminas\Filter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Dom\Query;
use PHPExcel;

class OnlineformController extends AbstractActionController
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
        $this->layout('layout/layout');
    }

    public function formsAction()
    {
        //getting submitted form values, if not assigning default values
        $orderby = filter_var($this->params()->fromRoute('orderby', 'id'), FILTER_SANITIZE_STRING);
        $ordertype = filter_var($this->params()->fromRoute('ordertype', 'ASC'), FILTER_SANITIZE_STRING);
        $activepage = (int)$this->params()->fromRoute('page', 1);
        $search = filter_var($this->params()->fromPost('search', ''), FILTER_SANITIZE_STRING);
        $countperpage = 20;
        $request = $this->getRequest();

        //if form submitted, assigned task will be operated
        if ($request->isPost()) {

            //task: save status
            if ($this->params()->fromPost('submitstatus')) {
                $rowcount = (int)$this->params()->fromPost('rowcount', 0);
                $rowcount = ($rowcount > $countperpage) ? 0 : $rowcount;
                $rownumber = (int)$this->params()->fromPost('rownumber', 1);
                $rowcount = $rownumber == 0 ? 0 : $rowcount;
                $rowcount = ($rownumber > $activepage * $countperpage) ? 0 : $rowcount;

                //The following code will be run for rowcount

                for ($i = $rownumber; $i < $rownumber + $rowcount; $i++) {
                    //form_id=(int)$this->params()->fromPost('row' . $i)
                    //status value of row=$this->params()->fromPost('row_status' . (int)$this->params()->fromPost('row' . $i))
                    $this->table->updateStatus((int)$this->params()->fromPost('row' . $i), $this->params()->fromPost('row_status' . (int)$this->params()->fromPost('row' . $i)));
                }
            }

            //task: delete-selected
            if ($this->params()->fromPost('delete-selected')) {
                $rowcount = (int)$this->params()->fromPost('rowcount', 0);
                $rowcount = ($rowcount > $countperpage) ? 0 : $rowcount;
                $rownumber = (int)$this->params()->fromPost('rownumber', 1);
                $rowcount = $rownumber == 0 ? 0 : $rowcount;
                $rowcount = ($rownumber > $activepage * $countperpage) ? 0 : $rowcount;

                //The following code will be run for rowcount
                for ($i = $rownumber; $i < $rownumber + $rowcount; $i++) {
                    if ($this->params()->fromPost('delete' . (int)$this->params()->fromPost('row' . $i))) {

                        //form_id=(int)$this->params()->fromPost('row' . $i)
                        $this->table->deleteForm((int)$this->params()->fromPost('row' . $i));
                    }
                }
            }

            //task: delete one
            if ($this->params()->fromPost('delete-one')) {
                $silid = (int)$this->params()->fromPost('delete-one');
                $this->table->deleteForm($silid);
            }
        }

        $paginator = $this->table->fetchAllWithFilter($search, $orderby, $ordertype, true);
        // set the current page to what has been passed in query string, or to 1 if none set
        $paginator->setCurrentPageNumber($activepage);
        // set the number of items per page to 10
        $paginator->setItemCountPerPage($countperpage);

        return new ViewModel(array(
            'paginator' => $paginator,
            'search' => $search,
            'orderby' => $orderby,
            'ordertype' => $ordertype,
            'page' => $activepage,
        ));
    }

    public function addAction()
    {
        //action for formbuilder to add a form
        $this->layout('layout/layoutformbuilder');
        return new ViewModel();
    }

    public function editAction()
    {
        //action for formbuilder to edit a form

        //changing layout for formbuilder
        $this->layout('layout/layoutformbuilder');

        //to check if the form is available with sending id
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('forms');
        }

        try {
            $formtable = $this->table->getForm($id);
        } catch (\Exception $ex) {

            return $this->redirect()->toRoute('formlar');
        }

        $public = ($formtable->public == 0 ? "" : "checked");

        $formdata = str_replace(["\r", "\n", "\t"], "", $formtable->form_xml);

        return new ViewModel(array(
            'formdata' => $formdata,
            'title' => $formtable->title,
            'public' => $public,
            'form_id' => $id,
        ));
    }

    public function reportAction()
    {
        //to check if the form is available with sending id
        $id = (int)$this->params()->fromRoute('id', 0);



        //if has respondents then it will create an excel sheet
        $get_answers = $this->table->getAnswers($id);
        $answer_count = count($get_answers);
        if ($answer_count > 0) {

            //changing layout for creating sheet witout html tags.
            $this->layout('layout/empty');

            $element_count = count($this->table->getElementCountForAnswers($id));
            $get_respondents_with_answers = $this->table->form_respondents_count($id);
            $count_respondents_with_answers = count($get_respondents_with_answers);

            date_default_timezone_set('Europe/London');
            define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("Erkin AKA")
                ->setLastModifiedBy("Erkin AKA")
                ->setTitle("Online Form Widget")
                ->setSubject($get_answers[0][1])
                ->setDescription("ekokod.com - github.com/erkinaka :: @Erkin AKA")
                ->setKeywords("online form widget")
                ->setCategory("Business Education");
            $objPHPExcel->getActiveSheet()->setCellValue('A1', $get_answers[0][2]);
            $row = 3;
            $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Sıra No');
            $col = 1;
            for ($i = 0; $i < $element_count; $i++) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col + $i, 3, $get_answers[$i][1]);
            }
            $col = $col + $i;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 3, 'Gönderilme Tarihi');
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 3, 'IP Adres');
            $row++;
            $satirsay = 0;
            $i = 0;
            $j = 0;
            for ($i = 0; $i < $count_respondents_with_answers; $i++) {
                $col = 0;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $i + 1);
                $col++;
                for ($j = 0; $j < $element_count; $j++) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $get_answers[$satirsay][0]);
                    //echo $getiryanit[$satirsay][0] . "\t";
                    $satirsay++;
                    $col++;
                }
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $get_answers[$satirsay - 1][3]);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $get_answers[$satirsay - 1][4]);
                $row++;
            }
// Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle($get_answers[0][1]);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Onlinebilgi.xlsx"');
            header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        } else {
            $result = "No respond yet!";
            return new ViewModel(array('result' => $result));
        }

    }


    public function showformAction()
    {
        //show form action

        //to check if the form is available with sending id
        $id = (int)$this->params()->fromRoute('id');
        $hidden_url = $this->params()->fromRoute('hidden_url');
        if (!$id) {
            return $this->redirect()->toRoute('forms');
        }

        $filter = new Filter\StripTags();
        $hidden_url = $filter->filter($hidden_url);

        //action_flag will be used in action to check if the form is submitted or has any error
        $action_flag = 0;
        try {

            $formtable = $this->table->showFormActive($id, $hidden_url);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('forms');
        }

        $request = $this->getRequest();

        //checking if the user sends the answers with form.

        if ($request->isPost() && $action_flag == 0) {
            $filter = new Filter\StripTags();
            $form_elements = $this->table->getFormElementsByForm($id);
            $respondents_id = $this->table->addRespondent($id);

            foreach ($form_elements as $element) {

                //if element answer is not empty
                if (isset($_POST[($element['element_name'])])) {

                    //if element type is select and has multi answer
                    if (is_array($_POST[($element['element_name'])])) {
                        $i = 0;
                        $element_answers = "";
                        foreach ($_POST[($element['element_name'])] as $value) {
                            $element_answers = $element_answers . " -" . $filter->filter($value);
                        }
                        $this->table->addAnswer($element['id'], $element_answers, $respondents_id);
                    } //if element type is not multi select or checkbox and has only one answer
                    else {
                        $this->table->addAnswer($element['id'], $filter->filter($_POST[($element['element_name'])]), $respondents_id);
                    }
                } //if element answer is empty
                else {
                    $this->table->addAnswer($element['id'], "", $respondents_id);
                }
            }
            $action_flag = 2;
        }


        $formdata = $formtable->form_html;
        return new ViewModel(array(
            'form_data' => $formdata,
            'title' => $formtable->title,
            'form_id' => $id,
            'hidden_url' => $hidden_url,
            'action_flag' => $action_flag
        ));
    }

    public function formsaveAction()
    {
        //this action is an ajax action. this action will be called from the action called add.

        //layout is being changed because of ajax request
        $this->layout('layout/empty');

        $form_template = $this->params()->fromPost('form_template');
        $form_html = $this->params()->fromPost('form_html');
        $form_title = $this->params()->fromPost('form_title');
        $form_public = $this->params()->fromPost('public');
        $filter = new Filter\StripTags();
        $form_title = $filter->filter($form_title);
        $form_public = $filter->filter($form_public);
        $added_form_id = $this->table->saveForm($form_title, $form_html, $form_template, $form_public);
        $filter = new Filter\StripTags();
        $dom = new Query($form_template);
        try {
            $results = $dom->execute('form-template fields field');
            $order = 1;
            foreach ($results as $result) {
                if ($result->getAttribute('subtype')) {
                    $field_type = $filter->filter($result->getAttribute('subtype'));
                } else {
                    $field_type = $filter->filter($result->getAttribute('type'));
                }
                $field_name = $filter->filter($result->getAttribute('name'));
                $field_title = iconv("UTF-8", "ISO-8859-9", $filter->filter($result->getAttribute('label')));

                //if element type is a label, this element will not be added to element table.
                if ($field_type <> "p") {
                    $this->table->addFormElements($added_form_id, $field_title, $order, $field_name, $field_type);
                }
                $order++;
            }
        } catch (\Exception $ex) {
            echo $ex;
        }
    }

    public function formupdateAction()
    {
        //this action is an ajax action. this action will be called from the action called add.

        //layout is being changed because of ajax request
        $this->layout('layout/empty');
        $form_id = $this->params()->fromPost('form_id');
        $form_template = $this->params()->fromPost('form_template');
        $form_html = $this->params()->fromPost('form_html');
        $form_title = $this->params()->fromPost('form_title');
        $form_public = $this->params()->fromPost('public');
        $filter = new Filter\StripTags();
        $form_title = $filter->filter($form_title);
        $form_public = $filter->filter($form_public);

        $this->table->updateForm($form_id, $form_title, $form_html, $form_template, $form_public);
        $filter = new Filter\StripTags();
        $dom = new Query($form_template);
        try {
            $results = $dom->execute('form-template fields field');
            $order = 1;

            foreach ($results as $result) {
                if ($result->getAttribute('subtype')) {
                    $field_type = $filter->filter($result->getAttribute('subtype'));
                } else {
                    $field_type = $filter->filter($result->getAttribute('type'));
                }
                $field_name = $filter->filter($result->getAttribute('name'));
                $field_title = iconv("UTF-8", "ISO-8859-9", $filter->filter($result->getAttribute('label')));
                $kayitkontrol = $this->table->getFormElement($field_name, $form_id);

                //checking if new elements are added at update process
                if (count($kayitkontrol) <> 0) {
                    $this->table->updateFormElement($field_name, $form_id, $field_title, $order, $field_type);
                } else {
                    $this->table->addFormElements($form_id, $field_title, $order, $field_name, $field_type);
                }
                $order++;
            }
        } catch (\Exception $ex) {
            echo $ex;
        }
    }

}
