        <div class="modal fade" id="employee_depart_deteils" tabindex="-1" role="dialog" aria-hidden="true" ondrop="return false;" ondragover="return false;"
             data-rid="" data-ffio="" data-date="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="bg-light y-pad-a10 y-border-b rounded-top">
                        <div class="modal-title w-100 p-0"> <!-- w-100 p-0-->
                           <div class="y-flex-row-nowrap p-0 align-items-center"> 
                                <h4 id="fm_clnf_ttl" class="y-dgray-text">Детальная информация <small><i id="fm_clnf_ttl_add" class="y-dgray-text"></i></small></h4>
                                <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz15">&times;</a>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body">
                        <div class="y-pad-a10 border-dotted">
                            <span class="fz-18 text-primary">Общее количество уходов - </span> 
                            <span id="total_leave_quantity" class="y-gray-text">{total_leave_quantity}</span> 
                        </div>  
                        
                        <div class="y-pad-a10">
                            <span class="fz-18 text-primary">Суммарное время отсутсвия - </span> 
                            <span id="total_absence_time" class="y-gray-text">{total_absence_time}</span> 
                        </div>     
                        
                    </div>
                    <div class="modal-footer bg-light">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="close_deteils_ok" class="btn btn-dark y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>