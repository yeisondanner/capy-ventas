<?php
require('fpdf.php');

class ReporteTerreno extends FPDF
{
    private $data;
    private $headerData;

    public function __construct($data, $headerData)
    {
        parent::__construct();
        $this->data = $data;
        $this->headerData = $headerData;
    }

    function Header()
    {
        // Logo (ruta desde el array)
        if (!empty($this->headerData['logo'])) {
            $this->Image($this->headerData['logo'], 10, 10, 25);
        }

        // Comité, RUC y datos de contacto
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, mb_convert_encoding($this->headerData['nombre_comite'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'RUC: ' . mb_convert_encoding($this->headerData['ruc'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');
        $this->Cell(0, 6, mb_convert_encoding($this->headerData['direccion'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');
        $this->Cell(0, 6, mb_convert_encoding($this->headerData['telefono'] . ' - ' . $this->headerData['correo'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');

        // Línea y espacio
        $this->Ln(8);
        $this->SetDrawColor(0, 0, 0);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding('Página ', "ISO-8859-1", "UTF-8") . $this->PageNo(), 0, 0, 'C');
    }

    public function generarReporte()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, mb_convert_encoding($this->data['terreno']['nombre'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');
        $this->Ln(5);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, mb_convert_encoding('Información del Área', "ISO-8859-1", "UTF-8"), 0, 1);
        $this->SetFont('Arial', '', 11);
        $this->agregarCampo('Código de plano:', $this->data['terreno']['codigo_plano']);
        $this->agregarCampo('Canal al que pertenece:', $this->data['terreno']['canal']);
        $this->agregarCampo('Lateral al que pertenece:', $this->data['terreno']['lateral']);
        $this->agregarCampo('Sublateral al que pertenece:', $this->data['terreno']['sublateral']);
        $this->agregarCampo('Tamaño en hectáreas (Ha):', $this->data['terreno']['tamano_ha']);
        $this->agregarCampo('Coordenadas:', $this->data['terreno']['coordenadas']);
        //  $this->agregarCampo('Situación de arriendo:', $this->data['terreno']['arriendo']);
        $this->agregarCampo('Estado:', $this->data['terreno']['estado']);

        $this->Ln(8);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, mb_convert_encoding('Información del Usuario', "ISO-8859-1", "UTF-8"), 0, 1);

        $this->SetFont('Arial', '', 11);
        $this->agregarCampo('Código de Usuario:', $this->data['usuario']['codigo']);
        $this->agregarCampo('Nombres Completos:', $this->data['usuario']['nombres']);
        $this->agregarCampo('DNI/RUC:', $this->data['usuario']['documento']);
        $this->agregarCampo('Género:', $this->data['usuario']['genero']);
        $this->agregarCampo('Dirección:', $this->data['usuario']['direccion']);
    }

    private function agregarCampo($label, $valor)
    {
        $this->Cell(70, 8, mb_convert_encoding($label, "ISO-8859-1", "UTF-8"), 0, 0);
        $this->Cell(0, 8, mb_convert_encoding($valor, "ISO-8859-1", "UTF-8"), 0, 1);
    }

    public function outputPDF($nombre = 'reporte_terreno.pdf')
    {
        $this->Output('I', $nombre);
    }
}
