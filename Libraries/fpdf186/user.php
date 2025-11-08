<?php
require('fpdf.php');

class ReporteUsuario extends FPDF
{
    private $usuario;
    private $headerData;
    private $colores;

    public function __construct($usuario, $headerData, $colores = [])
    {
        parent::__construct();

        $this->usuario = $usuario;
        $this->headerData = $headerData;

        // Solo dos colores: primario y secundario
        $this->colores = array_merge([
            'primario'   => [0, 102, 204],   // Azul
            'secundario' => [230, 230, 230]  // Gris claro
        ], $colores);
    }

    public function Header()
    {
        // Fondo primario
        $this->SetFillColor(...$this->colores['primario']);
        $this->Rect(0, 0, 210, 40, 'F');

        // Logo
        if (!empty($this->headerData['logo'])) {
            $this->Image($this->headerData['logo'], 10, 8, 25);
        }

        // Texto
        $this->SetY(8);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['primario']));
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, mb_convert_encoding($this->headerData['nombre_comite'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'RUC: ' . mb_convert_encoding($this->headerData['ruc'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');
        $this->Cell(0, 5, mb_convert_encoding($this->headerData['direccion'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');
        $this->Cell(0, 5, $this->headerData['telefono'] . ' - ' . $this->headerData['correo'], 0, 1, 'C');

        // Línea inferior
        $this->SetDrawColor(...$this->colores['secundario']);
        $this->SetLineWidth(1);
        $this->Line(10, 40, 200, 40);
        $this->Ln(15);
    }

    public function Footer()
    {
        $this->SetY(-20);

        // Fondo primario
        $this->SetFillColor(...$this->colores['primario']);
        $this->Rect(0, $this->GetY(), 210, 20, 'F');

        // Texto
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['primario']));
        $this->Cell(0, 10, mb_convert_encoding('Página ' . $this->PageNo(), "ISO-8859-1", "UTF-8"), 0, 0, 'C');
    }

    public function generarReporte()
    {
        $this->AddPage();

        // Título principal
        $this->SetFont('Arial', 'B', 14);
        $this->SetFillColor(...$this->colores['primario']);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['primario']));
        $this->Cell(0, 10, mb_convert_encoding('FICHA DE USUARIO DEL SISTEMA', "ISO-8859-1", "UTF-8"), 0, 1, 'C', true);
        $this->Ln(5);

        // Foto del usuario
        $y_inicio_foto = ($this->GetY() - 8);
        if (!empty($this->usuario['foto']) && file_exists($this->usuario['foto'])) {
            $this->Image($this->usuario['foto'], 160, $y_inicio_foto, 20, 20);
        }

        // Nombre completo
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(0, 0, 0);
        $this->MultiCell(130, 10, mb_convert_encoding($this->usuario['nombres_completos'], "ISO-8859-1", "UTF-8"), 0, 'L');
        $this->Ln(2);

        // Datos personales
        $this->seccion('Datos Personales', function () {
            $this->agregarCampo('DNI:', $this->usuario['dni']);
            $this->agregarCampo('Género:', $this->usuario['genero']);
        });

        // Datos de cuenta
        $this->seccion('Datos de la Cuenta', function () {
            $this->agregarCampo('Usuario:', $this->usuario['usuario']);
            $this->agregarCampo('Contraseña:', $this->usuario['contrasena']);
            $this->agregarCampo('Email:', $this->usuario['email']);
            $this->agregarCampo('Rol:', $this->usuario['rol']);
        });

        // Fechas
        $this->seccion('Registro y Actualización', function () {
            $this->SetFont('Arial', 'I', 10);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 8, mb_convert_encoding('Fecha de registro: ' . $this->usuario['fecha_registro'], "ISO-8859-1", "UTF-8"), 0, 1);
            $this->Cell(0, 8, mb_convert_encoding('Fecha de actualización: ' . $this->usuario['fecha_actualizacion'], "ISO-8859-1", "UTF-8"), 0, 1);
        });
    }

    private function seccion($titulo, $contenidoCallback)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(...$this->colores['secundario']);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['secundario']));
        $this->Cell(0, 9, mb_convert_encoding($titulo, "ISO-8859-1", "UTF-8"), 0, 1, 'L', true);

        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0, 0, 0);
        $contenidoCallback();
        $this->Ln(5);
    }

    private function agregarCampo($label, $valor)
    {
        $this->Cell(50, 8, mb_convert_encoding($label, "ISO-8859-1", "UTF-8"), 0, 0);
        $this->Cell(0, 8, mb_convert_encoding($valor, "ISO-8859-1", "UTF-8"), 0, 1);
    }

    private function getContrastingColor($rgb)
    {
        list($r, $g, $b) = $rgb;
        $luminancia = (0.299 * $r + 0.587 * $g + 0.114 * $b);
        return ($luminancia > 186) ? [0, 0, 0] : [255, 255, 255];
    }

    public function outputPDF($nombre = 'reporte_usuario.pdf')
    {
        $this->Output('I', $nombre);
    }
}
