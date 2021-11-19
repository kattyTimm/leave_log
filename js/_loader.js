var _loader = {   // interface to _loader.js
    start_loader:             function (orgs_to)           { start_loader(orgs_to); },
    
    measure_lfi:              function ()                  { measure_lfi(); },
    
    loader_isValidFileType:   function (filename)          { return loader_isValidFileType(filename); },
    
    loader_start_del:         function (e)                 { loader_start_del(e); },
}

var $$_loader_max_photo_side_px_ = 1280;

function start_loader(orgs_to) {
    if (_index.is_valid_user() && _index.app_orgs().length > 0) {   // CL can't load anything
        var postForm = {
           'part' : 'get_fm_loader',
           'orto' : orgs_to,
           'orfr' : _index.app_orgs(),
           'dt'   : _index.app_dt(),
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

                                // drag
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

                                $('#fm_loader')
                                    .on('show.bs.modal', function () {
                                        $(".modal-content").css("height", "calc(" + svh + "vh - " + diff + "px)");
                                        // pre-set small height of modal body. In on shown will open it up. (Else - will twitch(дергаться)
                                        $(".modal-body").css("height", "100%");
                                    
                                        $('#loader_use_ex_fldr').prop('checked', true);
                                        
                                        $('#loader_action').unbind('click').on('click',function() {
                                            loader_action_click();
                                        });
                                        
                                        var orgs_to_snm;
                                        
                                        if (orgs_to.length == 0) {
                                            orgs_to_snm = 'ЦЛ';

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
                                        }
                                        else {
                                            var snm_now = _index.app_orgsnm(), snm_to = _index.app_orgs_to_snm();
                                            
                                            if (snm_now == snm_to) {
                                                orgs_to_snm = snm_to;

                                            if ($('.sec-own.card-fldr-out').length > 0) {
                                                var to_fldr = $('.sec-own.card-fldr-out p').text();
                                                
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
                                            }
                                            else {
                                                orgs_to_snm = "<span class='y-lgray-text'>" + snm_now + " ... </span>" + snm_to;
    
                                                var snm_now_found = false;
                                                $("#loader_ex_fldr_choose>option").each(function() {
                                                    if ($(this).text() == snm_now) {
                                                        $(this).prop("selected", true);
                                                        snm_now_found = true;
                                                        return false;
                                                    }
                                                });
                                                
                                                if (snm_now_found) {
                                                    $("#loader_use_ex_fldr").prop("checked", true);
                                                    $("#loader_use_new_fldr").prop("disabled", true);
                                                }
                                                else {
                                                    $("#loader_use_ex_fldr").prop("disabled", true);
                                                    $("#loader_use_new_fldr").prop("checked", true);
                                                    $("#loader_new_fldr_name").val(snm_now);
                                                }
                                                
                                                $("#loader_ex_fldr_choose").prop("disabled", true);
                                                $("#loader_new_fldr_name").prop("disabled", true);
                                                
                                                //_common.say_noty_warn($("#loader_new_fldr_name").text());
                                            }
                                        }
                                        
                                        $('#loader_orgs_to_snm').html(orgs_to_snm);
                                        
                                        //$('#loader_orgs_to_snm').text(orgs_to.length == 0 ? 'ЦЛ' : _index.app_orgs_to_snm());
                                        
                                        window.loaded_orto = orgs_to;
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
                                        
                                        $('#div_tmp').empty();
                                        
                                        if (window.loaded_files > 0)
                                            _index.view_data_changed(window.loaded_orto);
                                    })
                                    .modal('show');
                            }
                            else $('#div_tmp').empty();
                        }
        });
    }
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
                                        
        if (window.loaded_files > 0)       
              _index.view_data_changed(window.loaded_orto);
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
           'part' : 'dsrc_check_rec',
           'orto' : window.loaded_orto,
           'orfr' : _index.app_orgs(),
           'fldr' : fldr,
           'fnm'  : window.loader_items[0].name,
           'dt'   : _index.app_dt(),
           'rdat' : data_as_url
        };

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


