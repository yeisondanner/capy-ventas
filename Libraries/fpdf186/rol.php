<?php
require('fpdf.php');

class rol extends FPDF
{
    protected $headerData;
    protected $colores;

    public function __construct($headerData, $colores = [])
    {
        parent::__construct();
        $this->headerData = $headerData;

        // Definir solo colores primario y secundario
        $this->colores = array_merge([
            'primario'   => [0, 102, 204],   // Azul
            'secundario' => [230, 230, 230]  // Gris claro
        ], $colores);
    }

    public function Header()
    {
        // Fondo del encabezado
        $this->SetFillColor(...$this->colores['primario']);
        $this->Rect(0, 0, 210, 40, 'F');

        // Logo
        if (!empty($this->headerData['logo'])) {
            $this->Image($this->headerData['logo'], 10, 8, 25);
        }

        // Texto del encabezado
        $this->SetY(8);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['primario']));
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, mb_convert_encoding($this->headerData['nombre_comite'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'RUC: ' . mb_convert_encoding($this->headerData['ruc'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');
        $this->Cell(0, 5, mb_convert_encoding($this->headerData['direccion'], "ISO-8859-1", "UTF-8"), 0, 1, 'C');
        $this->Cell(0, 5, $this->headerData['telefono'] . ' - ' . $this->headerData['correo'], 0, 1, 'C');

        // Línea inferior decorativa
        $this->SetDrawColor(...$this->colores['secundario']);
        $this->SetLineWidth(1);
        $this->Line(10, 40, 200, 40);
        $this->Ln(15);
    }

    public function Footer()
    {
        $this->SetY(-20);

        // Fondo del pie
        $this->SetFillColor(...$this->colores['primario']);
        $this->Rect(0, $this->GetY(), 210, 20, 'F');

        // Texto del pie
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['primario']));
        $this->Cell(0, 10, mb_convert_encoding('Página ' . $this->PageNo(), "ISO-8859-1", "UTF-8"), 0, 0, 'C');
    }

    public function generarReporteRol($rolData, $permisos)
    {
        $this->AddPage();

        // ===== Información del Rol =====
        $this->SetFont('Arial', 'B', 14);
        $this->SetFillColor(...$this->colores['primario']);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['primario']));
        $this->Cell(0, 10, mb_convert_encoding("Rol: {$rolData['nombre']}", "ISO-8859-1", "UTF-8"), 0, 1, 'L', true);

        $this->SetFillColor(...$this->colores['secundario']);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['secundario']));
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, mb_convert_encoding('Información General', "ISO-8859-1", "UTF-8"), 0, 1, 'L', true);

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 6, mb_convert_encoding("Código: {$rolData['codigo']}", "ISO-8859-1", "UTF-8"), 0, 1);
        $this->MultiCell(0, 6, mb_convert_encoding("Descripción: {$rolData['descripcion']}", "ISO-8859-1", "UTF-8"));
        $this->Cell(0, 6, mb_convert_encoding("Estado: {$rolData['estado']}", "ISO-8859-1", "UTF-8"), 0, 1);

        $this->Ln(4);

        // ===== Detalle de permisos =====
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(...$this->colores['secundario']);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['secundario']));
        $this->Cell(0, 8, mb_convert_encoding('Detalle de Permisos', "ISO-8859-1", "UTF-8"), 0, 1, 'L', true);

        // Encabezado de tabla
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(...$this->colores['primario']);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['primario']));
        $headers = ['Nombre' => 35, 'Ruta' => 30, 'Menú' => 20, 'Público' => 20, 'Nav. Menú' => 25, 'Descripción' => 60];
        foreach ($headers as $title => $width) {
            $this->Cell($width, 7, mb_convert_encoding($title, "ISO-8859-1", "UTF-8"), 1, 0, 'C', true);
        }
        $this->Ln();

        // Filas de datos
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
        $fill = false;
        foreach ($permisos as $permiso) {
            $data = [
                mb_convert_encoding($permiso['nombre'], "ISO-8859-1", "UTF-8"),
                mb_convert_encoding($permiso['ruta'], "ISO-8859-1", "UTF-8"),
                mb_convert_encoding($permiso['menu'], "ISO-8859-1", "UTF-8"),
                mb_convert_encoding($permiso['publico'], "ISO-8859-1", "UTF-8"),
                mb_convert_encoding($permiso['menu_nav'], "ISO-8859-1", "UTF-8"),
                mb_convert_encoding($permiso['descripcion'], "ISO-8859-1", "UTF-8")
            ];

            // Calcular altura máxima
            $nb = 0;
            $i = 0;
            foreach ($headers as $width) {
                $nb = max($nb, $this->NbLines($width, $data[$i]));
                $i++;
            }
            $h = 5 * $nb;

            // Fondo alternado
            $this->SetFillColor($fill ? 255 : 245, $fill ? 255 : 245, $fill ? 255 : 245);
            $fill = !$fill;

            // Dibujar celdas
            $x = $this->GetX();
            $y = $this->GetY();
            $i = 0;
            foreach ($headers as $width) {
                $this->Rect($x, $y, $width, $h);
                $this->MultiCell($width, 5, $data[$i], 0, 'L', true);
                $x += $width;
                $this->SetXY($x, $y);
                $i++;
            }
            $this->Ln($h);
        }

        $this->Ln(5);

        // ===== Fechas =====
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(...$this->colores['secundario']);
        $this->SetTextColor(...$this->getContrastingColor($this->colores['secundario']));
        $this->Cell(0, 8, mb_convert_encoding('Registro y Actualización', "ISO-8859-1", "UTF-8"), 0, 1, 'L', true);

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 6, mb_convert_encoding("Fecha de registro: {$rolData['fecha_registro']}", "ISO-8859-1", "UTF-8"), 0, 1);
        $this->Cell(0, 6, mb_convert_encoding("Fecha de actualización: {$rolData['fecha_actualizacion']}", "ISO-8859-1", "UTF-8"), 0, 1);
    }

    private function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    private function getContrastingColor($rgb)
    {
        list($r, $g, $b) = $rgb;
        $luminancia = (0.299 * $r + 0.587 * $g + 0.114 * $b);
        return ($luminancia > 186) ? [0, 0, 0] : [255, 255, 255];
    }
}
