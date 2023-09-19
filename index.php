<?php
session_start();


function saveCookies(){
    $names = $_POST['names'] ? trim($_POST['names']) : '';
    $textColor = $_POST['textColor'] ? $_POST['textColor'] : '';
    $bgColor = $_POST['bgColor'] ? $_POST['bgColor'] : '';
    $shColor = $_POST['shColor'] ? $_POST['shColor'] : '';
    $image = $_POST['image'] ? $_POST['image'] : '';
    $shon = $_POST['shon'] ? true : false;
    $dbBG = $_POST['dbBG'] ? true : false;
    $fontFamily = $_POST['fontFamily'] ? $_POST['fontFamily'] : '';

    setcookie('names', $names, time() + (86400 * 30));
    setcookie('textColor', $textColor, time() + (86400 * 30));
    setcookie('bgColor', $bgColor, time() + (86400 * 30));
    setcookie('shColor', $shColor, time() + (86400 * 30));
    setcookie('image', $image, time() + (86400 * 30));
    setcookie('shon', $shon, time() + (86400 * 30));
    setcookie('dbBG', $dbBG, time() + (86400 * 30));
    setcookie('fontFamily', $fontFamily, time() + (86400 * 30));

    return true;
}

$bg_nums = [
        0 => "bez pozadia", 
        1 => "ružové ruže najružovejšie",
        2 => "ale fuj, zlatá",
        3 => "kvetinky",
        4 => "čierny nezmysel",
        5 => "eukaliptus taký hustejši",
        6 => "eukaliptus taký rozťahaný",
        7 => "papier",
    ];



//SPRACOVANIE
if(isset($_POST['submit'])){
    require_once __DIR__ . '/vendor/autoload.php';
    saveCookies();

  //MENA DO ARRAY()
  $names = explode(";", $_POST['names']);

  //URCENIE OBRAZKU
    $img = isset($_POST['image']) && !empty($_POST['image']) ? $_POST['image'] : 'bg_empty.png';
    if(isset($_POST['dbBG']) && $_POST['image']!='bg_empty.png'){
      $img = str_replace("1.png", "2.png", $img);
    }

     //DÁTA PRE JEDNOTLIVÉ FONTY     limin - max na riadku; lineHeigh - ratio vysky riadku (lineHeigh*fontSize);  fontSize - výška textu
      $fontsSets = [
        'pacifico' => [
                'limit' => 15,
                'lineHeigh' => 1.2,
                'fontSize'=>30,
              ],
         'parisienne' => [
                'limit' => 16,
                'lineHeigh' =>  1.2,
                'fontSize'=>35,
              ],
         'dancingscript' => [
                'limit' => 16,
                'lineHeigh' => 1.2,
                'fontSize'=>36,
              ],
         'symbola_hint' => [
                'limit' => 16,
                'lineHeigh' => 1.2,
                'fontSize'=>28,
              ],
         'patrickhand' => [
                'limit' => 16,
                'lineHeigh' => 1.2,
                'fontSize'=>35,
              ],
      ];

    // NASTAVENIA Z FORM
      $textColor = $_POST['textColor'];
      $bgColor = $_POST['bgColor'];
      $shColor = $_POST['shColor'];
      if(isset($_POST['shon'])){
          $shadow = 'text-shadow: 1px 1px 5px '.$shColor.';';
      }

      $posPoint = 262;        //PEVNÁ POZÍCIA V STREDE SPODNEJ ČASTI


      $fontFamily = isset($_POST['fontFamily'] ) ? $_POST['fontFamily'] : "pacifico";     //AK NIE JE UPRCENY TAK DEFAULT pacifico
      $fontsSet = $fontsSets[$fontFamily];                                                //NACITANIE NASTAVENIA FONTU

      $fontSize = $fontsSet['fontSize'];
      $lineHeigh = $fontsSet['lineHeigh']*$fontSize;



      $i=0;                           //PREMENA PRE PRIDAVANIE NOVYCH STRAN
      $value='';                      //PRAZDNA PREMENA PRE GENEROVANÝ OBSAH

  //CYKLUS NA VYTVORENIE MENOVIEK
    foreach ($names as $name) {
        $name = trim($name);
        $pos = $posPoint - $lineHeigh/2;
        if(strlen($name)>$fontsSet['limit']){
          $pos = $posPoint - $lineHeigh;
          $name=str_replace(" ", "<br>", $name);
        }


          $value.='<div class="bk">';
            $value.='<div class="textik" style="margin-top: '.$pos.'px; width:230px; '.$shadow.'">'.trim($name).'</div>';
          $value.='</div>';

          $i++;

           if($i==4){
             $value.='<pagebreak>';
             $i=0;
           }

      }
  //KONIEC CYKLU NA VYTVORENIE MENOVIEK

    $html = '';  //PRÁZDNA PREMENÁ NA VYTVORENIE MENOVIEK
    //VYTORENÉ MENOVKY:
    $html.='
      <!DOCTYPE html>
      <html>
      <head>
      <meta charset="UTF-8">
      <title>Menovky</title>
      <style>
        html {
          margin 0px;
          padding: 0px;
        }
        body {
          margin 0px;
          padding: 0px;
        }
        .bk {
          background-color: '.$bgColor.';
          background-image: url("./public/images/'.$img.'");
          background-repeat:no-repeat;
          background-size: 100% 100%;
          width: 330px;
          height:350px;
          text-align: center;
          float: left;
          margin: 0px 0x 25px 10px;
          border: 0.01em solid black;
       }
      .textik {
          margin: 0 auto;
          font-size:'.$fontSize.'px;
          line-height: '.$lineHeigh.'px;
          color: '.$textColor.';
          position:relative;
          text-align: center;
      }
      </style>
      </head>
        <body>
        '.$value.'
        <pre>
            <pagebreak>
            Počet vygenerovaných: '.count($names).'<br>
        </pre>
        </body>
      </html>';

    //KONIEC VYTVOREN=YCH MENOVIEK

  //NASTVAENIA LIBRARY mPDF
      $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
      $fontDirs = 'public/fonts';

      $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
      $fontData = $defaultFontConfig['fontdata'];
      $mpdf = new \Mpdf\Mpdf([
        'fontDir' => 'public/fonts',
        'fontdata' => [
            'pacifico' => [
                'R' => 'Pacifico-Regular.ttf'
            ],
            'parisienne' => [
                'R' => 'Parisienne-Regular.ttf'
            ],
            'dancingscript' => [
                  'R' => 'DancingScript-Bold.ttf'
              ],
            'symbola_hint' => [
                  'R' => 'Symbola_hint.ttf'
              ],
            'patrickhand' => [
                  'R' => 'PatrickHand-Regular.ttf'
              ]

        ],
        'default_font' => isset($_POST['fontFamily']) ? $_POST['fontFamily'] : 'pacifico' ,
        'setAutoTopMargin' => 'stretch',
        'setAutoBottomMargin' => 'stretch'
        ]);

      $mpdf->WriteHTML($html);
      $mpdf->Output();            //VYGENEROVANIE .pdf
    }
?>
   <!DOCTYPE html>
      <html>
      <head>
      <meta charset="UTF-8">
      <title>Menovky</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

      </head>
        <body>
        <div class="container">
            <h2>Menovky:</h2>
          <form action="" method="POST" target="_blank" >
            <div class="row mb-5">
              <div class="col-2">
                  <label class="form-label">Mená:</label>
                  <br>
                  <small>delené ";" napr.: Jožko Mrkvička; Ferko Mrkvička;</small>
              </div>
              <div class="col-10">
                <textarea name="names" class="form-control" style="height: 10em"><?= isset($_COOKIE["names"]) ? $_COOKIE["names"] : 'Jožko Mrkvička; Ferko Mrkvička; Aristoteles Unudený' ?></textarea>
              </div>
            </div>
            <div class="row form-group mb-5">
              <div class="col-2">
                  <label class="font-weight-bold">Farby:</label>
              </div>
              <div class="col-10">
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="textColor" class="form-label block">Písmo: </label>
                            <input type="color" id="textColor" name="textColor"  value="<?= isset($_COOKIE["textColor"]) ? $_COOKIE["textColor"] : '#000000' ?>" style="mt-3">
                        </div>
                        <div class="col-sm-3">
                            <label for="bgColor" class="form-label block">Pozadie: </label>
                            <input type="color" id="bgColor" name="bgColor"  value="<?= isset($_COOKIE["bgColor"]) ? $_COOKIE["bgColor"] : '#ffffff' ?>" style="mt-3">
                        </div>    
                        <div class="col-sm-3">
                            <label for="shColor" class="form-label block">Tieň písma: </label>
                              <input type="color" id="shColor" name="shColor"  value="<?= isset($_COOKIE["shColor"]) ? $_COOKIE["shColor"] : '#808080' ?>" style="mt-3">
                        </div>
                    </div>
                   
              </div>
           </div>
         <div class="row form-group mb-5">
              <div class="col-2">
                  <label class="form-label">Vzor:</label>
              </div>
              <div class="col-10">
                <div class="row g-3">
                  <?php foreach($bg_nums as $bg_i=>$bg_v) { ?>
                      <div class="col-md-3 fs-6" style="position:relative">
                        <div class="form-check border border-secondary" >
                              <input class="form-check-input" type="radio" name="image" id="image_bg<?= $bg_i ?>" value="bg<?= $bg_i ?>_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg<?= $bg_i ?>_1.png' ? 'checked' : '' ?>>
                              <label class="form-check-label" for="image_bg<?= $bg_i ?>">
                                <?= $bg_v ?>
                                <img src="./public/images/bg<?= $bg_i ?>_1.png" class="img-fluid">
                              </label>
                        </div>
                      </div>
                  <?php } ?>
               </div>
              </div>
           </div>
            <div class="row form-group">
              <div class="col-2">
                  <label class="form-label">Možnosti:</label>
              </div>
              <div class="col-sm-3">
          
                 <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="shon" name="shon" <?= isset($_COOKIE["shon"]) && $_COOKIE["shon"] == 1 ? 'checked' : '' ?>>
                      <label class="form-check-label" for="shon">
                        tieň písma
                      </label>
                    </div>
              </div>
              <div class="col-sm-3 mb-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="dbBG" name="dbBG" <?= isset($_COOKIE["dbBG"]) && $_COOKIE["dbBG"] == 1 ? 'checked' : '' ?>>
                      <label class="form-check-label" for="dbBG">
                        dvojstrané pozadie
                      </label>
                    </div>
              </div>
               <div class="col-sm-3">
                <label  class="form-label">Písmo: </label><br/>

                   <div class="form-check">
                      <input class="form-check-input" type="radio" name="fontFamily" id="fontFamily_1" value="pacifico" <?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'pacifico' ? 'checked' : '' ?>>
                      <label class="form-check-label" for="fontFamily_1" style="font-family: pacifico">
                        pacifico
                      </label>
                    </div>
                   <div class="form-check">
                      <input class="form-check-input" type="radio" name="fontFamily" id="fontFamily_2" value="parisienne" <?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'parisienne' ? 'checked' : '' ?>>
                      <label class="form-check-label" for="fontFamily_2" style="font-family: parisienne">
                        parisienne
                      </label>
                    </div>  
                    
                   <div class="form-check">
                      <input class="form-check-input" type="radio" name="fontFamily" id="fontFamily_3" value="dancingscript" <?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'dancingscript' ? 'checked' : '' ?>>
                      <label class="form-check-label" for="fontFamily_3" style="font-family: dancingscript">
                        dancingscript
                      </label>
                    </div> 
                   <div class="form-check">
                      <input class="form-check-input" type="radio" name="fontFamily" id="fontFamily_4" value="symbola_hint" <?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'symbola_hint' ? 'checked' : '' ?>>
                      <label class="form-check-label" for="fontFamily_4" style="font-family: symbola_hint">
                        symbola hint
                      </label>
                    </div> 
                    
                   <div class="form-check">
                      <input class="form-check-input" type="radio" name="fontFamily" id="fontFamily_5" value="patrickhand" <?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'patrickhand' ? 'checked' : '' ?>>
                      <label class="form-check-label" for="fontFamily_5" style="font-family: patrickhand">
                        patrickhand
                      </label>
                    </div> 
              </div>
           </div>
              <div class="row form-group">
              <div class="col-4"><button name="submit" class="btn btn-primary"> generovať </button>
            </div>
            </div>
          </form>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

      </body>
</html>