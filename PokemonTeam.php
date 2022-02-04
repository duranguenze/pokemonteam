<?php
    if(isset($_REQUEST['FechaNacimiento'])){
        ob_start();
        $urlApi="https://pokeapi.co/api/v2/pokemon/";
        $PokemonListado=[];
        $Fecha=strtotime($_REQUEST['FechaNacimiento']);
        putenv('GDFONTPATH=' . realpath('.'));
        $Fuente='./Pokemon Solid.ttf';
        $FuenteHoll='./Pokemon Hollow.ttf';
        ini_set('memory_limit', '256M');

        array_push($PokemonListado, ltrim(date("m", $Fecha),'0'));
        array_push($PokemonListado, ltrim(date("d", $Fecha),'0'));
        array_push($PokemonListado, ltrim(date("y", $Fecha),'0'));
        
        $W=96*3;
        $H=110;
        $Px=0;
        $margen=10;
        $imgBase=imagecreatetruecolor($W+$margen+2,$H);
        imagesavealpha($imgBase,true);
        $rojo = imagecolorallocate($imgBase, 0xFF, 0x00, 0x00);
        $negro = imagecolorallocate($imgBase, 0x00, 0x00, 0x00);
        $blanco = imagecolorallocate($imgBase, 0xFF, 0xFF, 0xFF);
        $azul = imagecolorallocate($imgBase, 0x00, 0x00, 0xFF);
        $amarillo = imagecolorallocate($imgBase, 0xFF, 0xE9, 0x00);
        $transp = imagecolorallocatealpha($imgBase, 255, 0, 0, 127);
        imagefill($imgBase,0,0,$transp);
        foreach ($PokemonListado as $indice=>$PokemonID) {
            $PokemonListado[$indice]=json_decode(file_get_contents($urlApi.$PokemonID));
            list($ancho, $alto, $tipo, $atributos) =getimagesize($PokemonListado[$indice]->sprites->front_default);
            if($ancho>($W-$Px)){
                $imgX=imagecreatetruecolor($W+$ancho+$margen,$H);
                imagesavealpha($imgX,true);
                imagefill($imgX,0,0,$transp);
                imagecopy($imgX, $imgBase, 0, 0, 0, 0, $W, $H);
                $W+=$ancho+$margen;
                ImageDestroy($imgBase);
                $imgBase=$imgX;
            }
            
            $img=imagecreatefrompng($PokemonListado[$indice]->sprites->front_default);
            imagecopymerge($imgBase, $img, $Px+$margen, 0, 0, 0, $ancho, $alto, 100);
            ImageDestroy($img);

            $Posicion = imagefttext($imgBase, 14, 5, $Px+$margen, $alto, $amarillo, $Fuente, ucfirst($PokemonListado[$indice]->name));
            $Posicion = imagefttext($imgBase, 14, 5, $Px+$margen, $alto, $azul, $FuenteHoll, ucfirst($PokemonListado[$indice]->name));
            $Px+=$ancho;
        }
        ob_clean();
        imagepng($imgBase);
        $ArchivoTemporal=ob_get_clean();
        ImageDestroy($imgBase);
        ob_end_clean();
        if(!isset($_REQUEST['Imagen'])){
            echo '<img src="';
            echo 'data:image/png;base64,'.base64_encode($ArchivoTemporal);
            echo '" >';
        }else{
            header('Content-Type: image/png');
            echo $ArchivoTemporal;
        }
    }else{
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Equipo Pok&eacute;mon</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    </head>
    <body>
        <form method="get">
            <div class="container">
                <div class="alert alert-primary" role="alert">
                    Escribe tu fecha de nacimiento para generar tu equipo Pok&eacute;mon
                </div>
                <div class="form-group">
              <div class="row">
                <div class="col-12">
                </div>
                <div class="col-sm">
                    <input type="date" name="FechaNacimiento" value="" placeholder="Fecha nacimiento"> 
                </div>
                <div class="col-sm">
                    <button type="submit" class="btn btn-primary">Ver Equipo</button>
                    <button type="submit" class="btn btn-primary" name="imagen" value="1">Ver Equipo Imagen</button>
                </div>
              </div>
            </div>
        </form>
    </body>
</html>
<?php }