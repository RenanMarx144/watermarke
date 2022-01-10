<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Description of Watermark
 *
 * @author Renan
 */
class Watermark
{
    function __construct()
    {
    }
    public function resize($arr)
    {

        //$img_main, $main_type, $new_name, $m_height = 0, $m_width = 0, $watermark = false, $watermark_type = false, $w_height = 0, $w_width = 0, $positionX = 0, $positionY = 0, $opacity = 70, $quality = 90
        if (isset($arr['img_main'])) {
            # code...
            $img_main = $arr['img_main'];
        } else {
            return 'Precisa do local do arquivo';
        }
        if (isset($arr['main_type'])) {
            $main_type = $arr['main_type'];
        } else {
            return 'precisa mencionar o tipo do arquivo';
        }
        if (isset($arr['new_name'])) {
            $new_name = $arr['new_name'];
        } else {
            return 'é nescessario nome do arquivo';
        }
        if (isset($arr['location'])) {
            $location = $arr['location'];
        } else {
            return 'informe o local que deseja salvar o arquivo';
        }
        $m_height =  isset($arr['m_height']) && $arr['m_height'] > 0 ? $arr['m_height'] : 0;
        $m_width = isset($arr['m_width']) && $arr['m_width'] > 0 ? $arr['m_width'] : 0;
        $watermark = isset($arr['watermark']) ? $arr['watermark'] : false;
        $watermark_type = isset($arr['watermark_type']) ? $arr['watermark_type'] : false;
        $w_height = isset($arr['w_height']) && $arr['w_height'] > 0 ? $arr['w_height'] : 0;
        $w_width = isset($arr['w_width']) && $arr['w_width'] > 0 ? $arr['w_width'] : 0;
        $positionX = isset($arr['positionX']) && $arr['positionX'] > 0 ? $arr['positionX'] : 0;
        $positionY = isset($arr['positionY']) && $arr['positionY'] > 0 ? $arr['positionY'] : 0;
        $opacity = isset($arr['opacity']) && $arr['opacity'] > 0 ? $arr['opacity'] : 70;
        $quality = isset($arr['quality']) && $arr['quality'] > 0 ? $arr['quality'] : 90;
        $margin = isset($arr['margin']) && $arr['margin'] > 0 && $arr['margin'] != '' ? $arr['margin'] : 5;        
        $positionFixed = isset($arr['positionFixed']) && $arr['positionFixed'] != '' && $arr['positionFixed'] != '-'? $arr['positionFixed'] : false;
        
        if ($watermark) {
            if (!$watermark_type) {
                return 'é nescessario nome do tipo de arquivo';
            }
            // ----------------------------------------
            // get image watermark                    |
            // ----------------------------------------
            if ($watermark_type == 'image/jpeg') {
                $watermark = imagecreatefromjpeg($watermark);
            } elseif ($watermark_type == 'image/pjpeg') {
                $watermark = imagecreatefromjpeg($watermark);
            } elseif ($watermark_type == 'image/png') {
                $watermark = imagecreatefrompng($watermark);
            } elseif ($watermark_type == 'image/gif') {
                $watermark = imagecreatefromgif($watermark);
            } else {
                return 'tipo de arquivo invalido';
            }

            // ----------------------------------------
            // get exist difernt size                  |
            // ----------------------------------------
            $redmi_watermaker = $w_height == 0 && $w_width == 0 ? false : true;

            // ----------------------------------------
            // origin marcadagua                      |
            // ----------------------------------------
            $origin_width = imagesx($watermark);
            $origin_height = imagesy($watermark);

            // ----------------------------------------
            // redmensionamento marcadagua            |
            // ----------------------------------------
            if ($redmi_watermaker) {
                $nova_largura = $w_width != 0 ? $w_width : floor(($origin_width / $origin_height) * $w_height);
                $nova_altura = $w_height != 0 ? $w_height : floor(($origin_height / $origin_width) * $w_width);

                $watermark_rdm = imagecreatetruecolor($nova_largura, $nova_altura);
                imagecopyresampled($watermark_rdm, $watermark, 0, 0, 0, 0, $nova_largura, $nova_altura, $origin_width, $origin_height);

                $define_w_width = imagesx($watermark_rdm);
                $define_w_height = imagesy($watermark_rdm);
            } else {
                $watermark_rdm = $watermark;
                $define_w_width = imagesx($watermark_rdm);
                $define_w_height = imagesy($watermark_rdm);
            }

            // ----------------------------------------
            // define size image main                 |
            // ----------------------------------------
            $img_main_rdm = $this->resizing($img_main, $main_type, $m_height, $m_width);
            if ($positionFixed) {
                $forFixedPositionX = imagesx($img_main_rdm);
                $forFixedPositionY = imagesy($img_main_rdm);
                $arrPositionFixed = [
                    'top-left'   => [$x = $margin, $y = $margin],
                    'top-center' => [$x = ($forFixedPositionX / 2) - ($define_w_width / 2), $y = $margin],
                    'top-right' => [$x =  $forFixedPositionX - $define_w_width - $margin, $y = $margin],
                    'left' => [$x = $margin, $y = ($forFixedPositionY / 2) - ($define_w_height / 2)],
                    'center' => [$x = ($forFixedPositionX / 2) - ($define_w_width / 2), $y = ($forFixedPositionY / 2) - ($define_w_height / 2)],
                    'rigth' => [$x = $forFixedPositionX - $define_w_width - $margin, $y = ($forFixedPositionY / 2) - ($define_w_height / 2)],
                    'bottom-left'   => [$x = $margin, $y = $forFixedPositionY - $define_w_height - $margin],
                    'bottom-center' => [$x = ($forFixedPositionX / 2) - ($define_w_width / 2), $y = $forFixedPositionY - $define_w_height - $margin],
                    'bottom-right' => [$x =  $forFixedPositionX - $define_w_width - $margin, $y = $forFixedPositionY - $define_w_height - $margin],
                ];
                if (isset($arrPositionFixed[$positionFixed])) {
                    $positionX = $arrPositionFixed[$positionFixed][0];
                    $positionY = $arrPositionFixed[$positionFixed][1];
                }else {
                    return 'erro na escolha do parametro: "positionFixed" verifique a sintex -> '. $positionFixed;
                }
            }
            
            imagecopymerge($img_main_rdm, $watermark_rdm,  $positionX,  $positionY, 0, 0, $define_w_width, $define_w_height, $opacity);
            imagejpeg($img_main_rdm, $location . $new_name, $quality);
            $result = $location .  $new_name;
            return $result;
        } else {
            $img_main_rdm = $this->resizing($img_main, $main_type, $new_name, $m_height, $m_width);
            imagejpeg($img_main_rdm, $location . $new_name, $quality);
            $result = $location .  $new_name;
            return $result;
        }
    }

    public function resizing($img_main, $main_type, $m_height, $m_width)
    {
        $redmi_mainImg = $m_height == 0 && $m_width == 0 ? false : true;

        if ($main_type == 'image/jpeg') {
            $img_main = imagecreatefromjpeg($img_main);
        }
        if ($main_type == 'image/pjpeg') {
            $img_main = imagecreatefromjpeg($img_main);
        }
        if ($main_type == 'image/png') {
            $img_main = imagecreatefrompng($img_main);
        }
        if ($main_type == 'image/gif') {
            $img_main = imagecreatefromgif($img_main);
        }

        $largura_original_p = imagesx($img_main);
        $altura_original_p = imagesy($img_main);

        if ($redmi_mainImg) {
            $nova_largura_p = $m_width != 0 ? $m_width : floor(($largura_original_p / $altura_original_p) * $m_height);
            $nova_altura_p = $m_height != 0 ? $m_height : floor(($altura_original_p / $largura_original_p) * $m_width);
            $img_main_rdm = imagecreatetruecolor($nova_largura_p, $nova_altura_p);
            imagecopyresampled($img_main_rdm, $img_main, 0, 0, 0, 0, $nova_largura_p, $nova_altura_p, $largura_original_p, $altura_original_p);
        } else {
            $img_main_rdm = $img_main;
        }
        return $img_main_rdm;
    }

    // $positionX =  $positionX == 0 ? $define_m_width - $define_w_width : $define_m_width - $define_w_width - $positionX;
    // $positionY =  $positionY == 0 ? $define_m_height - $define_w_height :  $define_m_height - $define_w_height - $positionY;


    // $x_logo = imagesx($img_main_rdm) - $define_w_width - $positionX;
    // $y_logo = imagesy($img_main_rdm) - $define_w_height - $positionY;  

    /*
    <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
</head>
<body>
	<form action="redemencionamento.php" method="POST" enctype="multipart/form-data">
		<fieldset>
			<label for="">Tamanho marca da agua</label>
			<br>
			<input type="number" name="largura" placeholder="Largura">
			<input type="number" name="altura" placeholder="Altura">
			<input type="number" name="opacity" placeholder="Canal alpha">
			
			<!-- <input type="file" name='images[]'> -->
			<br>
			<br>
			<label for="">Posição da marca da agua</label>
			<br>
			<input type="number" name="positionX" placeholder="Posição X">
			<input type="number" name="positionY" placeholder="Posição Y">
			<br>
			<br>
			<label for="">Tamanho img principal</label>
			<br>
			<input type="number" name="larguraP" placeholder="Largura">
			<input type="number" name="alturaP" placeholder="Altura">
			<input type="number" name="quality" placeholder="Qualidade da imagem">
			<br>
			<br>
			<input type="file" name='image'>
			<br>
			<br>
			<input type="submit" value="Enviar">
		</fieldset>
	</form>
</body>
</html>
    */
}

