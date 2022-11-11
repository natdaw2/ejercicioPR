<?php
//A
$conexion=new mysqli("localhost","root","","tpv");
if(isset($_POST["pedido"])){
    $pedido=unserialize($_POST["pedido"]);
    $fechaActual=$_POST["fechaActual"];
    $hora=$_POST["hora"];
}
// 1. INICIALIZANDO
require("fpdf/fpdf.php");
$pdf=new FPDF();
$pdf->AddPage();
// 2. ESCRIBIR
$pdf->setFont("Arial", "B", 24);
$pdf->Cell(190,20,utf8_decode("CASA JIMÉNEZ OLLO"), 0,1,"C");
$pdf->setFont("Arial", "", 16);
$pdf->Cell(190,5,"C/ San Fernando n 15", 0,1,"C");
$pdf->Cell(190,10,"Tel/ 666666666", 0,1,"C");
$pdf->Cell(190,5,"NIF/ 77777777A", 0,1,"C");
$pdf->SetXY(0, 50);
$pdf->Cell(80,10,$fechaActual, 0,1,"C");
$pdf->SetXY(134, 50);
$pdf->Cell(80,10,$hora, 0,1,"C");
$pdf->SetY(57);
$pdf->Cell(190,10,"**************************************************************************", 0,1,"C");

$pdf->SetXY(40, 70);
$pdf->setFont("Arial", "B", 16);
$pdf->Cell(20,20,"CANTIDAD", 0,1,"C");
$pdf->SetXY(80, 70);
$pdf->Cell(20,20,utf8_decode("ARTÍCULO"), 0,1,"C");
$pdf->SetXY(120, 70);
$pdf->Cell(20,20,"eu/ud", 0,1,"C");
$pdf->SetXY(160, 70);
$pdf->Cell(20,20,"TOTAL", 0,1,"C");

$pdf->setFont("Arial", "", 16);
$posicionY=80;
foreach($pedido as $indice=>$valor){
    if($valor[1]!=0){
        $ordenProductos="SELECT * from producto where codigo_producto=$valor[0]";
        $chorizoProducto=$conexion->query($ordenProductos);
        while($rodajaProducto=$chorizoProducto->fetch_array()){
            $total=$rodajaProducto[2]*$valor[1]; 
            $posicionY=$posicionY+10;
            if($posicionY>250){
                $pdf->AddPage();
                $posicionY=20;
            }
            $pdf->SetXY(40, $posicionY);
            $pdf->Cell(20,20,$valor[1], 0,1,"C");
            $pdf->SetXY(80, $posicionY);
            $pdf->Cell(20,20,utf8_decode($rodajaProducto[1]), 0,1,"C");
            $pdf->SetXY(120, $posicionY);
            $pdf->Cell(20,20,number_format($rodajaProducto[2],2)." ".chr(128), 0,1,"C");
            $pdf->SetXY(160, $posicionY);
            $pdf->Cell(20,20,number_format($total,2)." ".chr(128), 0,1,"C");
            
        }
    }
}

$montanteTotal=0;
foreach($pedido as $indice => $contenido){
    if($contenido[1]!=0){
        //Selecciono la información de la base de datos de los productos cuyo código es el que se está mirando en este momento en el array
        $ordenProductos="SELECT * from producto where codigo_producto=$contenido[0]";
        $chorizoProducto=$conexion->query($ordenProductos);
        while($rodajaProducto=$chorizoProducto->fetch_array()){
            $total=$rodajaProducto[2]*$contenido[1];
            $montanteTotal=$montanteTotal+$total;
        }
    }
}

$pdf->SetXY(50, $posicionY+40);
$pdf->setFont("Arial", "B", 24);
$pdf->Cell(20,20,"TOTAL:   ".number_format($montanteTotal,2)." ".chr(128), 0,1,"C");

$pdf->SetX(38);
$pdf->setFont("Arial", "", 16);
$pdf->Cell(10,20,utf8_decode("IVA incluído"), 0,1,"C");

$ordenSeleccionarTicket="SELECT MAX(codigo_ticket) from linea_ticket";
$chorizo=$conexion->query($ordenSeleccionarTicket);
$rodaja=$chorizo->fetch_array();
$pdf->SetX(35);
$pdf->Cell(10,20,utf8_decode("Ticket nº".($rodaja[0]+1)), 0,1,"C");

// 4. SACO LA PÁGINA
$pdf->Output();
?>
