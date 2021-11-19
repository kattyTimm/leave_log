        <div class="modal fade" id="edit_start_tm_fm" tabindex="-1" role="dialog" aria-labelledby="fm_clnf_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;"
             data-rid="" >
            <div class="modal-dialog modal-sm">
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

                            <div class="form-row">
                                <div class="col">
                                    <small><label for="start_time_edit">Убыл: </label></small>
                                    <input id="start_time_edit" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-12 y-fz08 text-primary">
                                    <span class="text-primary">Отредактируйте минуты в соответствии с нужным значением</span><br>
                                    <span class="text-danger">После согласования зам. директора возможность редактирования отсутствует!</span>
                                    <!--<span class="text-secondary">Поля "Убыл" и "Прибыл" должны быть в формате: HH:mm</span>-->
                                </div>
                            </div>  

                        </form>
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="start_tm_ok" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>