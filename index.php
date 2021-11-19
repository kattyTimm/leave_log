<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Журнал ухода</title>
        
        <link rel="shortcut icon" type="image/png" href="../orgcomm/img/rgddiary_32.png">
        <!--link href="../orgcomm/css/bootstrap.min.css" rel="stylesheet">-->
        
        <link rel="stylesheet" href="../orgcomm/css/bootstrap.min.css">
        <link href="../orgcomm/css/bootstrap-datepicker3.css" rel="stylesheet">
        <link href="../orgcomm/css/jquery.timepicker.css" rel="stylesheet">
        <link href="/pktbbase/css/_indx_comm.css" rel="stylesheet"/> 
        <link href="/pktbbase/css/_indx_srch.css" rel="stylesheet"/> 
        <link href="/pktbbase/css/_indx_bbmon.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_root.css" rel="stylesheet"/>
        <link href="/pktbbase/css/colorbox.css" rel="stylesheet"/>
        <link href="/pktbbase/css/mprogress-gr.css" rel="stylesheet">
        
        <link href="/pktbbase/css/_indx_sout.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_drag.css" rel="stylesheet"/> <!--чтобы работал драг и дроп в форме-->
        
        <link rel="stylesheet" href="css/styles.css">
        <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
      <?php
        include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
        include_once 'php/assist.php';
        
        $current_browser = get_browser(null, true);
        if (strcasecmp($current_browser['browser'], 'ie') == 0 && intval($current_browser['majorver']) < 10) {
            echo "<div class='text-center align-middle' style='padding:20px;color:white;background:red;line-height:40px;'>Версии браузера Internet Explorer ниже 10 не поддерживаются!</div>";
            exit();
        }
      ?>

        <header class="navbar navbar-brand">
            <div id="header_content">            
                <img src="img/transparent_train_512.png" widht="70" height="70">
                
                <h4><i>Журнал ухода</i></h4>
            </div>
        </header>
 
        <div id="bg_tmp">
            <img src="img/bg_1920.jpg">
        </div>  
       <div id="content">   
         <main id="main">

             <div id="fio-container" class="k-row blue-900">
                 <div class="fio-container-tmp">                   
                   <div id="btn_tmp"></div>  
                   <div id="ffio"></div>
                   <div class="">
                      <input id="srch_box" type="text" placeholder="Поиск <min 2 символа>..." ondrop="return false;" ondragover="return false;" class="form-control"> 
                   </div>
                 </div>
             </div>
             
             <div id="checkbox_tmp">
                <!-- <input id="only_unChecked" type="checkbox"> -->
             </div>  
             
             <div id="conteiner">
                 
                            <?php 
                           // include_once 'php/ip.php';

                          /*
                                     function makecoffee($types = array("капучино"), $coffeeMaker = NULL)
                                     {
                                         // $coffeeMaker = NULL это true

                                         $device = is_null($coffeeMaker) ? "вручную" : $coffeeMaker;
                                         echo($device).'</br>';
                                         return "Готовлю чашку ".join(", ", $types)." $device.\n";
                                     }
                                     echo makecoffee().'</br>';
                                     echo makecoffee(array("капучино", "лавацца"), "в чайнике");

                           */
                            /*
                             function get_currentClientIP() {
                                 if (isset($_SERVER['REMOTE_ADDR']))
                                     return $_SERVER['REMOTE_ADDR'];
                                 else {  // this operators returns internet-address (172...). Bad for debug.
                                     $name = gethostname();
                                     return is_string($name) ? gethostbyname($name) : '';
                                 }
                             }*/
                            ?>
               </div> 
             
        <!--      <div id="test">
                  <img src="img/departure-platform_1280.jpg">
             </div>    -->
             
             <div id="pagination"></div>
             
             <div id="ip-container">                 
                   <div class="k-row"  id="ip"></div>
             </div>
             
          </main>
           
           <div id="samples">
               <div id="docs_header_tmp" class="k-row blue-900">
                   <div id="btn_tmp_docs"></div>  
                   <div id="docs_title"></div> 
               </div> 
               
               <div id="back_arrow_tmp"></div>
               
               <div id="docs_body"></div>
           </div> 
    </div>

        

        <footer class="sticky-footer"> 
            <div id="user_ip"></div>
            <p class="p-0">ПКТБ Л 2020..2021</p>
        </footer>

        <div id="div_tmp"></div>
        <div id="div_tmpx"></div>
        
        <a id='a_download' href='javascript:;' class='d-none' download></a>

        <script src="../orgcomm/js/jquery.min.js"></script>  

        <script src="../orgcomm/js/bootstrap.bundle.min.js"></script>
        <script src="/pktbbase/js/jquery.colorbox-min.js"></script>
        <script src="/pktbbase/js/mprogress.min.js"></script>

        <script src="../orgcomm/js/jquery.noty.packaged.min.js"></script>                
        <script src="../orgcomm/js/jquery.noty.packaged.min.js"></script>

        <script src="../orgcomm/js/purl.min.js"></script>         
        <script src="../Org/js/jquery.autocomplete.corrected-me.min.js"></script>

        <script src="../orgcomm/js/jquery.timepicker.min.js"></script>

        <script src="../orgcomm/js/bootstrap-datepicker.min.js"></script>
        <script src="../orgcomm/js/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>

        <script src="js/main.js"></script> 
  </body>
</html>
