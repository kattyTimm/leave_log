<?php
  include_once 'assist.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . '/orgcomm/php/_dbcomm.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . '/orgcomm/php/_jgcomm.php';
   
  $part = strval($_POST['part']);

  $data = [];
  $data['success'] = false;
  
  $ass = new assist();  
  $db = new _dbcomm();

  $result = [];
  
  if($part == 'checkIp'){
      $offset = intval($_POST['offset']);
      $rows = intval($_POST['rows']);
      $currpage = intval($_POST['currpage']);
      $only_unChecked = intval($_POST['iparam']);
      
      $result = $ass->checkIp($offset, $rows, $currpage, $only_unChecked);  
      $data['html'] = $result['html'];
      
      $data['success'] = true;      
      $data['header'] = $result['header'];
      $data['add_btn'] = $result['add_btn'];
      $data['ffio'] = $result['ffio'];
      $data['rid'] = $result['rid'];
      $data['pagination'] = $result['pagination'];
      $data['checkbox'] = $result['checkbox'];
      $data['ip'] = $result['ip'];
      
      $data['title'] = $result['title'];
      $data['docs_btn'] = $result['docs_btn'];      
   //   $data['docs'] = $result['docs'];
      $data['in_arr'] = $result['in_arr'];
      $data['folders'] = $result['folders'];
   }
   
      
   else if($part == 'get_fldrs'){
      $user_rid = strval($_POST['user_rid']);
      
      $result = $ass->get_folders($user_rid);
      
      $data['success'] = true;
      
      if(count($result) > 0)
         $data['folders'] = $result['folders'];
   }
   
   else if($part == 'get_docs'){
      $user_rid = strval($_POST['user_rid']);
      $holder = strval($_POST['holder']);
      
      $result = $ass->get_docs_section($user_rid, $holder);
      $innerDocs = true;
      
      $data['success'] = true;
      $data['docs'] = $result;
      $data['arrow'] = $ass->getArrow();
   }
   
   else if ($part == 'get_fm'){
       $sparam = array_key_exists('sparam', $_POST) ? trim(strval($_POST['sparam'])) : "";
       $fm_nm = strval($_POST['fm_id']);
       $emp_rid = '';
       
       if($fm_nm == 'employee_depart_deteils')
           $emp_rid = strval($_POST['emp_rid']);
       
       $result = $ass->get_fm($fm_nm, $emp_rid, $sparam);
       
       if(strlen($result) > 0){
           $data['success'] = true;  
           $data['html'] = $result;
       }
   }
   
// для загрузки ОДНОГО документа, без папок   
    else if($part == 'docs_add_rec'){
        $tbl = trim(strval($_POST['tbl']));
        $pid = trim(strval($_POST['pid']));
        $fnm = _dbbase::shrink_filename(trim(strval($_POST['fnm'])), 50);
        $nm  = mb_substr(trim(strval($_POST['nm'])), 0, 50);
        $flg = intval($_POST['doc_flg']);
        $rdat = strval($_POST['rdat']);

        $result = $db->docs_addFile($tbl, $pid, $fnm, $nm, $flg, $rdat);
        
        if (strlen($result) > 0) {
            $data['pid'] = $pid;
            $data['docs_rid'] = $result;
            $data['success'] = true;
        }            
    }
    
    
    else if($part == 'delete_doc'){
        $rid = strval($_POST['rid']);

        $result = $db->delete_doc_by_self_rid($rid);

        if($result) $data['success'] = true;
    }
    
    else if($part == 'file_put_tmp'){
        $rid = trim(strval($_POST['val']));

        $result = $db->get_document($rid);

        if (count($result) > 0) {
            $fname = _assbase::dataUri2tmpFile($_SERVER['DOCUMENT_ROOT'] . assist::siteRootDir() . '/tmp', $result['fnm'], $result['rdat']);

            if (mb_strlen($fname) > 0) {
                $data['frelname'] = assist::siteRootDir() . '/tmp/' . $result['fnm'];
                $data['success'] = true;
            }
        }
    }
    
    else if ($part == 'remove_tmp_file') {
    $jg = new _jgcomm();
    $jg->jg_remove_tmp_file($form_data);   // $form_data pass by reference
    unset($jg);
    
    /* RESULT
        --SUCCESS:
        $form_data['success'] = true;
        --ERROR:
        $form_data['success'] = false;
    */
}
   
   else if($part == 'add_journal_record'){
        $emp_rid = strval($_POST['user_rid']);
     //   $flg = intval($_POST['flg']);
        $date = strval($_POST['date']);
        $org_nm = strval($_POST['org_nm']);
        $leave_tm = strval($_POST['depart']);
        $back = strval($_POST['back']);
        $rem = strval($_POST['rem']);
 
        $result = $db->journal_depart_add_record($emp_rid, $date, $org_nm, $leave_tm, $back, $rem);
        
        if(strlen($result) > 0){
            $data['success'] = true;    
            $data['rid'] = $result;
        }
   }
   else if($part == 'edit_back_tm'){
       $rid = strval($_POST['rid']);
       $back = strval($_POST['back']);
       
       $result = $db->edit_backTm_in_journal_depart($rid, $back);
        
       if(strlen($result) > 0){
          $data['success'] = true;    
          $data['rid'] = $result;
       }
   }
   
    else if($part == 'edit_start_tm'){
       $rid = strval($_POST['rid']);
       $start = strval($_POST['start']);
       
       $result = $db->edit_startTm_in_journal_depart($rid, $start);
        
       if(strlen($result) > 0){
          $data['success'] = true;    
          $data['rid'] = $result;
       }
   }
   
    else if($part == 'edit_date'){
       $rid = strval($_POST['rid']);
       $date = strval($_POST['date']);    
       
       $result = $db->edit_date_journal_depart($rid, $date);
        
       if(strlen($result) > 0){
          $data['success'] = true;    
          $data['rid'] = $result;
       }
   }

   else if ($part == 'set_label'){
       $rid = strval($_POST['rid']);
       $flg = intval($_POST['flg']);
       $back = strval($_POST['back']);
       
       $result = $db->set_label_in_journal_depart($rid, $flg, $back);
        
       if(strlen($result) > 0){ 
          $data['success'] = true;    
          $data['rid'] = $result;
       }
   }
  
   
   else if ($part == 'search_get_depart_row') {    

        $rid = trim(strval($_POST['rid']));

        $emp_rid = trim(strval($_POST['emp_rid']));
        $emp_record = $db->emp_getListRec($emp_rid);
        $emp_position_records = $db->pos_record_by_self_rid($emp_record['pos']);
        $emp_depart_records = $db->dep_record_by_self_rid($emp_record['dep']);   
        
        $emp_position = $emp_position_records['ttl'];
        $emp_depart = $emp_depart_records['abb'];
        
        $in_arr = [];
        $i = 0;
        
        if(preg_match('#ОК#i', $emp_depart) == 1 || (preg_match('#иректор#i', $emp_position) == 1 AND preg_match('#адм#i', $emp_depart) == 1)){  
            $i = 1;
            
            $result = $db->table_getRidRowNumber_depart('depart', $rid, [], ''); 
        } 
        else if(preg_match('#КТБ#i', $emp_depart) == 1 ||  preg_match('#БТПК#i', $emp_depart) == 1){
            $i = 2;
            
            if(preg_match('#БТПК#i', $emp_depart) == 1) $in_arr = ['ТОП', 'ТПИ', 'РАНТД']; 
            else 
                $in_arr = ['КиТРМПС', 'ТМД', 'ТПВЛТ', 'КРПВЛТ']; 

           $result = $db->table_getRidRowNumber_depart('depart', $rid, $in_arr, '');

        }
        else if(preg_match('#лавный#i', $emp_position) == 1 || preg_match('#ачальн#i', $emp_position) == 1){
            $i = 3;

            if(preg_match('#лавный#i', $emp_position) == 1){
                $in_arr = ['АХО', 'РАСУПК', 'ТО', 'ОМС'];
            }
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ОЭ#i',$emp_depart) == 1){
               $in_arr = ['ОЭ']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#РАСУПК#i', $emp_depart) == 1){
               $in_arr = ['РАСУПК']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ОМС#i', $emp_depart) == 1){
               $in_arr = ['ОМС']; 
            }  
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТО#i', $emp_depart) == 1){
               $in_arr = ['ТО']; 
            }  
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#КиТРМПС#i', $emp_depart) == 1){
               $in_arr = ['КиТРМПС']; 
            }
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТМД#i', $emp_depart) == 1){
               $in_arr = ['ТМД']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТПВЛТ#i', $emp_depart) == 1){
               $in_arr = ['ТПВЛТ']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#КРПВЛТ#i', $emp_depart) == 1){
               $in_arr = ['КРПВЛТ']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТПИ#i', $emp_depart) == 1){
               $in_arr = ['ТПИ']; 
            } 

            $result = $db->table_getRidRowNumber_depart('depart', $rid, $in_arr, '');
        }
        else{
            $i = 4;
            $result = $db->table_getRidRowNumber_depart('depart', $rid, [], $emp_rid);
        }                

        $data['rownum'] = $result;
        $data['i'] = $i;
        $data['success'] = true;
}

else if ($part == 'get_fm_loader') {

    $result = $ass->get_fm_loader(); //$orto, $orfr, $dt
    
    $data['html'] = $result;
    if (strlen($result) > 0)
        $data['success'] = true;
}



else if ($part == 'add_document') {
    
    $pid = strval($_POST['pid']);
    $flg = intval($_POST['flg']);   
    $holder = strval($_POST['holder']);
    $fnm = _dbbase::shrink_filename(trim(strval($_POST['fnm'])), 50);
    $nm  = mb_substr(trim(strval($_POST['nm'])), 0, 50);
    $rdat = strval($_POST['rdat']);
    
    $result = $db->docs_load_file_to_folder($pid, $fnm, $nm, $flg, $rdat, $holder);
        
        if (strlen($result) > 0) {
            $data['pid'] = $pid;
            $data['docs_rid'] = $result;
            $data['success'] = true;
        }          
}


//$ass = new assist();
//$result = $ass->get_docs_section('832a407d-3e5b-41a1-9228-2a43a4cacde5');
// var_dump($result);
//echo '<hr>';

 unset($ass); 
 unset($db); 
/*
 $db = new _dbcomm();
        $emp_rid = '832a407d-3e5b-41a1-9228-2a43a4cacde5';
        $rid = '78fb2388-44df-4c94-b4bb-f38c5cef4076';
        $result = 0;
        $i = 0;
        $emp_record = $db->emp_getListRec($emp_rid);

        $emp_position_arr = $db->pos_record_by_self_rid($emp_record['pos']);
        $emp_depart_arr = $db->dep_record_by_self_rid($emp_record['dep']);   
        
        $emp_depart = $emp_depart_arr['abb'];
        $emp_position = $emp_position_arr['ttl'];
        
        var_dump($emp_depart);
        echo '<hr>';
        var_dump($emp_position);
        

        $in_arr = [];
        
        if(preg_match('#ОК#i', $emp_depart) == 1 || (preg_match('#ирект#i', $emp_position) == 1 AND preg_match('#адм#i', $emp_depart) == 1)){  
            
            $result = $db->table_getRidRowNumber_depart('depart', $rid, [], ''); 
            $i = 1;
        } 
        else if(preg_match('#КТБ#i', $emp_depart) == 1 ||  preg_match('#БТПК#i', $emp_depart) == 1){
            
            $i = 2;
            
            if(preg_match('#БТПК#i', $emp_depart) == 1) $in_arr = ['ТОП', 'ТПИ', 'РАНТД']; 
            else 
                $in_arr = ['КиТРМПС', 'ТМД', 'ТПВЛТ', 'КРПВЛТ']; 

           $result = $db->table_getRidRowNumber_depart('depart', $rid, $in_arr, '');

        }
        else if(preg_match('#лавный#i', $emp_position) == 1 || preg_match('#ачальн#i', $emp_position) == 1){
            
            $i = 3;

            if(preg_match('#лавный#i', $emp_position) == 1){
                $in_arr = ['АХО', 'РАСУПК', 'ТО', 'ОМС'];
            }
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ОЭ#i',$emp_depart) == 1){
               $in_arr = ['ОЭ']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#РАСУПК#i', $emp_depart) == 1){
               $in_arr = ['РАСУПК']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ОМС#i', $emp_depart) == 1){
               $in_arr = ['ОМС']; 
            }  
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТО#i', $emp_depart) == 1){
               $in_arr = ['ТО']; 
            }  
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#КиТРМПС#i', $emp_depart) == 1){
               $in_arr = ['КиТРМПС']; 
            }
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТМД#i', $emp_depart) == 1){
               $in_arr = ['ТМД']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТПВЛТ#i', $emp_depart) == 1){
               $in_arr = ['ТПВЛТ']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#КРПВЛТ#i', $emp_depart) == 1){
               $in_arr = ['КРПВЛТ']; 
            } 
            else if(preg_match('#ачальн#i', $emp_position) == 1 AND preg_match('#ТПИ#i', $emp_depart) == 1){
               $in_arr = ['ТПИ']; 
            } 

            $result = $db->table_getRidRowNumber_depart('depart', $rid, $in_arr, '');
        }
        else{
            $i = 4;
            $result = $db->table_getRidRowNumber_depart('depart', $rid, [], $emp_rid);
        }                

        
         echo '<hr>';
        
          var_dump($in_arr, $emp_rid);
           echo '<hr>';
          var_dump($result, $i);
          echo '<hr>';
          var_dump(preg_match('#КТБ#i', $emp_depart) == 1);
      */  
        
 echo json_encode($data);
?>