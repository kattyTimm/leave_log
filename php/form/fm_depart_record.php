        <div class="modal fade" id="fm_depart_record" tabindex="-1" role="dialog" aria-labelledby="fm_clnf_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;"
             data-rid="" data-flg="" data-ffio="" data-pos="" data-date="">
            <div class="modal-dialog">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_clnf_ttl" class="y-dgray-text">Журнал ухода. <small><i id="fm_clnf_ttl_add" class="y-dgray-text"></i></small></h4>
                                <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz15">&times;</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form role="form" autocomplete="off" onsubmit="return false">

                            <div class="form-group">
                                <label for="org_nm">Наименование организации:</label>
                                <input id="org_nm" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                            </div>
                            
                            
                            <div class="form-group">
                                <label for="leave_date">Дата:</label>
                                <input id="leave_date" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                            </div>
                            
                            <div class="form-row y-mrg-b10">                               
                              
                                <div class="col-6">
                                    <label for="depart_time">Убыл: </label>
                                    <input id="depart_time" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>

                                <div class="col-6">
                                    <small><label for="back_time">Прибыл: </label></small>
                                    <input id="back_time" class="form-control timepicker" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                
                                <div class="col-12 y-fz08 text-primary">
                                    <span class="text-primary">Отредактируйте минуты в соответствии с нужным значением</span><br>
                                    <!--<span class="text-secondary">Поля "Убыл" и "Прибыл" должны быть в формате: HH:mm</span>-->
                                </div>
                            </div>  
                            
                            <div class="form-group">
                                <small><label for="remark">Примечание: </label></small>
                                <input id="remark" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                            </div>                
                            
                        </form>
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="depart_record_ok" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>