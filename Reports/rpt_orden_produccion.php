<?php
include_once '../Clases/clsSetting.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set= new Set();     
//echo $_GET[id];
class PDF extends FPDF
{
    
function barCode($xpos,$ypos,$code,$baseline,$height){

	$wide = $baseline;
	$narrow = $baseline / 3 ; 
	$gap = $narrow;
	$barChar['0'] = 'nnnwwnwnn';
	$barChar['1'] = 'wnnwnnnnw';
	$barChar['2'] = 'nnwwnnnnw';
	$barChar['3'] = 'wnwwnnnnn';
	$barChar['4'] = 'nnnwwnnnw';
	$barChar['5'] = 'wnnwwnnnn';
	$barChar['6'] = 'nnwwwnnnn';
	$barChar['7'] = 'nnnwnnwnw';
	$barChar['8'] = 'wnnwnnwnn';
	$barChar['9'] = 'nnwwnnwnn';
	$barChar['A'] = 'wnnnnwnnw';
	$barChar['B'] = 'nnwnnwnnw';
	$barChar['C'] = 'wnwnnwnnn';
	$barChar['D'] = 'nnnnwwnnw';
	$barChar['E'] = 'wnnnwwnnn';
	$barChar['F'] = 'nnwnwwnnn';
	$barChar['G'] = 'nnnnnwwnw';
	$barChar['H'] = 'wnnnnwwnn';
	$barChar['I'] = 'nnwnnwwnn';
	$barChar['J'] = 'nnnnwwwnn';
	$barChar['K'] = 'wnnnnnnww';
	$barChar['L'] = 'nnwnnnnww';
	$barChar['M'] = 'wnwnnnnwn';
	$barChar['N'] = 'nnnnwnnww';
	$barChar['O'] = 'wnnnwnnwn'; 
	$barChar['P'] = 'nnwnwnnwn';
	$barChar['Q'] = 'nnnnnnwww';
	$barChar['R'] = 'wnnnnnwwn';
	$barChar['S'] = 'nnwnnnwwn';
	$barChar['T'] = 'nnnnwnwwn';
	$barChar['U'] = 'wwnnnnnnw';
	$barChar['V'] = 'nwwnnnnnw';
	$barChar['W'] = 'wwwnnnnnn';
	$barChar['X'] = 'nwnnwnnnw';
	$barChar['Y'] = 'wwnnwnnnn';
	$barChar['Z'] = 'nwwnwnnnn';
	$barChar['-'] = 'nwnnnnwnw';
	$barChar['.'] = 'wwnnnnwnn';
	$barChar[' '] = 'nwwnnnwnn';
	$barChar['*'] = 'nwnnwnwnn';
	$barChar['$'] = 'nwnwnwnnn';
	$barChar['/'] = 'nwnwnnnwn';
	$barChar['+'] = 'nwnnnwnwn';
	$barChar['%'] = 'nnnwnwnwn';
	$this->SetFont('Arial','',10);
	$this->Text($xpos, $ypos + $height + 4, $code);
	$this->SetFillColor(0);
	$code = '*'.strtoupper($code).'*';
	for($i=0; $i<strlen($code); $i++){
		$char = $code[$i];
		if(!isset($barChar[$char])){
			$this->Error('Caracter Invalido: '.$char);
		}
		$seq = $barChar[$char];
		for($bar=0; $bar<9; $bar++){
			if($seq[$bar] == 'n'){
				$lineWidth = $narrow/1.5;
			}else{
				$lineWidth = $wide/1.5;
			}
			if($bar % 2 == 0){
			    	$this->Rect($xpos-12, $ypos, $lineWidth, $height, 'F');
			}
			$xpos += $lineWidth;
		}
		$xpos += $gap;
	}
}
function template($x=0,$y=0,$tlt)
{
    $this->SetFont('times','B',9);
    $this->SetFillColor(240,240,240);
    $this->Image("logo_noperti.png",$x+2,$y+1,50);
    $this->Text($x+70,$y+6,"ORDEN DE PRODUCCION DE ".$tlt);$this->barCode($x+160,$y+3,"000PRUEBA0111",1,10);$this->Text($x+80,$y+15,"ORDEN No ");
    $this->Text($x,$y+70,"___________________________________________________________________________________________________________");
    $this->SetFont('Arial','B',6);
    $this->Text($x+5,$y+15,"FECHA ORDEN:");
    $this->Text($x+5,$y+19,"FECHA ETREGA:");
    $this->Text($x+5,$y+27,"CLIENTE:");$this->Text($x+50,$y+27,"FAMILIA:");$this->Text($x+95,$y+23,"Cantidad Solicitada");$this->Text($x+160,$y+23,"REPORTE DE PRODUCCION");
    $this->Text($x+5,$y+32,"CODIGO PRO:");$this->Text($x+50,$y+32,"LINEA:");
    $this->Text($x+5,$y+37,"MATERIAL A UTILIZAR");
    $this->Text($x+5,$y+38,"_____________________");
    $this->SetXY($x+90,$y+24);$this->Cell(10,4,"1,5",1,0,"C");$this->Cell(10,4,"2",1,0,"C");$this->Cell(10,4,"2,5",1,0,"C");$this->Cell(10,4,"3",1,0,"C");
    $this->SetXY($x+75,$y+28);$this->Cell(15,4,"Plazas",0,0,"C");$this->Cell(10,4,"1,5",1,0,"C");$this->Cell(10,4,"2",1,0,"C");$this->Cell(10,4,"2,5",1,0,"C");$this->Cell(10,4,"3",1,0,"C");
    $this->SetXY($x+75,$y+32);$this->Cell(15,4,"Cojin",0,0,"C");$this->Cell(10,4,"  ",1,0,"C");$this->Cell(10,4,"   ",1,0,"C");$this->Cell(10,4,"  ",1,0,"C");$this->Cell(10,4,"  ",1,0,"C");
    $this->SetXY($x+5,$y+40);$this->Cell(30,4,"",0,0,"C");$this->Cell(20,4,"Referencia",0,0,"C");$this->Cell(15,4,"Color",0,0,"C");
    $this->SetXY($x+132,$y+24);$this->Cell(10,4,"FECHA",1,0,"C");$this->Cell(25,4,"REFERENCIA",1,0,"C");$this->Cell(10,4,"1,5",1,0,"C");$this->Cell(10,4,"2",1,0,"C");$this->Cell(10,4,"2,5",1,0,"C");$this->Cell(10,4,"3",1,0,"C");
    $this->SetXY($x+132,$y+28);$this->Cell(10,4,"",1,0,"C");$this->Cell(25,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");
    $this->SetXY($x+132,$y+32);$this->Cell(10,4,"",1,0,"C");$this->Cell(25,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");
    $this->SetXY($x+132,$y+36);$this->Cell(10,4,"",1,0,"C");$this->Cell(25,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");
    $this->SetXY($x+132,$y+40);$this->Cell(10,4,"",1,0,"C");$this->Cell(25,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");
    $this->SetXY($x+132,$y+44);$this->Cell(10,4,"",1,0,"C");$this->Cell(25,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");
    $this->SetXY($x+132,$y+48);$this->Cell(10,4,"",1,0,"C");$this->Cell(25,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");
    $this->SetXY($x+132,$y+52);$this->Cell(10,4,"",1,0,"C");$this->Cell(25,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");$this->Cell(10,4,"",1,0,"C");
}
function egr_mp($x=0,$y=0,$data,$data2,$files)
{
    $Set= new Set();     
    $this->SetFont('times','B',9);
    $this->SetFillColor(240,240,240);
    $this->Image("logo_noperti.png",$x+2,$y+1,50);
    $this->Text($x+70,$y+6,"EGRESO DE BODEGA DE MP");$this->barCode($x+160,$y+3,"000PRUEBA0111",1,10);$this->Text($x+80,$y+15,"ORDEN No ");
    $this->SetFont('Arial','B',6);
    $this->Text($x+5,$y+15,"FECHA ORDEN:");
    $this->Text($x+5,$y+19,"FECHA ETREGA:");
    $this->Text($x+5,$y+27,"CLIENTE:");$this->Text($x+50,$y+27,"FAMILIA:");$this->Text($x+95,$y+23,"Cantidad Solicitada");$this->Text($x+160,$y+23,"REPORTE DE PRODUCCION");
    $this->Text($x+5,$y+32,"CODIGO PRO:");$this->Text($x+50,$y+32,"LINEA:");
    $this->SetXY($x+7.2,$y+35);$this->Cell(50,4,"DESCRIPCION",1,0,"C");$this->Cell(30,4,"REFERENCIA",1,0,"C");$this->Cell(30,4,"CANTIDAD SOLICITADA",1,0,"C");
    $this->Ln();
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='I' && !empty($file[9]) )
                                        {
                                            
                                            $val=$data2[$file[8]];                                            
                                            
                                            switch ($file[2]){
                                                case 'E':
                                                    $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    
                                                    $this->Cell(50,3,$file[9],1,0,"C");$this->Cell(50,3,$file[9],1,0,"C");
                                                    $this->Ln();
                                                    
                                                break;  
                                            }
                                        }    
                                    $n++;                    
                                    }
    
    
    $this->SetXY($x+140,$y+65);$this->Cell(60,4,"EGRESO DE BODEGA MP","T",0,"C");
}
function orden($data,$data2,$files,$id)
{
    $this->template($x=0,$y=0,"CORTE");
    $this->template($x=0,$y=70,"COSTURA");
    $this->template($x=0,$y=140,"EMPAQUE");
    $this->egr_mp($x=0,$y=210,$data,$data2,$files,$id);
}

}


$data2=pg_fetch_array($Set->list_one_data_by_id('erp_pedidos',$_GET[id]));      
$data=pg_fetch_array($Set->list_one_data_by_id('erp_productos',$data2[ped_d]));   
$files=pg_fetch_array($Set->lista_one_data('erp_productos_set',$data[ids]));               


$pdf = new PDF();
$pdf->AddPage();
$pdf->SetDisplayMode(100);
$pdf->orden($data,$data2,$files);
$pdf->Output();



