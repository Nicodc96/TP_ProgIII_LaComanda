<?php
use Fpdf\Fpdf;

class Encuesta{
    public $id;
    public $pedido_id;
    public $mesa_puntuacion;
    public $restaurante_puntuacion;
    public $mozo_puntuacion;
    public $cocinero_puntuacion;
    public $puntuacion_promedio;
    public $comentario;

    public function __construct(){}

    public static function crearEncuesta($pedido_id, $mesa_ptn, $restaurante_ptn, $mozo_ptn, $cocinero_ptn, $comentario){
        $encuesta = new Encuesta();
        $encuesta->pedido_id = $pedido_id;
        $encuesta->mesa_puntuacion = $mesa_ptn;
        $encuesta->restaurante_puntuacion = $restaurante_ptn;
        $encuesta->mozo_puntuacion = $mozo_ptn;
        $encuesta->cocinero_puntuacion = $cocinero_ptn;
        $encuesta->setPuntuacionPromedio();
        $encuesta->comentario = $comentario;
        return $encuesta;
    }

    public function setPuntuacionPromedio() {
        $ptn_promedio = 0;
        $arraySum = array($this->mesa_puntuacion, $this->restaurante_puntuacion, $this->mozo_puntuacion, $this->cocinero_puntuacion);
        if(count($arraySum) > 0) {
            $ptn_promedio = round(array_sum($arraySum) / count($arraySum), 2);
        }
        $this->puntuacion_promedio = $ptn_promedio;
    }

    public static function mostrarEncuestasTabla($array_encuestas = array()){
        if (count($array_encuestas) <= 0){
            $array_encuestas = self::obtenerTodos();
        }
        $mensaje = "Lista vacia.<br>";
        if (is_array($array_encuestas) && count($array_encuestas) > 0){
            $mensaje = "<h3 align='center'> Lista de Encuestas </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>ID Pedido</th><th>Mesa Points</th><th>Restaurante Points</th><th>Mozo Points</th><th>Cocinero Points</th><th>Puntuacion promedio</th></tr><tbody>";
            foreach($array_encuestas as $encuesta){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $encuesta->id . "</td>" .
                "<td>" . $encuesta->pedido_id . "</td>" .
                "<td>" . $encuesta->mesa_puntuacion . "</td>" .
                "<td>" . $encuesta->restaurante_puntuacion . "</td>" .
                "<td>" . $encuesta->mozo_puntuacion . "</td>" .
                "<td>" . $encuesta->cocinero_puntuacion . "</td>" .
                "<td><strong>" . $encuesta->puntuacion_promedio . "</strong></td></tr>";
                $mensaje .= "<th colspan='7' align='center'> Comentario </th>";
                $mensaje .= "<tr><td colspan='7' align='center'>" . $encuesta->comentario . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function mostrarEncuestaTabla($encuesta){
        $mensaje = "El objeto enviado por parametro no es una encuesta.";
        if (is_a($encuesta, "Encuesta")){
            $mensaje = "<h3 align='center'> Lista de Encuestas </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Usuario ID</th><th>Nombre</th><th>ID Area Trabajo</th><th>Fecha Alta</th><th>Fecha Baja</th></tr><tbody>";
            $mensaje .= "<tr align='center'>" .
            "<td>" . $encuesta->id . "</td>" .
            "<td>" . $encuesta->pedido_id . "</td>" .
            "<td>" . $encuesta->mesa_puntuacion . "</td>" .
            "<td>" . $encuesta->restaurante_puntuacion . "</td>" .
            "<td>" . $encuesta->mozo_puntuacion . "</td>" .
            "<td>" . $encuesta->cocinero_puntuacion . "</td>" .
            "<td><strong>" . $encuesta->puntuacion_promedio . "</strong></td></tr>";
            $mensaje .= "<th colspan='7' align='center'> Comentario </th>";
            $mensaje .= "<tr><td colspan='7' align='center'>" . $encuesta->comentario . "</td></tr>";
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }

    public static function insertarEncuestaDB($encuesta){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (pedido_id, mesa_puntuacion, restaurante_puntuacion, mozo_puntuacion, cocinero_puntuacion, puntuacion_promedio, comentario)
        VALUES (:pedido_id, :mesa_puntuacion, :restaurante_puntuacion, :mozo_puntuacion, :cocinero_puntuacion, :puntuacion_promedio, :comentario);");
        $consulta->bindParam(":pedido_id", $encuesta->pedido_id);
        $consulta->bindParam(":mesa_puntuacion", $encuesta->mesa_puntuacion);
        $consulta->bindParam(":restaurante_puntuacion", $encuesta->restaurante_puntuacion);
        $consulta->bindParam(":mozo_puntuacion", $encuesta->mozo_puntuacion);
        $consulta->bindParam(":cocinero_puntuacion", $encuesta->cocinero_puntuacion);
        $consulta->bindParam(":puntuacion_promedio", $encuesta->puntuacion_promedio);
        $consulta->bindParam(":comentario", $encuesta->comentario);
        try {
            $consulta->execute();
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }        
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta");
    }

    public static function obtenerEncuestaPorId($id_encuesta){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE id = :id");
        $consulta->bindValue(":id", $id_encuesta, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject("Encuesta");
    }

    public static function obtenerMejoresEncuestas($cantidad){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas ORDER BY puntuacion_promedio DESC LIMIT :cantidad");
        $consulta->bindValue(":cantidad", $cantidad, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta");
    }

    public static function DescargarPDF($directorio, $cantidadEncuestas){
        $encuestas = self::obtenerMejoresEncuestas($cantidadEncuestas);
        if ($encuestas){
            if (!file_exists($directorio)){
                mkdir($directorio, 0777, true);
            }
            $pdf = new Fpdf();
            $pdf->AddPage();

            // Titulo
            $pdf->SetFont("Arial", "I", 23);
            $pdf->Cell(160, 15, "La Comanda - Informes - Administracion", 1, 3, "L");
            $pdf->Ln(3);

            // Sub-titulo
            $pdf->SetFont("Arial", "", 15);
            $pdf->Cell(67, 6, "TP - Programacion III - PHP", 1, 3, "L");
            $pdf->Ln(3);
            
            // Nombre del Alumno
            $pdf->Cell(60, 4, "Alumno: Diaz, Lautaro Nicolas", 0, 1, "L");
            $pdf->Cell(73, 0, "", "T");
            $pdf->Ln(5);

            // Columnas de la clase 'Encuesta'
            $cabecera = array("ID", "Pedid_ID", "Mesa_P", "Rest_P", "Mozo_P", "Cocin_P", "PROMED", "COMENTARIO");

            $pdf->SetFillColor(125, 0, 0);
            $pdf->SetTextColor(125);
            $pdf->SetDrawColor(50, 0, 0);
            $pdf->SetLineWidth(.3);
            $pdf->SetFont("Arial", "B", 8);
            $w = array(10, 15, 15, 15, 15, 15, 15, 92);

            // Escribo el titulo de cada columna
            for ($i = 0; $i < count($cabecera); $i++) {
                $pdf->Cell($w[$i], 7, $cabecera[$i], 1, 0, "C", true);
            }
            $pdf->Ln();

            $pdf->SetFillColor(215, 209, 235);
            $pdf->SetTextColor(0);
            $pdf->SetFont("");

            // Informacion
            $fill = false;

            // Datos de las encuestas en las columnas
            foreach ($encuestas as $encuesta) {
                $pdf->Cell($w[0], 6, $encuesta->id, "LR", 0, "C", $fill);
                $pdf->Cell($w[1], 6, $encuesta->pedido_id, "LR", 0, "C", $fill);
                $pdf->Cell($w[2], 6, $encuesta->mesa_puntuacion, "LR", 0, "C", $fill);
                $pdf->Cell($w[3], 6, $encuesta->restaurante_puntuacion, "LR", 0, "C", $fill);
                $pdf->Cell($w[4], 6, $encuesta->mozo_puntuacion, "LR", 0, "C", $fill);
                $pdf->Cell($w[5], 6, $encuesta->cocinero_puntuacion, "LR", 0, "C", $fill);
                $pdf->Cell($w[6], 6, $encuesta->puntuacion_promedio, "LR", 0, "C", $fill);
                $pdf->Cell($w[7], 6, $encuesta->comentario, "LR", 0, "C", $fill);
                $pdf->Ln();
                $fill = !$fill;
            }

            $pdf->Cell(array_sum($w), 0, "", "T");

            $archivo_ruta = $directorio . "Encuestas_" . date("Y_m_d") . ".pdf";
            $pdf->Output("F", $archivo_ruta, "I");

            $payload = json_encode(array("message" => "Se ha generado el archivo PDF " . $archivo_ruta));
        } else{
            $payload = json_encode(array("error" => "No se han encontrado encuestas para imprimir."));
        }
        return $payload;
    }
}
?>