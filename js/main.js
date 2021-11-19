/* global mess */
 window.$$_scroll_to_id = '';
 var $$_loader_max_photo_side_px_ = 1280;

var _main = {   // interface to index.js
    kadr_changed:         function (pg_id)           { kadr_changed(pg_id); },
    get_currentLastpage: function (pg_id)       {return get_currentLastpage(pg_id);},
    reload_page:         function ()             {show_my_request();}, 
    start_gopage:         function (pg_id, new_page) { start_gopage(pg_id, new_page); }
    
}

var _index = {
    pg_performGo:         function (pg_id)           { pg_performGo(pg_id); },
    docget_ok_click:      function (data_sec, data_rid, doc_nm, doc_flg) { docget_ok_click(data_sec, data_rid, doc_nm, doc_flg); },
    glob_fm_before_show:  function ()                { glob_fm_before_show(); },
    glob_fm_after_show:   function ()                { glob_fm_after_show(); }
};


$(function() {      // Shorthand for $(document).ready(function() {
    "use strict";
    
    window.addEventListener("popstate", function() { 
        if ($(".modal.show").length > 0)
            $(".modal.show").modal('hide');
    });
    
    $(window).resize(function(){ // Событие resize, происходит тогда когда меняется размеры объекта window ( т.е. окна браузера)
              measure();    
              
            //  $('#docs_body img').each(function(){
           //      console.log($(this))
           //   });
    });
    
    $(document).mouseup(function(e) {
        if ($(".popover.outhide").length > 0)
            $(".popover.outhide").each(function () {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0) $(this).popover("dispose");
            });
            
            var sideout = $("#sideout");
        
        if (sideout.hasClass("active")) {
            // if the target of the click isn't button or menupanel nor a descendant of this elements
            if (!sideout.is(e.target) && sideout.has(e.target).length === 0)
               _sout.hide_sideout();
        }
            
        _common.close_tooltips();
    });
  
    $.when($.getScript("/pktbbase/js/_common.js") ,
           $.getScript("/pktbbase/js/_fdb.js",
           $.getScript("/pktbbase/js/_help.js"),
           $.getScript("/pktbbase/js/_bbmon.js")),
           $.getScript("/pktbbase/js/_viewer.js"),
           $.getScript("/pktbbase/js/_sout.js"),
           $.getScript("/pktbbase/js/_docget.js")
           )
        .done(function () {            
            // all synchronous functions here
            load_page();        // recreate_page here
        });
        
}); // End of use strict


function measure(){
    $('#conteiner').css({'height': $('#main').innerHeight() - $('#main').innerHeight() / 4.5 + 'px'});
    $('#docs_body').css({'height': $('#main').innerHeight() - 200 + 'px'});

    $('#pagination').css({'bottom': $('#conteiner').innerHeight() / 4 + 'px'});
    
    $('#test img').css({'width' : $('#conteiner').innerWidth() + 'px', 'height' : $('#conteiner').innerHeight() / 5 + 'px'});

    $('#ip').css({'top': $('#conteiner').innerHeight()  + 140 + 'px'});
    $('#bg_tmp img').css({'width': $('body').innerWidth(), 'height': $('body').innerHeight()});     
}

function reset_clnf_vars(){
    _common.storeSession('user_rid', '');
    _common.storeSession('user_ffio', '');
};


function pg_performGo(pg_id) {  // function is called when pagination link is clicked
    switch (pg_id) {
        default:
            load_page();
    }
}

function select_search_item(rid) {  // ex: rws:879876876.... перескакивает на нужное место в таблице

    var postForm = {
       'part' : 'search_get_depart_row',
       'rid'  : rid,   
       'emp_rid' : _common.getStoredSessionStr('user_rid')
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        success   : function(data) {
                        if (data.success) {
                            $$_scroll_to_id = '#depart_record-' + rid; 

console.log(data);
                            if (data.rownum >= 0) {    
                                console.log(data.rownum);
                                start_gopage('index', Math.floor(data.rownum/10));
                            }
                            
                        }
        }
    });
}

function start_gopage(pg_id, new_page) {
    _common.storeSession(pg_id + '_last_page', new_page);
        
    load_page();
}

function start_ynPopoverForDeleteX(jq_elem) {
    var el_id = jq_elem.attr('id'),
            
        rid = _common.value_fromElementID(el_id),
        
        pref = el_id.substring(0, el_id.indexOf('-')),
        part = pref.substring(pref.lastIndexOf('_') + 1);
                                  
    var subj_type = '', subj_name = '';

    switch (part) {
        case 'doc' : {
            subj_type = 'документ';
            subj_name = jq_elem.attr('data-fnm');  
            break;
        }
    }
        
    var btn_comm_classes = "btn btn-sm y-mrg-t10 y-mrg-r10 y-mrg-b10 y-shad";

    jq_elem.popover({
        delay: { "show": 500, "hide": 100 },
        placement : 'left',
        html : true,
        template: '<div class="popover yn-popover outhide" role="tooltip">' + 
                    '<h3 class="popover-header"></h3><div class="popover-body"></div></div>',
        content : 
                '<span>Действительно удалить ' + subj_type + ' <i class="y-dred-text">' + subj_name + '</i> ?</span>' +
                "<div class='text-right'>" + "<a id='popover_yes' href='javascript:;'>Да</a><a id='popover_no' href='javascript:;'>Нет</a>"+
                  // "<button id='popover_yes' type='button' class='btn-warning'>Да</button>" + //" + btn_comm_classes + "
              //     "<button id='popover_no' type='button' class='btn-light " + btn_comm_classes + "'>Нет</button>" +
               "</div>"
    }).popover('show');

    $('.popover-header').text('Требуется подтверждение');
    
    $('#popover_yes').unbind('click').on('click',function() {
        $('.yn-popover').popover('dispose');

        switch (part) {
            case 'doc': {
                delete_doc(rid, jq_elem);
                break;
            }
        }
        
        jq_elem.tooltip('hide');
    });

    $('#popover_no').unbind('click').on('click',function() {
        $('.yn-popover').popover('dispose');
    });
}

function load_page() {
    reset_clnf_vars(); 
    
    var lastpage = _common.get_currentLastpage('index');
    var pg = 'index';
    var only_unChecked =  _common.getStoredLocalInt('only_unChecked'); 
    
    var postForm = {
       'part'  : 'checkIp',
       'offset': lastpage * 10,
       'rows': 10,
       'currpage': lastpage,
       'iparam' : only_unChecked
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        complete: function() {
              //  load_docs_block()
              load_folders();
        },
        error: function (jqXHR, exception) {

        },
        success: function(data) {   
            
                        measure();
                     //   load_docs_block()

                        _common.storeSession('user_rid', data.rid); 
                        _common.storeSession('user_ffio', data.ffio);
                        _common.storeSession('in_arr', data.in_arr);

                        $('#conteiner').html(data.html);


                        $('#pagination').html(data.pagination);
                        $('#btn_tmp').html(data.add_btn);
                        $('#ffio').html(data.header);

                        $('#checkbox_tmp').html(data.checkbox);                           

                        $('#docs_title').html(data.title);
                        $('#btn_tmp_docs').html(data.docs_btn);
                      //  $('#docs_body').html(data.folders);

                     //   $('#conteiner').css({'height': $('#main').innerHeight() - $('#main').innerHeight() / 4.5 + 'px'})
                      //  $('#docs_body').css({'height': $('#main').innerHeight() - 200 + 'px'})
                            
                        $('#ip').html(data.ip);

                        $('#only_unChecked').prop('checked', only_unChecked == 1).change(function() { 
                            // storeLocal записывает в localStorage 1 или 0
                           _common.storeLocal('only_unChecked', $(this).prop('checked') ? 1 : 0);
                           load_page();
                        });

                        if ($('.' + pg + '-pagination').length == 0)
                        _common.storeSession(pg + '_last_page', -1);
                        else {
                            var li_id = '#' + pg + '_li_to_page-' + lastpage;

                            $(li_id + '.active').removeClass('active');
                            $(li_id).addClass('active');                                     
                        }   

                        _common.process_scrollToID();
                           $('#srch_box')
                                .autocomplete({
                                    serviceUrl: 'php/_search.php',
                                    paramName:  'srch_box',
                                    autoSelectFirst: true,
                                    //maxHeight: 350,
                                    triggerSelectOnValidInput: false,   // block onselect firing on browser activate
                                    showNoSuggestionNotice: true,
                                    noSuggestionNotice: 'Совпадений не найдено',
                                    minChars: 2,
                                    //lookupLimit: 100,
                                    onSelect: function (suggestion) {

                                       select_search_item(suggestion.data.trim()); // suggestion.data: rid, suggestion.value: pname

                                       $('#srch_box').val('');   //  и есть контекст
                                    },
                                    onSearchStart: function () {
                                    },
                                    onSearchComplete: function (query, suggestions) {
                                    },
                                    //onInvalidateSelection: function (suggestion) {
                                    //},
                                    onHide: function (container) {   // call only when suggestions found or "No results" was visible. So set showNoSuggestionNotice to true
                                    },
                                    beforeRender: function (container, suggestions) {

                                    }
                                }); 
                                
    /*                            
                                                         
            $('[data-holder] img').each(function(){                
                $(this).hover(function(){
                    console.log($(this));
                    $(this).css({width: '220px'});
                });
                
                $(this).mouseleave(function(){
                    $(this).css({width: '200px', transition: 'width .5s'});
                });
            }); 
*/
        }
    });
}

function load_folders(){

    var postForm = {
       'part'  : 'get_fldrs',
       'user_rid' : $('table').attr('data-user_rid')
    };
    
    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {

        },
        complete: function(){
                                           
            $('[data-holder] img').each(function(){                
                $(this).hover(function(){
                    $(this).css({width: '200px', transition: 'width .5s'});
                });
                
                $(this).mouseleave(function(){
                    $(this).css({width: '180px', transition: 'width .5s'});
                });
            }); 
            
             $('[data-holder] p').each(function(){                
                $(this).hover(function(){
                    $(this).prev().css({width: '200px', transition: 'width .5s'});
                });
                
                $(this).mouseleave(function(){
                    $(this).prev().css({width: '180px', transition: 'width .5s'});
                });
            }); 

        },
        success: function(data) { 
            $('#back_arrow_tmp').empty();
            $('#docs_body').html(data.folders);        
        }
    }); 
}

/*
function load_docs_block(){
    
    var postForm = {
       'part'  : 'get_docs',
       'user_rid' : $('table').attr('data-user_rid')
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {

        },
        success: function(data) { 
            console.log(data);
                
                $('#docs_body').html(data.docs);
        }
    }); 
}
*/

function add_depart_record(e){
     var postForm = {
       'part'  : 'get_fm',
       'fm_id': 'fm_depart_record'
    };
    
    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {
           // recreate_page();
        },
        success: function(data) {    
                   if(data.success){
                        $('#div_tmp').html(data.html);     

                        $('#fm_depart_record').attr('data-rid', $(e).attr('data-user-rid')).attr('data-ffio', $(e).attr('data-ffio'))
                                              .attr('data-pos', $(e).attr('data-pos')).attr('data-date', $(e).attr('data-date'))                         
                       .on('show.bs.modal', function () {   
                            
                             /*  когда отправка формы была здесь, то запись двоилась??? добавлялась в базу два раза
                               $('#depart_record_ok').click(function(){
                                 send_depart_record();
                               });  
                              
                              */
                           
                        })
                        .on('shown.bs.modal', function () {
                            
                            $('#leave_date').datepicker({
                                format: 'dd.mm.yyyy',
                                autoclose: true,
                                keyboardNavigation: false,
                                language: 'ru',
                                startDate: '01.01.1901',
								todayHighlight: true,
                            });   
                           
                            $('#depart_time').timepicker({
                                                timeFormat: 'H:i',
                                                step: 10,
                                                minTime: '8',
                                                maxTime: '5:45pm',
                                                defaultTime: '11',
                                                startTime: '08:00',
                                                dynamic: false,
                                                dropdown: true,
                                                scrollbar: true
                                            });                              
                                        
                            $('#back_time').timepicker({
                                                timeFormat: 'H:i',
                                                step: 10,
                                                minTime: '8',
                                                maxTime: '5:45pm',
                                                defaultTime: '11',
                                                startTime: '08:00',
                                                dynamic: false,
                                                dropdown: true,
                                                scrollbar: true
                                            });  
                                            
                            $('#depart_record_ok').click(function(){
                                 send_depart_record();
                             });                
                            
                        })            
                        .modal('show');
                   }
        }
    });
}

function send_depart_record(){
    var org_nm = $('#org_nm').val().trim(), depart_time = $('#depart_time').val().trim(), back_time = $('#back_time').val().trim();

    if(org_nm.length > 0 && depart_time.length > 0){
            
        if(/^[0-9]{2}:[0-9]{2}$/.test(depart_time) && (/^[0-9]{2}:[0-9]{2}$/.test(back_time) || back_time == '') ){
            
            var date = $('#leave_date').val();
            var date_arr = date.split('.');
            var day = date_arr[0];
            var m = date_arr[1];
            var y = date_arr[2];
            var date_ = y+'.'+m+'.'+day+' '+depart_time+ ':00';

            var postForm = {
                'part': 'add_journal_record',
                'org_nm' : org_nm,
                'depart' :depart_time,
                'back' : back_time,
                'rem' : $('#remark').val(),

                'user_rid': $('#fm_depart_record').attr('data-rid'),
               // 'position' : $('#fm_depart_record').attr('data-pos'),
               // 'date': $('#fm_depart_record').attr('data-date') //когда дата проставлялась автоматически
                 'date': date_
            };

            $.ajax({
                type      : 'POST',
                url       : 'php/jgate.php',
                data      : postForm,
                dataType  : 'json',
                error: function (jqXHR, exception) {
                   // recreate_page();
                },
                success: function(data) {    
                 
                           if(data.success){            
                                $('#fm_depart_record').modal('hide');
                                 load_page();
                            } 
                }
            });
        }else _common.say_noty_err("Должно быть указано время! Вместо безвозвратно укажите 17:45");
    } else _common.say_noty_err("Поля \"Наименование организации\" и \"Убыл\" обязательны для заполнения");
}
 
/*
function show_back_input(e){
    var input = document.createElement('input');
    var text = e.innerHTML;
    var self = e;
    e.innerHTML= '';
    input.value = text;
    input.style.width = 100+'px';
    input.classList.add('form-control'); 
    e.appendChild(input);
    
    input.addEventListener('blur', function(){
        self.innerHTML = this.value;
        
        self.addEventListener('dblclick', function(){
            show_back_input(this)
        });
        self.removeChild(this); 
    });
    e.ondblclick = function(){};
    
}

*/


function show_start_input (e){
    // вообще эта проверка  рудимент, т.к. каждый пользователь видит только свои записи, но пока пусть будет
    if(_common.getStoredSessionStr('user_rid') == $(e).parent().attr('data-user-rid')){
        var start_tm = $(e).text();
      
        var postForm = {
            'part' : 'get_fm',
            'fm_id': 'edit_start_tm_fm'
        };
         
        $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            error: function (jqXHR, exception) {
              
            },
            success: function(data) {    
                       if(data.success){            
                            $('#div_tmp').html(data.html);     

                           $('#edit_start_tm_fm').attr('data-rid', $(e).parent().attr('data-rid'))
                           .on('show.bs.modal', function () {   

                                 $('#start_tm_ok').click(function(){
                                     send_start_tm();
                                 });

                            })
                            .on('shown.bs.modal', function () {
  
                            $('#start_time_edit').timepicker({
                                                timeFormat: 'H:i',
                                                step: 10,
                                                minTime: '8',
                                                maxTime: '5:45pm',
                                                defaultTime: '11',
                                                startTime: start_tm,
                                                dynamic: false,
                                                dropdown: true,
                                                scrollbar: true
                                            });                                  
                             })            
                             .modal('show');
                        } 
            }
        });
    }
}

function send_start_tm (){
    
     var postForm = {
            'part' : 'edit_start_tm',
            'rid': $('#edit_start_tm_fm').attr('data-rid'),
            'start': $('#start_time_edit').val()
        };
        
         $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            error: function (jqXHR, exception) {
              
            },
            success: function(data) {    
                       if(data.success){            
                           $('#edit_start_tm_fm').modal('hide');
                           load_page();
                        } 
            }
        });
}

function show_back_input (e){
   
    // вообще эта проверка  рудимент, т.к. каждый пользователь видит только свои записи, но пока пусть будет
    if(_common.getStoredSessionStr('user_rid') == $(e).parent().attr('data-user-rid')){
        var back_tm = $(e).text();
      
        var postForm = {
            'part' : 'get_fm',
            'fm_id': 'edit_back_tm'
        };
        
        $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            error: function (jqXHR, exception) {
              
            },
            success: function(data) {    
                       if(data.success){            
                            $('#div_tmp').html(data.html);     

                           $('#edit_back_tm').attr('data-rid', $(e).parent().attr('data-rid'))
                           .on('show.bs.modal', function () {   

                                 $('#back_tm_ok').click(function(){
                                     send_back_tm();
                                 });

                            })
                            .on('shown.bs.modal', function () {
  
                            $('#back_time_edit').timepicker({
                                                timeFormat: 'H:i',
                                                step: 10,
                                                minTime: '8',
                                                maxTime: '5:45pm',
                                                defaultTime: '11',
                                                startTime: back_tm,
                                                dynamic: false,
                                                dropdown: true,
                                                scrollbar: true
                                            });                                  
                             })            
                             .modal('show');
                        } 
            }
        });
    }
}

function send_back_tm (){
    
     var postForm = {
            'part' : 'edit_back_tm',
            'rid': $('#edit_back_tm').attr('data-rid'),
            'back': $('#back_time_edit').val()
        };
        
         $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            error: function (jqXHR, exception) {
              
            },
            success: function(data) {    
                       if(data.success){            
                           $('#edit_back_tm').modal('hide');
                           load_page();
                        } 
            }
        });
}

function cant_edit_note(){ // уведомление общее для даты и времени прихода
   var postForm = {
        'part'  : 'get_fm',
        'fm_id': 'edit_unabled_notify'
   };
   
   $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {

        },
        success: function(data) {    
                    if(data.success){            
                        $('#div_tmp').html(data.html);     

                       $('#edit_unabled_notify')
                       .on('show.bs.modal', function () {   

                             $('#close_notify').click(function(){
                                 $('#edit_unabled_notify').modal('hide');
                             });
                        })
                        .on('shown.bs.modal', function () {
                            
                        })            
                        .modal('show');
                    } 
        }
    });
}



function show_date_input(e){
    if(_common.getStoredSessionStr('user_rid') == $(e).parent().attr('data-user-rid')){
        var date = $(e).text();

        var postForm = {
            'part' : 'get_fm',
            'fm_id': 'edit_date'
        };
        
        $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            error: function (jqXHR, exception) {
              
            },
            success: function(data) {    
                       if(data.success){            
                            $('#div_tmp').html(data.html);     

                           $('#edit_date').attr('data-rid', $(e).parent().attr('data-rid'))
                           .on('show.bs.modal', function () {   

                                 $('#date_ok').click(function(){
                                     send_date();
                                 });

                            })
                            .on('shown.bs.modal', function () {
                                
                          $('#date_edit').datepicker({
                                                format: 'dd.mm.yyyy',
                                                autoclose: true,
                                                keyboardNavigation: false,
                                                language: 'ru',
                                                startDate: '01.01.1901', //'01.01.1901', не работае, инпут пустой
												todayHighlight: true,
                                            });
                            })            
                            .modal('show');
                        } 
            }
        }); 
    }
}

function send_date(){
    var date = $('#date_edit').val();
    var date_arr = date.split('.');
    var day = date_arr[0];
    var m = date_arr[1];
    var y = date_arr[2];
    var date_ = y+'.'+m+'.'+day;       
    
    var postForm = {
            'part' : 'edit_date',
            'rid': $('#edit_date').attr('data-rid'),
            'date': date_
    };

     $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {

        },
        success: function(data) {                  
                   if(data.success){            
                       $('#edit_date').modal('hide');
                       load_page();
                    } 
        }
    });

}

function get_label(e){
   
    var vice_chief_label = $(e).parent().parent().find('input#vice_chief_label').prop('checked') ? 1 : 0;
    var chief_label = $(e).parent().parent().find('input#chief_label').prop('checked') ? 1 : 0;

    var flg = vice_chief_label | (chief_label << 2);
 
    if(/^<img/.test($(e).parent().parent().find('#back_sell').html()))
         $(e).parent().parent().find('#back_sell').html('17:45');
     
    var postForm = {
            'part' : 'set_label',
            'rid' : $(e).parent().parent().attr('data-rid'),
            'flg' : flg,
            'back': $(e).parent().parent().find('#back_sell').html()
    }; 

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {

        },
        success: function(data) {    
                   if(data.success){ 
                       //console.log($('#back_sell').html());
                       //   console.log($(e).parent().parent().find('#back_sell').html());
                       
                       // 0x3 - это последние два бита(11), 0x2 - втрой бит(10), 0x1 - первый бит (1)
                       // можно перезагрузить страницу, но вряд ли это понадобиться
                    } 
        }
    });
}


function show_details(e) {
   var postForm = {
        'part'  : 'get_fm',
        'fm_id': 'employee_depart_deteils',
        'emp_rid' : $(e).parent().attr('data-user-rid')
   };
   
   $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {

        },
        success: function(data) {    
                    if(data.success){            
                        $('#div_tmp').html(data.html);     

                       $('#employee_depart_deteils').attr('data-rid', '')
                       .on('show.bs.modal', function () {   

                             $('#close_deteils_ok').click(function(){
                                 $('#employee_depart_deteils').modal('hide');
                             });

                        })
                        .on('shown.bs.modal', function () {
                            
                        })            
                        .modal('show');
                    } 
        }
    });
}

function add_doc(e){

     var postForm = {
           'part' : 'get_fm_loader'
        };
        
      $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            error: function (jqXHR, exception) {
                $('#div_tmp').empty();
            },
            success   : function(data) {

                            if (data.success && data.html.length > 0) {
                                $('#div_tmp').html(data.html);

                                  $("#loader_drop_area")
                                    .on('dragenter', function (e) { loader_process_ddEvent(this, e, '#EAF4FF'); })
                                    .on('dragleave', function (e) { loader_process_ddEvent(this, e, '#F9F9F9'); })
                                    .on('dragover', function (e) { loader_process_ddEvent(this, e, '#EAF4FF'); })
                                    .on('drop', function (e){
                                        loader_process_ddEvent(this, e, '#F9F9F9');
                                        loader_set_files_up(e.originalEvent.dataTransfer.files); //[0]
                                    });
                                // End of: Image File choose block

                                // File choose button block
                                $('#loader_file_selector').on('change', function() {
                                    loader_set_files_up($('#loader_file_selector')[0].files);   //[0]
                                });

                                var diff = $('body').outerHeight() - $(window).innerHeight();   // in px
                                if (diff < 0) diff = 0;

                                var svh = _common.vh_forMaxForms();

                                $('#fm_loader').attr('data-dep', $(e).attr('data-dep')).attr('data-holder', $('[id^="a_delete_doc"]').attr('data-holder'))
                                    .on('show.bs.modal', function () {
                                        
                                       if($(this).attr('data-holder').length > 0){
                                           var data_holder = $(this).attr('data-holder');
                                        //   $("#loader_use_ex_fldr").prop("checked", true);
                                           $("#loader_ex_fldr_choose option").each(function(){
                                               
                                             if($(this).val() == data_holder){
                                               console.log($(this).prop('selected', true))
                                             } //$("#loader_ex_fldr_choose option").prop('selected', true)
                                                
                                           })
                                       }
                                           

                                        $(".modal-content").css("height", "calc(" + svh + "vh - " + diff + "px)");
                                        // pre-set small height of modal body. In on shown will open it up. (Else - will twitch(дергаться)
                                        $(".modal-body").css("height", "100%");
                                    
                                        $('#loader_use_ex_fldr').prop('checked', true);
                                        
                                        $('#loader_action').unbind('click').on('click',function() {
                                            loader_action_click();
                                        });
                                        
/*
                                        if ($('.sec-cl.card-fldr-out').length > 0) {
                                            var to_fldr = $('.sec-cl.card-fldr-out p').text();

                                            $("#loader_ex_fldr_choose>option").each(function() {
                                                if ($(this).text() == to_fldr) {
                                                    $(this).prop("selected", true);
                                                    return false;
                                                }
                                            });

                                            $("#loader_use_ex_fldr").prop("checked", true);
                                            $("#loader_use_new_fldr").prop("disabled", true);

                                            $("#loader_ex_fldr_choose").prop("disabled", true);
                                            $("#loader_new_fldr_name").prop("disabled", true);
                                        }
*/
                                        window.loaded_files = 0;            //do we need reload views after form hide?
                                        window.loader_items = new Array();  //recreate window.loader_items
                                        window.loader_in_process = 0;
                                        window.loader_must_stop = false;
                                        
                                        loader_refresh_buttons();
                                        
                                    })
                                    .on('shown.bs.modal', function () {
                                        // final height of modal body
                                        $(".modal-body").css("height", "calc(" + svh + "vh - " + 
                                                                (diff + $(".modal-header").outerHeight() + $(".modal-footer").outerHeight()) + "px)");

                                        window.history.pushState('forward', null, '#modal');  //'./#modal'
                                
                                        measure_lfi();
                                        
                                        
                                    })
                                    .on('hidden.bs.modal', function () {
                                        if (_common.ends_with(window.location.href, '#modal'))
                                            window.history.back();
                                        
                                        if (Array.isArray(window.loader_items) && window.loader_items.length > 0)   //clear window.loader_items
                                            window.loader_items.splice(0, window.loader_items.length);
                                        
                                        if (window.loaded_files > 0){

                                            if($('[type="radio"]:checked').attr('id') == 'loader_use_ex_fldr'){
                                                
                                                if($('#loader_ex_fldr_choose').val().length > 0)
                                                   get_docs_by_holder($('#loader_ex_fldr_choose').val());
                                                else 
                                                   load_folders();
                                               
                                            }
                                            else
                                                get_docs_by_holder($('#loader_new_fldr_name').val());
                                            
                                        }                                            
                                        
                                        $('#div_tmp').empty();
                                        
                                        
                                    })
                                    .modal('show');

                            }
                            else $('#div_tmp').empty();
            }
        });    
}

function measure_lfi() {
    if ($("#fm_loader.show").length > 0 && $("#loader_drop_container").length > 0) {
        var ldc_top = $("#loader_file_items").offset().top,
            mft_top = $(".modal-footer").offset().top;

        var ldc_h = mft_top - ldc_top - 10;

        if (ldc_h < 10) ldc_h = 10;

        $("#loader_file_items").css({ 'height': ldc_h + 'px' });
    }
}

function loader_process_ddEvent(eobj, e, bcolor) {
    e.stopPropagation();
    e.preventDefault();
    $(eobj).css('background', bcolor);
}

function loader_set_files_up(files) {
    if (typeof(files) === 'object' && files instanceof FileList) {
        
        var found, j;
        
        for (var i = 0; i < files.length; ++i)
            if (loader_isValidFileType(files[i].name)) {
                
                found = false;

                for (j = 0; j < window.loader_items.length; ++j)
                    if (files[i].name.toLowerCase() == window.loader_items[j].name.toLowerCase()) {
                        found = true;
                        break;
                    }
                
                if (!found) window.loader_items.push(files[i]);
            }

        if (window.loader_items.length > 0) {
            window.loader_items.sort(function(a, b) {
                var anm = a.name.toLowerCase(), bnm = b.name.toLowerCase();
                
                return anm.localeCompare(bnm);  // ie10 and safari support only 1-st parameter of localCompare
            });
        }
    }
    
    loader_refresh_table();
}

function loader_isValidFileType(filename) {
    var valids = '.pdf.txt.jpg.jpeg.png.doc.docx.xls.xlsx.ppt.pptx.rtf.', ext = _common.file_ext(filename);
    
    return ext.length > 0 && valids.indexOf('.' + ext + '.') >= 0;
}

function loader_refresh_table() {
    if (window.loader_items.length > 0) {
        var html_ = "<table id='ta_litems' class='table y-border-no'>" +
                        "<thead class='y-border-no'>" +
                            "<tr class='y-border-no' style='background:aqua;'>" +
                                "<th class='y-maxw-col-11 y-pad-lr5 h-0 m-0 p-0 y-border-no'></th>" +   // Empty headers (invisible header, but row width actual for data rows)
                                "<th class='y-wdt-col-1 h-0 m-0 p-0 y-border-no'></th>" +
                            "</tr>" +
                        "</thead>" +
                        "<tbody>";

        for (var k = 0; k < window.loader_items.length; ++k)
            html_ +=        "<tr id='tr_litem-" + k.toString() + "' class='y-border-no'>" +
                                "<td id='td_litem_name-" + k.toString() + "' class='y-border-b-only'>" +
                                     window.loader_items[k].name +
                                "</td>" +
                                "<td id='td_litem_acts-" + k.toString() + "' class='y-border-b-only'>" +
                                    "<a id='a_litem_del-" + k.toString() + "' href='javascript:;' class='y-mrg-lr10' data-toggle='tooltip' title='Удалить' data-delay='100' onclick='_loader.loader_start_del(this);'>" +
                                        "<img src='../nxcomm/img/delete_24.png'>" + 
                                    "</a>";
                                "</td>" +
                            "</tr>";
        
        html_ +=        "</tbody>" +
                    "</table>";
            
        $('#loader_file_items').html(html_);
    
        _common.refresh_tooltips();
    }
    else $('#loader_file_items').empty();
    
    loader_refresh_buttons();
}

function loader_start_del(e) {
    $(e).tooltip('hide');
   
    var indx = _common.value_fromElementID($(e).attr('id'));
    
    if (indx >= 0 && indx < window.loader_items.length) {
        window.loader_items.splice(indx, 1);
        
        if ($('#tr_litem-' + indx.toString()).length > 1)
            $('#tr_litem-' + indx.toString()).remove();
        else
            loader_refresh_table();
    }
}

function loader_refresh_buttons() {
    if (window.loader_items.length > 0) {
        if (window.loader_in_process > 0) {
            $("#loader_action").text('Стоп');

            $("#loader_close").removeClass('d-inline-block').addClass('d-none');
            $("[id^='a_litem_del-']").addClass('d-none');
        }
        else {
            $("#loader_action").text('Старт');

            $("#loader_close").removeClass('d-none').addClass('d-inline-block');
            $("[id^='a_litem_del-']").removeClass('d-none');
        }
    }
    else {
        $("#loader_action").text('Выход');  //.prop("disabled", false)

        $("#loader_close").removeClass('d-none').addClass('d-inline-block');
        $("[id^='a_litem_del-']").removeClass('d-none');
                                        
      //  if (window.loaded_files > 0)
             //  console.log('fff');
        //    _index.view_data_changed(window.loaded_orto);
    }
}

function loader_action_click() {
    if (window.loader_in_process > 0) {     // loading in process
        if (!window.loader_must_stop)
            window.loader_must_stop = true;
    }
    else {
        if (window.loader_items.length > 0) {
            if (loader_folder_ok())
                loader_start_upload();
            else loader_folder_err();
        }
        else $('#fm_loader').modal('hide');
    }
}

function loader_start_upload() {
    $.noty.closeAll();
    
    if (window.loader_in_process == 0) {
        // folder
        var fldr = '';
        
        if ($('#loader_use_ex_fldr').prop('checked'))
            fldr = $('#loader_ex_fldr_choose').val();
        else {
            var fnew = $('#loader_new_fldr_name').val().trim().toLowerCase(), fcmp, fnd = '';

            $("#loader_ex_fldr_choose > option").each(function() {
                fcmp = this.value.toLowerCase();

                if (fcmp.localeCompare(fnew) == 0) {
                    fnd = this.value;
                    return false;
                }
            });

            fldr = fnd.length > 0 ? fnd : $('#loader_new_fldr_name').val().trim();
        }

        if (fldr.length > 0 || $('#loader_use_ex_fldr').prop('checked'))
            loader_upload_top(fldr);
            loader_refresh_buttons();
    }
}

function loader_folder_ok() {
    return $('#loader_use_ex_fldr').prop('checked') || ($('#loader_new_fldr_name').val().trim().length > 0);
}

function loader_folder_err() {
    _common.say_noty_err('Не определена папка назначения');
}

function loader_upload_top(fldr) {
    if (window.loader_in_process == 0 && !window.loader_must_stop) {
        
        if (window.loader_items.length > 0) {
            if ($(".y-ajax-wait").css('visibility') == 'hidden')
                $(".y-ajax-wait").css('visibility', 'visible');
            
            ++window.loader_in_process;
            $("[id^='tr_litem-']:first").addClass('y-tmp-sel-row');
            
            loader_refresh_buttons();

            var reader = new FileReader();
            reader.onload = function(e) {
                var rdat_ = reader.result;

                switch (_common.file_ext(window.loader_items[0].name)) {
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                        {
                            var img = new Image();
                            img.onload = function() {   // start copy binary data to  img.src
                                if (img.height > $$_loader_max_photo_side_px_ || img.width > $$_loader_max_photo_side_px_) {
                                    var w, h;

                                    if (img.height > img.width) {
                                        h = $$_loader_max_photo_side_px_;
                                        w = img.width * ($$_loader_max_photo_side_px_ / img.height);
                                    }
                                    else {
                                        w = $$_loader_max_photo_side_px_;
                                        h = img.height * ($$_loader_max_photo_side_px_ / img.width);
                                    }

                                    var canvas = document.createElement("canvas");
                                    canvas.width  = w;
                                    canvas.height = h;
                                    canvas.getContext("2d").drawImage(img, 0, 0, w, h);

                                    rdat_ = canvas.toDataURL("image/" + (_common.file_ext(window.loader_items[0].name) == 'png' ? "png" : "jpeg"));

                                    loader_processed2DB(fldr, rdat_);
                                }
                                else loader_processed2DB(fldr, rdat_);
                            }
                            img.src = rdat_;
                        }
                        break;
                    default:
                        loader_processed2DB(fldr, rdat_);
                }
            }
            reader.onloadstart = function(e) {
            };
            reader.onerror = function() {
                if (window.loader_in_process > 0)
                    --window.loader_in_process;
                restart_loader(fldr);
            };
            reader.readAsDataURL(window.loader_items[0]);
        }
        else {
            if ($(".y-ajax-wait").css('visibility') == 'visible')
                $(".y-ajax-wait").css('visibility', 'hidden');
        }
    }
    else {
        window.loader_must_stop = false;
        
        loader_refresh_buttons();
        
        if ($(".y-ajax-wait").css('visibility') == 'visible')
            $(".y-ajax-wait").css('visibility', 'hidden');
    }
}

function loader_processed2DB(fldr, data_as_url) {
    
    if (data_as_url.length > 0) {
        var postForm = {
           'part' : 'add_document', 
           'pid': $("#fm_loader").attr("data-dep"),
           'flg': 1, // почему один не зню (в таблице се флг 1)
           'nm': '', // в таблице поле nm почему то пустое
           'holder' : fldr,
           'fnm'  : window.loader_items[0].name,
           'rdat' : data_as_url
        };
        
      //  console.log($("#fm_loader").attr("data-dep"));

        $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            complete: function() {
                restart_loader(fldr);
            },
            success   : function(data) {
                        if (data.success) {
                            window.loader_items.splice(0, 1);

                            ++window.loaded_files;

                            $("[id^='tr_litem-']:first").remove();

                            if ($("[id^='tr_litem-']").length == 0)
                                $("#ta_litems").remove();
                        }
                }
            });
    }
}

function restart_loader(fldr) {
    setTimeout(function(){
        --window.loader_in_process;
        loader_refresh_buttons();

        loader_upload_top(fldr);
    }, 1000);
}

/*
function add_doc(e){
    var tbl = 'depart';
    var flg = 1;
    var docs_pid = $(e).attr('data-dep');
    
    _common.close_dropdowns();
 
    _docget.fm_get_doc_startForm(tbl, docs_pid, ".pdf.doc.docx.xls.xlsx.rtf.txt.", 0, flg); // 0 name length, 1 - flag   
}

function docget_ok_click(data_tbl, rid_carr_pass, doc_nm, doc_flg){ 

    if (window.docget_file_up != null && rid_carr_pass.length > 0) {
        
     var reader = new FileReader();
        reader.onload = function(e) {              
        
            var postForm = {
                'part' : 'docs_add_rec',
                'tbl'  : data_tbl,
                'pid'  : rid_carr_pass,
                'fnm'  : window.docget_file_up.name,
                'nm'   : doc_nm,
                'doc_flg' : doc_flg,
                'rdat' : reader.result
            };
 

            $.ajax({
                type      : 'POST',
                url       : 'php/jgate.php',
                data      : postForm,
                dataType  : 'json',
                beforeSend: function() {
                    $(".y-ajax-wait").css('visibility', 'visible');
                },
                complete: function() {
                    $(".y-ajax-wait").css('visibility', 'hidden');
                },
                success   : function(data) {
                                if (data.success) {
                                   //_index.ofl_reloadDocSec(rid_carr_pass);
                                   _common.say_noty_ok("Документ загружен");
                                }
                                else _common.say_noty_err("Ошибка загрузки документа");
                                
                                load_page();
                }
            });
        }
        reader.readAsDataURL(window.docget_file_up);
     }
}
*/
function delete_doc_click(e){

    _common.stop_propagation(e);
    start_ynPopoverForDeleteX($(e));
}

function delete_doc(rid, elem){    

    var postForm = {
        'part': 'delete_doc',
        'rid': rid
    };

    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           if(data.success){
              if(elem.attr('data-holder').length == 0) 
                load_page();
            
              else get_docs_by_holder(elem);
           }   
        }
    });
}

function get_docs_by_holder(e){   

    if(typeof e == 'string')
       _common.storeSession('hldr', e);
    else
      _common.storeSession('hldr', $(e).attr('data-holder'));
  
 // console.log(_common.getStoredSessionStr('hldr'));
       
    var postForm = {
       'part'  : 'get_docs',
       'user_rid' : $('table').attr('data-user_rid'),
       'holder' : _common.getStoredSessionStr('hldr')  //$(e).parent().attr('data-holder')
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {

        },
        complete: function(){                      
                $('[src="img/arrow_back.png"]').hover(function(){
                    $(this).css({width: '120px', transition: 'width .5s'});
               
                
                $('[src="img/arrow_back.png"]').mouseleave(function(){
                    $(this).css({width: '100px', transition: 'width .5s'});
                });
            }); 
        },
        success: function(data) { 
                $('#back_arrow_tmp').html(data.arrow);
                $('#docs_body').html(data.docs);
                
              //  _common.storeSession('hldr', '');
        }
    });  
}

function doc_view_click(e){

    _common.close_dropdowns();
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    _viewer.viewer_view("file_put_tmp", "rid", $(e).attr('data-doc'), false);
}

function glob_fm_before_show() {
      // $("#div_ta_registr").css({ 'overflow-y': 'hidden' });  // IE can show scrollbar over modal
}

function glob_fm_after_show() {
         //   $("#div_ta_registr").css({ 'overflow-y': 'auto' });
}