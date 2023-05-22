<?php
session_start();


function saveCookies(){
    $names = $_POST['names'] ? trim($_POST['names']) : '';
    $textColor = $_POST['textColor'] ? $_POST['textColor'] : '';
    $bgColor = $_POST['bgColor'] ? $_POST['bgColor'] : '';
    $shColor = $_POST['shColor'] ? $_POST['shColor'] : '';
    $image = $_POST['image'] ? $_POST['image'] : '';
    $shon = $_POST['shon']=='on' ? true : false;
    $dbBG = $_POST['dbBG']=='on' ? true : false;
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
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
      </head>
        <body>
        <div class="container">
            <h2>Menovky:</h2>
          <form action="" method="POST" target="_blank" >
            <div class="row">
              <div class="col-1">
                  <label class="font-weight-bold">Mená:</label>
                  <br>
                  <small>delené ";" napr.: Jožko Mrkvička; Ferko Mrkvička;</small>
              </div>
              <div class="col-9">
                <textarea name="names" style="height: 15em; min-height:12em; width:750px"><?= isset($_COOKIE["names"]) ? $_COOKIE["names"] : 'Jožko Mrkvička; Ferko Mrkvička; Aristoteles Unudený' ?></textarea>
              </div>
            </div>
            <div class="row form-group">
              <div class="col-1">
                  <label class="font-weight-bold">Farby:</label>
              </div>
              <div class="col-2">
                   <label for="textColor">Písmo: </label>
                  <input type="color" id="textColor" name="textColor"  value="<?= isset($_COOKIE["textColor"]) ? $_COOKIE["textColor"] : '#000000' ?>" style="mt-3">
              </div>
             <div class="col-2">
                   <label for="bgColor">Pozadie: </label>
                  <input type="color" id="bgColor" name="bgColor"  value="<?= isset($_COOKIE["bgColor"]) ? $_COOKIE["bgColor"] : '#ffffff' ?>" style="mt-3">
              </div>
             <div class="col-2">
                   <label for="shColor">Tieň písma: </label>
                  <input type="color" id="shColor" name="shColor"  value="<?= isset($_COOKIE["bgColor"]) ? $_COOKIE["bgColor"] : '#808080' ?>" style="mt-3">
              </div>
           </div>
         <div class="row form-group">
              <div class="col-1">
                  <label class="font-weight-bold">Vzor:</label>
              </div>
              <div class="col-2">
                <label style="border:1px solid black; width: 150px" for="image_bg">
                  <input type="radio" name="image" id="image_bg" value="bg_empty.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg_empty.png' ? 'checked' : '' ?>>prázdne<br>
                  <img src="./public/images/bg_empty.png" style="width: 150px ">
                </label>
              </div>
              <div class="col-2">
                <label style="border:1px solid black;" for="image_bg1">
                  <input type="radio" name="image" id="image_bg1" value="bg1_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg1_1.png' ? 'checked' : '' ?>>ružičky<br>
                  <img src="./public/images/bg1_1.png" style="width: 150px ">
                </label>
              </div>
              <div class="col-2">
                <label style="border:1px solid black;" for="image_bg2">
                  <input type="radio" name="image" id="image_bg2" value="bg2_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg2_1.png' ? 'checked' : '' ?>>zlate a skarede<br>
                  <img src="./public/images/bg2_1.png" style="width: 150px ">
                </label>
              </div>
               <div class="col-2">
                <label style="border:1px solid black;" for="image_bg3">
                  <input type="radio" name="image" id="image_bg3" value="bg3_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg3_1.png' ? 'checked' : '' ?>>zase kvety...<br>
                  <img src="./public/images/bg3_1.png" style="width: 150px ">
                </label>
              </div>
              <div class="col-2">
                <label style="border:1px solid black;" for="image_bg4">
                  <input type="radio" name="image" id="image_bg4" value="bg4_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg4_1.png' ? 'checked' : '' ?>>nejake cb<br>
                  <img src="./public/images/bg4_1.png" style="width: 150px ">
                </label>
              </div>
              <div class="col-2">
                <label style="border:1px solid black;" for="image_bg5">
                  <input type="radio" name="image" id="image_bg5" value="bg5_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg5_1.png' ? 'checked' : '' ?>>eukalyptus 1<br>
                  <img src="./public/images/bg5_1.png" style="width: 150px ">
                </label>
              </div>
              <div class="col-2">
                <label style="border:1px solid black;" for="image_bg6">
                  <input type="radio" name="image" id="image_bg6" value="bg4_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg6_1.png' ? 'checked' : '' ?>>eukalyptus 2<br>
                  <img src="./public/images/bg6_1.png" style="width: 150px ">
                </label>
              </div>
              <div class="col-2">
                <label style="border:1px solid black;" for="image_bg7">
                  <input type="radio" name="image" id="image_bg7" value="bg7_1.png" <?= isset($_COOKIE["image"]) && $_COOKIE["image"] == 'bg7_1.png' ? 'checked' : '' ?>>papier<br>
                  <img src="./public/images/bg7_1.png" style="width: 150px ">
                </label>
              </div>
           </div>
            <div class="row form-group">
              <div class="col-1">
                  <label class="font-weight-bold">Možnosti:</label>
              </div>
              <div class="col-2" style="text-align: center">
                  <input type="checkbox" name="shon" id="shon" <?= isset($_COOKIE["shon"]) && $_COOKIE["shon"] == 1 ? 'checked' : '' ?>><br>
                  <label  class="" for="shon">tieň písma</label>
              </div>
              <div class="col-2" style="text-align: center">
                  <input type="checkbox" name="dbBG" id="dbBG" <?= isset($_COOKIE["dbBG"]) && $_COOKIE["dbBG"] == 1 ? 'checked' : '' ?>><br>
                  <label  class="" for="dbBG">dvojstrané pozadie</label>
              </div>
               <div class="col-2" style="text-align: center">
                  <label  class="">Písmo: </label><br/>
                  <label  class="" for="fontFamily_1" style="font-family: pacifico"><input type="radio" name="fontFamily" id="fontFamily_1" value="pacifico" <?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'pacifico' ? 'checked' : '' ?>> pacifico</label><br/>
                  <label  class="" for="fontFamily_2" style="font-family: parisienne"><input type="radio" name="fontFamily" id="fontFamily_2" value="parisienne"<?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'parisienne' ? 'checked' : '' ?>> parisienne</label><br/>
                  <label  class="" for="fontFamily_3" style="font-family: dancingscript"><input type="radio" name="fontFamily" id="fontFamily_3" value="dancingscript"<?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'dancingscript' ? 'checked' : '' ?>> dancingscript</label><br/>
                  <label  class="" for="fontFamily_4" style="font-family: symbola_hint"><input type="radio" name="fontFamily" id="fontFamily_4" value="symbola_hint"<?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'symbola_hint' ? 'checked' : '' ?>> symbola_hint</label><br/>
                  <label  class="" for="fontFamily_5" style="font-family: patrickhand"><input type="radio" name="fontFamily" id="fontFamily_5" value="patrickhand"<?= $_COOKIE["fontFamily"] && $_COOKIE["fontFamily"] == 'patrickhand' ? 'checked' : '' ?>> Patrick Hand</label><br/>
              </div>
           </div>
              <div class="row form-group">
              <div class="col-4"><button name="submit" class="btn btn-primary"> generovať </button>
            </div>
            </div>
          </form>

      </body>
</html>
