<div class="modal fade" id="fm_loader" tabindex="-1" role="dialog" aria-labelledby="fm_loader_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;" data-dep="" data-holder=""
            data-backdrop="static" data-keyboard="false">   <!-- Prevent close by click outside or by ESC press -->       <!-- data-orto="" -->
            <div class="modal-dialog modal-lg">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_loader_ttl" class="y-dgray-text">Документы. <small><i class="y-steel-blue-text">Загрузка</i></small></h4>
                                <a id="loader_close" data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz15">&times;</a>
                            </div>
                         <!--   <div><small class="y-llgray-text">Секция:</small> <span id="loader_orgs_to_snm" class="y-dred-text"></span></div> -->
                        </div>
                    </div>
                    <div class="modal-body" style="overflow:hidden;">    <!-- height:calc(100vh - 250px); -->
                        <form role="form" autocomplete="off" onsubmit="return false">
                            
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="loader_use_ex_fldr" name="loader_folder">
                                    <label class="custom-control-label" for="loader_use_ex_fldr">Загрузить в существующую папку:</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <select class='custom-select d-inline-block m-0' id='loader_ex_fldr_choose'>
                                {loader_ex_fldr_choose:options}
                                </select>
                            </div>                                                        
                            
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="loader_use_new_fldr" name="loader_folder">
                                    <label class="custom-control-label" for="loader_use_new_fldr">Создать новую папку:</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="loader_nm">Наименование* :</label>
                                <input id="loader_new_fldr_name" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                            </div>
                            
                            <br>
                            
                            <div>
                                <label class="btn btn-info btn-sm y-shad" for="loader_file_selector">
                                    <input id="loader_file_selector" type="file" class="d-none" multiple>
                                    Выберите файлы
                                </label>
                            </div>
                            <div id="loader_drop_container" class="y-drop-container">
                                <div id="loader_drop_area" class="y-drop-area">
                                    <span id="loader_drop_text" class="y-drop-text">или перетащите сюда</span>
                                </div>
                            </div>

                            <br>
                            
                            <div id="loader_file_items" style="border:solid 1px #EEE;overflow-y:auto;min-height:10px;" >
                            </div>
                            
                        </form>
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id='dlg_err' class='y-modal-err y-err-text y-info-label'></p>
                        <button id="loader_action" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>
