<?php  
include_once $_SERVER['DOCUMENT_ROOT'].'/orgcomm/php/_dbcomm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_dbbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';

class assist{
    public function __construct() {
        if (strlen(trim(session_id())) == 0)
            session_start();
    }
    
    public static function siteRootDir() : string {    // site root must have index.php. directory will return with starting slash, ex: /IcmrM
        return _assbase::siteRootDir_($_SERVER['PHP_SELF']);
    }
    
    public function make_pagination(string $pg_id, int $offset, int $rows, int $currpage, int $totalrows) : string {
        $ass = new _assbase();
        $result = $ass->make_pagination($pg_id, $offset, $rows, $currpage, $totalrows);
        unset($ass);
        
        return $result;
    }
    
    public function get_fm($fm_nm, $emp_rid = '', $sparam = ''){
        $result = '';
        
      //  if(strlen(trim($fm_nm))> 0){
            $fm_path = 'form/'.$fm_nm.'.php';
            if(file_exists($fm_path)){
                $form = file_get_contents($fm_path);
                
                if($fm_nm == 'employee_depart_deteils'){
                    $db = new _dbcomm();
                    $totalCount_depart = $db->count_summary_depart_by_emp_rid($emp_rid);
                    $depart_records = $db->journal_depart_by_emp_rid($emp_rid);
                    unset($db);
                    
                    $diff = 0;
                    
                    foreach($depart_records as $row){
                        $leave = $row['leave_tm'];
                        $back = $row['back'];
                        
                        $leave_arr = explode(':', $leave);
                        $back_arr = explode(':', $back);
                        
                        $leave_var = mktime($leave_arr[0], $leave_arr[1]);
                        $back_var = mktime($back_arr[0], $back_arr[1]);
                        
                        $diff += $back_var - $leave_var;
                    }
                    
                    $result_str_diff = $this->timeForTable($diff);
                    
                    $form = str_replace("{total_leave_quantity}", $totalCount_depart, $form);
                    $form = str_replace("{total_absence_time}", $result_str_diff, $form);
                }
                
                if(is_string($form))
                   $result = $form;
            }else{
                $assbase = new _assbase();
                $result = $assbase->get_fm($fm_nm, $sparam);
                unset($ass);
            }
      //  }
        
        return $result;
    }
    
    public function get_rownum(){
        
    }
    
    public function checkIp($offset, $rows, $currpage, $only_unChecked){
            $db = new _dbcomm();
            //$db->emp_getListRecByIP - возвращает массив из emp по конкретному пользователю    '::2'  10.11.12.143  _dbcomm::get_currentClientIP()
            $exist_user_record = $db->emp_getListRecByIP(_dbcomm::get_currentClientIP()); //$_SERVER['REMOTE_ADDR'] - возвращает ip с которго просматривают страницу
          //  $folders_list = $db->get_folder_list();
            $result = [];
            $in_arr = [];
            $totalrows = 0;

            if(count($exist_user_record) > 0){
                $user_rid = $exist_user_record['rid'];  
                $position = $db->pos_record_by_self_rid($exist_user_record['pos']); //$position['ttl'].   
                $depart = $db->dep_record_by_self_rid($exist_user_record['dep']);
                
                $result['docs'] = '';
                
                $result['ip'] = "<span>ip: </span><span>" . _dbcomm::get_currentClientIP() . "</span>"; 

                $result['html'] = "<div class=\"k-row\" >" .
                                    "<div class=\"inner\" id='content_tmp' data-user-rid='".$user_rid."'>" .
                                          "{TABLE}" .           
                                    "</div>".
                             "</div>" ;

                if(preg_match('#ОК#i', $depart['abb']) == 1 || (preg_match('#иректор#i', $position['ttl']) == 1 AND preg_match('#адм#i', $depart['abb']) == 1)){                
                    $totalrows = $db->depart_getRowcount($only_unChecked);
                    
                } 
                else if(preg_match('#КТБ#i', $depart['abb']) == 1 ||  preg_match('#БТПК#i', $depart['abb']) == 1){
                    
                    if(preg_match('#БТПК#i', $depart['abb']) == 1) $in_arr = ['ТОП', 'ТПИ', 'РАНТД']; 
                    else 
                        $in_arr = ['КиТРМПС', 'ТМД', 'ТПВЛТ', 'КРПВЛТ']; 
                    
                    $totalrows = $db->depart_getRowcount($only_unChecked, $in_arr);
                    
                }
                else if(preg_match('#лавный#i', $position['ttl']) == 1 || preg_match('#ачальн#i', $position['ttl']) == 1){

                    if(preg_match('#лавный#i', $position['ttl']) == 1){
                        $in_arr = ['АХО', 'РАСУПК', 'ТО', 'ОМС'];
                    }
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ОЭ#i', $depart['abb']) == 1){
                       $in_arr = ['ОЭ']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#РАСУПК#i', $depart['abb']) == 1){
                       $in_arr = ['РАСУПК']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ОМС#i', $depart['abb']) == 1){
                       $in_arr = ['ОМС']; 
                    }  
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ТО#i', $depart['abb']) == 1){
                       $in_arr = ['ТО']; 
                    }  
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#КиТРМПС#i', $depart['abb']) == 1){
                       $in_arr = ['КиТРМПС']; 
                    }
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ТМД#i', $depart['abb']) == 1){
                       $in_arr = ['ТМД']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ТПВЛТ#i', $depart['abb']) == 1){
                       $in_arr = ['ТПВЛТ']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#КРПВЛТ#i', $depart['abb']) == 1){
                       $in_arr = ['КРПВЛТ']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ТПИ#i', $depart['abb']) == 1){
                       $in_arr = ['ТПИ']; 
                    } 
                    
                    $totalrows = $db->depart_getRowcount($only_unChecked, $in_arr);
                }
                
                else{
                    $totalrows = $db->depart_getRowcount($only_unChecked, [], $user_rid);
                }
        
                unset($db);
                
                $table = $this->get_table_of_journal($offset, $rows, $totalrows, $position['ttl'], $depart['abb'], $user_rid, $only_unChecked);

                $result['html'] = str_replace("{TABLE}", $table, $result['html']);
                
                
                $result['add_btn'] = '<img src="img/new_add_btn.png" data-toggle="tooltip" title="Добавить запись" data-date="'.date('Y.m.d H:i:s').'"'. 
                                     ' data-ffio="'.$exist_user_record['ffio'].'" data-user-rid="'.$exist_user_record['rid'].'" data-pos="'.$position['ttl'].'"'.
                                     ' onclick="add_depart_record(this)">';
                $result['header'] =  '<span>' . $exist_user_record['ffio']. '</span>';
                                  
                
                $result['rid'] = $exist_user_record['rid'];
                $result['ffio'] = $exist_user_record['ffio'];
                
                $result['title'] = '<span>Шаблоны</span>';
                
                (preg_match('#АСУПК#i', $depart['abb']) == 1 || preg_match('#ОК#i', $depart['abb']) == 1 ?
                        $result['docs_btn'] = '<img src="img/new_add_btn.png" data-dep="'.$depart['rid'].'" data-toggle="tooltip" title="Добавить документ" '. 
                                              ' onclick="add_doc(this);">' : '');
                
                $result['in_arr'] = count($in_arr) > 0 ? $in_arr : '';

                if($totalrows > 0){
                    $result['pagination'] = $this->make_pagination('index', $offset, $rows, $currpage, $totalrows);  
                }
                
                $result['checkbox'] = '<div id="only_unChecked_filter">'.
                                          '<label for=\'only_unChecked\' class="checkboxx">'.
                                              '<input id="only_unChecked" type="checkbox">' . // class=\'custom-control-input\'
                                          '<div class="checkboxx__text">' .
                                              'Только неподписанные записи' .
                                          '</div>'. 
                                          '</label>' .
                                      '</div>';
          
            }else if(count($exist_user_record) == 0){
                  $result['html'] = "<div class=\"k-row\">" .
                                "<h6></h6>" .
                                 "<div class=\"inner\">" .
                                      "<p class='text-center error'>нет доступа</p>".
                                 "</div>".
                $result['header'] = '';
                $result['btn_tmp'] = '';
                $result['ffio'] = '';
                $result['pagination'] = '';
                $result['rid'] = '';
                $result['checkbox'] = '';
                
                $result['title'] = '';
              //  $result['docs'] = '';
                $result['ip'] = 'ip адрес не определен';
            } 
            
            return $result;   
    }

    public function get_folders($user_rid){
        $result = [];
        
        $db = new _dbcomm();
        $folders_list = $db->get_folder_list();

        $user_rec = $db->emp_getListRec($user_rid);
        $depart_rec = $db->dep_record_by_self_rid($user_rec['dep']);
        $depart = $depart_rec['abb'];
        unset($db);

        if(count($folders_list) > 0) 
            $i = 0;
            $margin_bottom = '';
            
            foreach($folders_list as $row){
                $i++;
                if($i === count($folders_list)) $margin_bottom = 'y-mrg-b10';
            
                if($row['holder'] != '')
                    $result['folders'] .= "<div data-holder='".$row['holder']."' class='card-body text-center ".$margin_bottom."'>" .
                                             "<img src='img/folder_transparent.png' onclick='get_docs_by_holder(this);' width='180' height='160' data-holder='".$row['holder']."'>".
                                             "<p onclick='get_docs_by_holder(this);' data-holder='".$row['holder']."'>".$row['holder']."</p>" .   
                                          "</div>";
            
               
            }
    /*    if(count($docs_list) > 0){
            foreach($docs_list as $row){
                if($row['holder'] == ''){
                    $ftype = _assbase::getFtypeByFname($row['fnm']); 

                    $img = $ftype == "unk" ? "" : "<img class='display-block' src='/pktbbase/img/file/" . $ftype . "_64.png'>";

                    $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";

                    $fnm = "<p class='y-gray-text ' " . $fnm_ttip . ">" . _dbbase::shrink_filename($row['fnm'], 30) . "</p>";                        

                    $result['docs'] .=  "<div id='doc_rid-'".$row['rid']." class='card file-card y-shad-light' data-pid='".$row['pid']."'>" . //y-shad-light
                            "<div class='card-body y-pad-tb5 y-pad-lr10 text-center y-cur-point' onclick='doc_view_click(this);' " .
                                                                    "data-doc='" .$row['rid']."'>" . 

                            (preg_match('#АСУПК#i', $depart) == 1 || preg_match('#ОК#i', $depart) == 1 ?
                                "<img id='a_delete_doc-" . $row['rid'] . "' src='img/delete_15.png' onclick='delete_doc_click(this);' " .
                                        "data-fnm='".$row['fnm']."' data-pid='".$row['pid']."' data-flg='".$row['flg']."' ".
                                        " style='margin-right:5px;' class='y-cur-point'>" : '').  

                               $img . $fnm .                                                 
                            "</div>" .

                        "</div>";
                }
            }                      
        }     
    
    */   
        $result['docs'] = $this->get_docs_section($user_rid, '');        
                
        $result['folders'] = $result['folders'].$result['docs'];
                
        return $result;
    }    
    
  //  public function 
    
    public function get_docs_section($user_rid, $holder = ''){
        $db = new _dbcomm();       
        
        $user_rec = $db->emp_getListRec($user_rid);
        $depart_rec = $db->dep_record_by_self_rid($user_rec['dep']);
        $depart = $depart_rec['abb'];
        
        $docs_list = $db->get_docs_list($holder);
        
        unset($db);
         
        $result = '';         
        
            if(count($docs_list) > 0){
                $i = 0; 
                $margin_top = '';
                
                if($i == 0) $margin_top = 'y-mrg-t10';
                
                foreach($docs_list as $row){
                
                        $ftype = _assbase::getFtypeByFname($row['fnm']); 

                        $img = $ftype == "unk" ? "" : "<img class='display-block' src='/pktbbase/img/file/" . $ftype . "_64.png'>";

                        $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";

                        $fnm = "<p class='y-gray-text' " . $fnm_ttip . ">" . _dbbase::shrink_filename($row['fnm'], 30) . "</p>";                        

                        $result .=  "<div id='doc_rid-'".$row['rid']." class='card file-card y-shad-light ".$margin_top."' data-pid='".$row['pid']."'>" . 
                                        "<div class='card-body y-pad-tb5 y-pad-lr10 text-center y-cur-point' onclick='doc_view_click(this);' " .
                                                                        " data-doc='" .$row['rid']."'>" . // data-holder='".$row['holder']."' 

                                        (preg_match('#АСУПК#i', $depart) == 1 || preg_match('#ОК#i', $depart) == 1 ?
                                            "<img id='a_delete_doc-" . $row['rid'] . "' src='img/delete_15.png' onclick='delete_doc_click(this);' " .
                                                    "data-fnm='".$row['fnm']."' data-pid='".$row['pid']."' data-flg='".$row['flg']."' data-holder='".$row['holder']."' ".
                                                    " style='margin-right:5px;' class='y-cur-point'>" : '').  

                                            $img . $fnm .                                                 
                                        "</div>" .
                                    "</div>";            
                  $i++;
                }                      
            } 
            return $result;     
    }
    
    public function getArrow(){
        return '<img src="img/arrow_back.png" width=\'100\' height=\'100\' onclick=\'load_folders();\'>';
    }
    
 
    public function get_table_of_journal($offset, $rows, $totalrows, $emp_position, $emp_depart, $user_rid, $only_unChecked){ 
        $db = new _dbcomm();
        $records = [];
        $in_arr = [];
        
        if(preg_match('#ОК#i', $emp_depart) == 1 || (preg_match('#иректор#i', $emp_position) == 1 AND preg_match('#адм#i', $emp_depart) == 1)){       
              
            if ($offset >= 0 && $offset < $totalrows && $rows > 0 && $rows < $totalrows)        
                $records = $db->departJournal_getListSubset($offset, $rows, $only_unChecked);   
            else 
                $records = $db->get_all_journal($only_unChecked);
        } 
        else if(preg_match('#КТБ#i', $emp_depart) == 1 ||  preg_match('#БТПК#i', $emp_depart) == 1){
            
            if(preg_match('#БТПК#i', $emp_depart) == 1) $in_arr = ['ТОП', 'ТПИ', 'РАНТД']; 
            else 
                $in_arr = ['КиТРМПС', 'ТМД', 'ТПВЛТ', 'КРПВЛТ']; 

            if ($offset >= 0 && $offset < $totalrows && $rows > 0 && $rows < $totalrows)
                  $records = $db->get_journal_by_section_Subset($offset, $rows, $only_unChecked, $in_arr);
            else $records = $db->get_journal_by_section_Whole($only_unChecked, $in_arr);

        }
        else if(preg_match('#лавный#i', $emp_position) == 1 || preg_match('#ачальн#i', $emp_position) == 1){

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

             if ($offset >= 0 && $offset < $totalrows && $rows > 0 && $rows < $totalrows)
                  $records = $db->get_journal_by_section_Subset($offset, $rows, $only_unChecked, $in_arr);
              else 
                  $records = $db->get_journal_by_section_Whole($only_unChecked, $in_arr);
        }
        else{
            if ($offset >= 0 && $offset < $totalrows && $rows > 0 && $rows < $totalrows)
                $records = $db->departJournal_getListSubset_4_simple_user($offset, $rows, $only_unChecked, $user_rid);           
            else 
                $records = $db->get_all_journal_4_simple_user($only_unChecked, $user_rid);
        }        

        $rowset = '';
        
        $result = "<table class='table table-striped' data-user_rid='".$user_rid."'>".      
                            "<thead class='h-0'>" .
                                "<tr class='y-dborder-b'>" .
                                      "{HEADER}".
                                "</tr>" .
                            "</thead>" .
                            "<tbody class='y-border-no-t'>".
                                "{BODY}".   
                            "</tbody>" . 
                        "</table>";
        
        $header_cells = $this->get_table_header();
        
        $result = str_replace("{HEADER}", $header_cells, $result);
        
        if(count($records) > 0){
          //  if(preg_match('#лавный#i', $emp_position) == 1 || preg_match('#ачальник#i', $emp_position) == 1 
                //    || preg_match('#иректор#i', $emp_position) == 1){
          //  foreach($records as $row)              
           //   $rowset .= $this->get_table_body($row, $emp_position, $user_rid);
          //  }else {
                foreach($records as $row)
                    $rowset .= $this->get_table_body($row, $emp_position, $user_rid);

         //   }
        }
        
        $result = str_replace("{BODY}", $rowset, $result);
                        
        return $result;
    }
    
    private function get_table_header(){
        $comm_classes = "text-center align-middle y-border-no";
        
        return  "<th class='tbl-order ".$comm_classes." k-width5'>Дата</th>" . 
                "<th class='tbl-grey-cell ".$comm_classes." k-width20'>Ф.И.О.</th>" .  
                "<th class='tbl-dark-grey-cell ".$comm_classes." k-width15'>Должность</th>" . 
                "<th class='tbl-order ".$comm_classes." k-width20'>Наименование организации, в которую направляется работник</th>" .
                "<th class='tbl-dark-grey-cell ".$comm_classes."'>Убыл</th>" .
                "<th class='tbl-grey-cell ".$comm_classes." k-width5'>Прибыл</th>" .
                "<th class='tbl-dark-grey-cell ".$comm_classes." k-width10'>Подпись начальника /зам начальника отдела</th>" .
                "<th class='tbl-grey-cell ".$comm_classes." k-width10'>Подпись зам директора</th>" .
                "<th class='tbl-order ".$comm_classes." k-width10'>Примечание</th>" ;                       
    }
    
    private function get_table_body_4_simple_user($rec, $emp_position, $user_rid){
        $result = '';  
        $comm_classes = "text-center align-middle y-border-no";

        $dt = $rec['date'];
      
        $year = substr($dt, 0, 4);
        $month = substr($dt, 4, 2);
        $day = substr($dt, 6, 2);
        
        $chief_label = '';
        $vice_chief_label= '';  /*deputy - зам */   
        
        if($rec['flg'] == 1){
            $vice_chief_label = "<input id='vice_chief_label' type='checkbox' checked disabled>"; 
        }else if($rec['flg'] == 4){
            $chief_label = "<input id='chief_label' type='checkbox' checked disabled>"; 
        }else if($rec['flg'] == 5){
            $chief_label = "<input id='chief_label' type='checkbox' checked disabled>"; 
            $vice_chief_label = "<input id='vice_chief_label' type='checkbox' checked disabled>"; 
        } 

        $back_sell = (strlen($rec['back']) > 0) ? $rec['back'] 
                                                      : "<img src='img/edit_pen_64.png' width='32' heoght='32' title='Отредактируйте время прибытия'>";

        $event_start = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_start_input(this);";

        $event = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_back_input(this);";    

        $edit_date_event = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_date_input(this);";

        if($rec['emp_rid'] == $user_rid){
             $result = "<tr id='depart_record-".$rec['rid']."' data-rid='".$rec['rid']."' data-user-rid='".$rec['emp_rid']."' data-flg='".$rec['flg']."'>".
                            "<td class='tbl-order ".$comm_classes." k-width5 edit-cell' onclick='".$edit_date_event."'>".$day.".".$month.".".$year."</td>" . //20210315: 
                            "<td onclick='show_details(this);' class='tbl-grey-cell edit-cell ".$comm_classes." k-width20'>".$rec['ffio']."</td>" .  
                            "<td class='tbl-dark-grey-cell ".$comm_classes." k-width15'>".$rec['position']."</td>" . 
                            "<td class='tbl-order ".$comm_classes." k-width20'>".$rec['org_nm']."</td>" .
                            "<td class='tbl-dark-grey-cell ".$comm_classes." k-width5 edit-cell' onclick='".$event_start."' >".$rec['leave_tm']."</td>" .
                            "<td class='tbl-grey-cell ".$comm_classes." edit-cell k-width5' onclick='".$event."' >".$back_sell."</td>" . 
                            "<td class='tbl-dark-grey-cell ".$comm_classes." k-width10 label'>".$vice_chief_label."</td>" .
                            "<td class='tbl-grey-cell ".$comm_classes." k-width10 label'>".$chief_label."</td>" .
                            "<td class='tbl-order ".$comm_classes." k-width10'>".$rec['rem']."</td>" ;     
                      "</tr>";
        }
        
        return $result;
    }
    
    private function get_table_body($rec, $emp_position, $user_rid){
        $result = '';  
        $comm_classes = "text-center align-middle y-border-no";

        $dt = $rec['date'];
      
        $year = substr($dt, 0, 4);
        $month = substr($dt, 4, 2);
        $day = substr($dt, 6, 2);
        
        $chief_label = '';
        $vice_chief_label= '';  /*deputy - зам */   
        
        
        if(preg_match('#лавный#i', $emp_position) == 1 || preg_match('#ачальник#i', $emp_position) == 1 || preg_match('#иректор#i', $emp_position) == 1){
            $chief_label = "<input id='chief_label' type='checkbox' onclick='get_label(this);'>"; 
            $vice_chief_label = "<input id='vice_chief_label' type='checkbox' onclick='get_label(this);'>"; 
        }
        
        if($rec['flg'] == 1){
            $vice_chief_label = "<input id='vice_chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
        }else if($rec['flg'] == 4){
            $chief_label = "<input id='chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
        }else if($rec['flg'] == 5){
            $chief_label = "<input id='chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
            $vice_chief_label = "<input id='vice_chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
        } 
        
        $back_sell = (strlen($rec['back']) > 0) ? $rec['back'] 
                                                      : "<img src='img/edit_pen_64.png' width='32' height='32' title='Отредактируйте время прибытия'>";
                                                      
        $event_start = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_start_input(this);";
        
        $event = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_back_input(this);";   
        
        $edit_date_event = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_date_input(this);";
        
        $attr_title = ($rec['emp_rid'] == $user_rid) ? 'Кликните чтобы отредактировать' : '';
        
        $result = "<tr id='depart_record-".$rec['rid']."' data-rid='".$rec['rid']."' data-user-rid='".$rec['emp_rid']."' data-flg='".$rec['flg']."'>".
                        "<td title='".$attr_title."' class='tbl-order ".$comm_classes." k-width5 edit-cell' onclick='".$edit_date_event."' >".$day.".".$month.".".$year."</td>" . //20210315: 
                        "<td onclick='show_details(this);' class='tbl-grey-cell edit-cell ".$comm_classes." k-width20'>".$rec['ffio']."</td>" .  
                        "<td class='tbl-dark-grey-cell ".$comm_classes." k-width15'>".$rec['position']."</td>" . 
                        "<td class='tbl-order ".$comm_classes." k-width20'>".$rec['org_nm']."</td>" .
                        "<td title='".$attr_title."' class='tbl-dark-grey-cell ".$comm_classes." k-width5 edit-cell' onclick='".$event_start."' >".$rec['leave_tm']."</td>" .
                        "<td title='".$attr_title."' class='tbl-grey-cell ".$comm_classes." edit-cell k-width5' id='back_sell' onclick='".$event."' >".$back_sell."</td>" . 
                        "<td class='tbl-dark-grey-cell ".$comm_classes." k-width10 label'>".$vice_chief_label."</td>" .
                        "<td class='tbl-grey-cell ".$comm_classes." k-width10 label'>".$chief_label."</td>" .
                        "<td class='tbl-order ".$comm_classes." k-width10'>".$rec['rem']."</td>" ;     
                  "</tr>";

        return $result;
    }
    
    public function get_fm_loader() : string {   // $dt: 0-yesterday, 1-today    (string $orto, string $orfr, int $dt)
        $result = '';
 
        $result = $this->get_fm("__fm_loader");

        if (mb_strlen($result) > 0) {
           $db = new _dbcomm();
           $flist = $db->get_folder_list();
            
            $opt_list = "<option style='color:#C9C9C9;' value=''>Корневая папка</option>";

           if (count($flist) > 0)
                foreach($flist as $row)
                    if(mb_strlen($row['holder']) > 0)
                       $opt_list .= "<option value='" . $row['holder'] . "'>" . $row['holder'] . "</option>";

            $result = str_replace("{loader_ex_fldr_choose:options}", $opt_list, $result);    // what, with, in
        }
        
        return $result;
    }
    
    public function dsrc_getFolderList4Yesterday(string $orto, string $orfr) : array {   // function only for operator (nxo)
        $t = DateTime::createFromFormat('Ymd', date('Ymd'));            
        $y = $t->modify('-1 day'); 
        
        return $this->dsrc_getFolderList4TheYmd($y->format('Ymd'), $orto, $orfr);
    }
    
    public function dsrc_getFolderList4Today(string $orto, string $orfr) : array {   // function only for operator (nxo)
        return $this->dsrc_getFolderList4TheYmd(date('Ymd'), $orto, $orfr);
    }
    
    public function dsrc_getFolderList4TheYmd(string $yyyymmdd, string $orto, string $orfr) : array {
        $result = [];
        
        $conn = $this->connectDBDef();

        if ($conn !== false) {
            //$q = mysqli_prepare($conn, "SELECT DISTINCT fldr FROM dsrc WHERE orto=? AND fldr<>'' AND dtts=DATE_FORMAT(CURDATE(),'%Y%m%d')");
            $q = mysqli_prepare($conn, "SELECT DISTINCT fldr FROM dsrc WHERE orto=? AND fldr<>'' AND dtts='" . $yyyymmdd . "'");

            if ($q !== false) {
                //mysqli_stmt_bind_param($q, 'ss', $orto, $orfr);
                mysqli_stmt_bind_param($q, 's', $orto);

                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_fldr);

                while (mysqli_stmt_fetch($q))
                    array_push($result, $sel_fldr);

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }

            $this->closeConnection($conn);
            unset($conn);
        }
        
        return $result;
    }
    
    public static function getTimeWithoutSecont ($str){
            $arr = explode(':', $str);
            $result = $arr[0].':'.$arr[1];
            return $result;
   } 
   
       public function seconds2times($seconds){
        $times = array();

        // считать нули в значениях
        $count_zero = false;

        // количество секунд в году не учитывает високосный год
        // поэтому функция считает что в году 365 дней
        // секунд в минуте|часе|сутках|году
        $periods = array(60, 3600, 86400, 31536000);

        for ($i = 3; $i >= 0; $i--){
            $period = floor($seconds/$periods[$i]);

            if (($period > 0) || ($period == 0 && $count_zero)){
                $times[$i+1] = $period;
                $seconds -= $period * $periods[$i];

                $count_zero = true;
            }
        }    
        $times[0] = $seconds;
        return $times;
    }

    public function timeForTable($diff){       
        $res = '';
        $seconds = array($diff);
                        // значения времени
                $times_values = array('с.','м.','ч.','д.','лет');

                    foreach ($seconds as $second){
                          //  $res .= $second . ' сек. = ';

                            $times = $this->seconds2times($second);
                            for ($i = count($times)-1; $i >= 0; $i--) {
                                $res .= $times[$i] . '' . $times_values[$i] . ' ';
                            }                           
                    }
        return $res;
    }
    
}

/*
  $db = new _dbcomm();


 $result = $db->get_docs_list();
 $docs = [];
  
  foreach($result as $row){
                if($row['holder'] == ''){
                     var_dump($row['holder']);
                }
  }
 
  var_dump($docs);
*/
  
/*
$depart['abb'] = 'ТМД';
$position['ttl'] = 'Начальник отдела';
$totalrows = 0;
$in_arr = [];
       
                if(preg_match('#ОК#i', $depart['abb']) == 1 || (preg_match('#иректор#i', $position['ttl']) == 1 AND preg_match('#адм#i', $depart['abb']) == 1)){                
                    $totalrows = 1;
                    
                } 
                else if(preg_match('#адм_КТБ#i', $depart['abb']) == 1 ||  preg_match('#адм_БТПК#i', $depart['abb']) == 1){
                    if(preg_match('#адм_БТПК#i', $depart['abb']) == 1) $in_arr = ['ТОП', 'ТПИ', 'РАНТД']; 
                    else $in_arr = ['КиТРМПС', 'ТМД', 'ТПВЛТ', 'КРПВЛТ']; 
                    
                    $totalrows = 4;
                    
                }
                else if(preg_match('#лавный#i', $position['ttl']) == 1 || preg_match('#ачальн#i', $position['ttl']) == 1){
             
                    
                    if(preg_match('#лавный#i', $position['ttl']) == 1){
                        $in_arr = ['АХО', 'РАСУПК', 'ТО', 'ОМС'];
                    }
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ОЭ#i', $depart['abb']) == 1){
                       $in_arr = ['ОЭ']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#РАСУПК#i', $depart['abb']) == 1){
                       $in_arr = ['РАСУПК']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ОМС#i', $depart['abb']) == 1){
                       $in_arr = ['ОМС']; 
                    }  
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ТО#i', $depart['abb']) == 1){
                       $in_arr = ['ТО']; 
                    }  
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#КиТРМПС#i', $depart['abb']) == 1){
                       $in_arr = ['КиТРМПС']; 
                    }
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ТМД#i', $depart['abb']) == 1){
                       $in_arr = ['ТМД']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#ТПВЛТ#i', $depart['abb']) == 1){
                       $in_arr = ['ТПВЛТ']; 
                    } 
                    else if(preg_match('#ачальн#i', $position['ttl']) == 1 AND preg_match('#КРПВЛТ#i', $depart['abb']) == 1){
                       $in_arr = ['КРПВЛТ']; 
                    } 
                    
                    $totalrows = 2;
                }
                
                else{
                    $totalrows = 3;
                }
               
    echo $totalrows .'<hr>';        
    var_dump($in_arr);             
*/
/*
     $in_arr = ['d', 'РАСdУПК', '44', 'ОМС'];
    $in_str = ''; 
    $i = 0;

    foreach($in_arr as $el){
        if(mb_strlen($in_str) > 0){
            $in_str .= ', ';
        }
        $in_str .= $el;
    }
      var_dump($in_str);


  $db = new _dbcomm();

  $emp_position = $db->pos_record_by_self_rid('0a6097ba-be65-41e1-bf2c-199b7382cbcb');
 // echo $emp_position['ttl'];
  $only_unChecked = 1;
  $user_rid = '1804beed-eb12-4fd3-be43-9b73edbc080f';
  $totalrows = 0;
        
        if(preg_match('#лавный#i', $emp_position['ttl']) == 1 || preg_match('#ачальник#i', $emp_position['ttl']) == 1 
                    || preg_match('#иректор#i', $emp_position['ttl']) == 1){

              $records = $db->get_all_journal($only_unChecked);
              $totalrows = $db->depart_getRowcount($only_unChecked);
        }else{
  
                $records = $db->get_all_journal_4_simple_user($only_unChecked, $user_rid);
                $totalrows = $db->depart_getRowcount_4_simple_user($only_unChecked, $user_rid);
        }

          var_dump($totalrows);
*/




//$db = new assist();
//$result = $db->get_fm_loader();
            
  //var_dump($result);


        /*}else{
             if($rec['flg'] == 1){
                $vice_chief_label = "<input id='vice_chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
            }else if($rec['flg'] == 4){
                $chief_label = "<input id='chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
            }else if($rec['flg'] == 5){
                $chief_label = "<input id='chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
                $vice_chief_label = "<input id='vice_chief_label' type='checkbox' onclick='get_label(this);' checked disabled>"; 
            } 
            
            $back_sell = (strlen($rec['back']) > 0) ? $rec['back'] 
                                                          : "<img src='img/edit_pen_64.png' width='32' heoght='32' title='Отредактируйте время прибытия'>";

            $event_start = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_start_input(this);";

            $event = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_back_input(this);";    
            
            $edit_date_event = (preg_match('#checked#', $chief_label) == 1) ?  "cant_edit_note();" : "show_date_input(this);";
            
            if($rec['emp_rid'] == $user_rid){
                 $result = "<tr id='depart_record-".$rec['rid']."' data-rid='".$rec['rid']."' data-user-rid='".$rec['emp_rid']."' data-flg='".$rec['flg']."'>".
                                "<td class='tbl-order ".$comm_classes." k-width5 edit-cell' ondblclick='".$edit_date_event."'>".$day.".".$month.".".$year."</td>" . //20210315: 
                                "<td ondblclick='show_details(this);' class='tbl-grey-cell edit-cell ".$comm_classes." k-width20'>".$rec['ffio']."</td>" .  
                                "<td class='tbl-dark-grey-cell ".$comm_classes." k-width15'>".$rec['position']."</td>" . 
                                "<td class='tbl-order ".$comm_classes." k-width20'>".$rec['org_nm']."</td>" .
                                "<td class='tbl-dark-grey-cell ".$comm_classes." k-width5 edit-cell' ondblclick='".$event_start."' >".$rec['leave_tm']."</td>" .
                                "<td class='tbl-grey-cell ".$comm_classes." edit-cell k-width5' ondblclick='".$event."' >".$back_sell."</td>" . 
                                "<td class='tbl-dark-grey-cell ".$comm_classes." k-width10 label'>".$vice_chief_label."</td>" .
                                "<td class='tbl-grey-cell ".$comm_classes." k-width10 label'>".$chief_label."</td>" .
                                "<td class='tbl-order ".$comm_classes." k-width10'>".$rec['rem']."</td>" ;     
                          "</tr>";
            }
        }
        
        */

//$arr = [1,2,3];
//var_dump(count($arr));

?>