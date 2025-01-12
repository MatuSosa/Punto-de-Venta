<?php
session_start();
include "../conexion.php";
require_once 'tcpdf/tcpdf.php';

// Consulta de productos
$query = mysqli_query($conexion, "SELECT codigo, descripcion FROM producto");
$productos = mysqli_fetch_all($query, MYSQLI_ASSOC);

class PDF extends TCPDF
{
    public function Header()
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Códigos de Barras', 0, 1, 'C');
        $this->Ln(10);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    public function Barcode($codigo, $descripcion)
    {
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 10, $descripcion, 0, 1, 'L');
        $this->write1DBarcode($codigo, 'C128', '', '', 60, 18, 0.4, array('position'=>'S', 'align'=>'C', 'stretch'=>false, 'fitwidth'=>true, 'cellfitalign'=>'', 'border'=>true, 'hpadding'=>'', 'vpadding'=>'', 'fgcolor'=>array(0,0,0), 'bgcolor'=>false, 'text'=>true, 'label'=> $codigo, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>2), 'N');
        $this->Ln(10);
    }
}

$pdf = new PDF();
$pdf->AddPage();

foreach ($productos as $producto) {
    $pdf->Barcode($producto['codigo'], $producto['descripcion']);
}

$pdf->Output('codigos_de_barras.pdf', 'I');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Códigos de Barras</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .barcode {
            display: inline-block;
            text-align: center;
            margin: 20px;
        }
        .barcode img {
            width: 200px;
            height: auto;
        }
        .product-name {
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-center">Códigos de Barras</h3>
        <div class="row justify-content-center">
            <?php foreach ($productos as $producto): ?>
                <div class="barcode">
                    <?php
                    // Generar la imagen del código de barras
                    $codigo = $producto['codigo'];
                    barcode("images/barcode_$codigo.png", $codigo, 20, "horizontal", "code128", true);
                    ?>
                    <img src="images/barcode_<?php echo $codigo; ?>.png" alt="Código de Barras">
                    <div class="product-name"><?php echo htmlspecialchars($producto['descripcion']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
        </div>
    </div>
</body>
</html>
