<!-- TABLA -->
<div class="container tablatexto">
  <div class="row">
    <div class="col-lg-10 col-lg-offset-1">
      <table class="table table-striped table-dark">
        <thead class="thead-dark">
        </thead>
        <tbody>
          <?php
          foreach ($array as $nrocompetencia => $nivel) {
              ?>
              <tr>
                <th scope="row" class="text-center info" colspan="<?php echo count($nivel); ?>">
                  C<?php echo $nrocompetencia; ?>:
                  <?php
                  $c = new Sql('matriz_observados_nombre_competencias');
                  $c->where('id', $nrocompetencia);
                  $nombrecomp = $c->fetch('field:nombre');
                  echo $nombrecomp[0];
                  ?></b>
                </th>
                <tr>
                  <?php
                    foreach ($respuesta as $niveles) {
                      ?>
                      <th scope="col">
                        <input type="radio" name="radionivel<?php echo $nrocompetencia; ?>" nivelid="<?php echo $niveles->id; ?>" nrocompetencia='<?php echo $nrocompetencia; ?>' name2="radionivel" value="<?php echo $niveles->nivel_nombre; ?>"><?php echo $niveles->nivel_nombre; ?>
                      </th>
                      <?php
                    }
                  ?>
                </tr>
                  <?php
                    foreach ($nivel as $nronivel => $marcadores) {
                  ?>
                <td>
                  <?php
                    foreach ($marcadores as $marcador) {
                      ?>
                      <input type="checkbox" name="marcador" nrocompetencia='<?php echo $nrocompetencia; ?>' nivel='<?php echo $nronivel; ?>' marcadorid='<?php echo $marcador["id"];?>' value="<?php echo $marcador["marcador"]; ?>"><?php echo $marcador["marcador"]; ?><br>
                  <?php
                    }
                  ?>
                </td>
                <?php
                    }
                 ?>
              </tr>
              <tr>
                <td colspan="<?php echo count($nivel); ?>">
                  <textarea class="commentbox text-center form-control" nrocompetencia='<?php echo $nrocompetencia; ?>' placeholder="Fundamente su elección" id="comentariocompetencia<?php echo $nrocompetencia; ?>" name="comentariocompetencia" class="formcontrol" rows="2" cols="80"></textarea>
                </td>
              </tr>
            <?php
            }
          ?>
        </tbody>
        <tfoot>
          <tr colspan="<?php echo count($nivel); ?>">
            <td colspan="<?php echo count($nivel); ?>">
              <h5 class="commentbox text-center"> <b>Observacion Final:</b> </h5>
              <textarea name="observacionfinal" id="observacionfinal" placeholder="Observación Final" class="commentbox text-center form-control"  rows="4" cols="80"></textarea>
            </td>
          </tr>
        </tfoot>
      </table>
      <?php
      for ($i=2; $i <= $nrocompetencia; $i++) {
        $cant = $i;
      }
      ?>
      <input type="hidden" name="canttotal" value="<?php echo $cant; ?>">
      <br>
      <form name='formrevision' id='formrevision' class='ax_form' style='margin:0px;' enctype='multipart/form-data' action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?page=/b/observado_revision"; ?>' method=post >
      <input type='hidden' name='accion' id='accion' value='feedback' />
      <input type='hidden' name='idobservado' id='idobservado' value='<?php echo $observado->id ?>' />
      <input type='hidden' name='idalumno' id='idalumno' value='<?php echo $observado->idalumno ?>' />
      <input type='hidden' name='nombrealumno' id='nombrealumno' value='<?php echo $observado->nombrealumno ?>' />
      <input type='hidden' name='emailalumno' id='emailalumno' value='<?php echo $observado->email ?>' />
      <input type='hidden' name='idcursoaca' id='idcursoaca' value='<?php echo $observado->idcursoaca ?>' />
      <input type="hidden" name="idobservado2" id='idobservado2' value="<?php echo $idobservado ?>">
      <fieldset class="column column_1_1">
      <!-- <label>Opcional: Cargar Archivo con Feedback (cualquier formato, máximo 5 Mb, por ejemplo un audio de celular)<br></label>
      <input type='hidden' name='MAX_FILE_SIZE' value='5242880' />
      <input id='fbaudioarchivo' name='fbaudioarchivo' type='file' class='text_input' style='width:400px' /> -->
      <style>
        #mceu_22-body{
          display:none !important;
        }
        #mceu_30{
          display:none !important;
        }
      </style>
      <?php
          if (strlen($observado->ubicacion)>0) {
              echo "<a href='".$observado->fbaudioubicacion."' target='_blank'>".$observado->fbaudioarchivo."</a>";
          }
      ?>
      </fieldset>
      <fieldset class="col-md-offset-4">
        <label><strong>Estado de la Revisión</strong><br></label>
        <?php
        if($observado->fbtexto == ""){
          ?>
          <script type="text/javascript">
          $(document).ready(function(){
            $("#idestado option[value*='1']").prop('disabled',true);
            $("#idestado option[value*='5']").prop('disabled',true);
            $("#idestado option[value='']").prop('selected',true);
          });
          </script>
          <?php
          echo combo_estados_revision_requisitos_certificacion("idestado", $observado->estado, "text_input");
        }else{
          echo "<br>".System::get_estado_revision($observado->estado);
        }
        ?>
      </fieldset>
      <?php
        if ($observado->estado<>2) {
      ?>
        <button type="button" class="col-lg-4 col-lg-offset-4 btn btn-warning"  name="button" id="EnviaEval">Enviar Evaluación</button>
        <!-- <input name='enviarform' type='button' value=" Enviar Feedback al Alumno" onclick="validarform(<?php echo $observado->id;?>,<?php echo $observado->idalumno;?>);"> -->
      <?php
        }
      ?>
        </form>

<!-- SCRIPT AJAX JQUERY -->

<script type="text/javascript">

$("#EnviaEval").on("click",function(){
  idobservado = $("#idobservado2").val();
  //SETEA EL NIVEL
  var nivelSeleccionado = [];
  $('input[name2=radionivel]:checked').each(function(){
    var nivelid = $(this).attr("nivelid");
    var nrocompetencia = $(this).attr("nrocompetencia");
    var nivelselect = $(this).val();
    nivelSeleccionado[nrocompetencia] = [];
    nivelSeleccionado[nrocompetencia].push(nivelselect, nivelid);
  });

  //MARCADORES, NIVELES Y COMPETENCIAS
  var calificacion = [];
  $("input[marcadorid]:checked").each(function(){
    var nrocompetencia = $(this).attr("nrocompetencia");
    var nivel = $(this).attr("nivel");
    var marcadorid = $(this).attr("marcadorid");
    // var marcador = $(this).val();
    var texto = nrocompetencia + "||" + nivel + "||" + marcadorid;
    calificacion.push(texto);
    // calificacion[nrocompetencia][nivel].push(marcadorid);
  });

  //Fundamentacion (textarea)
  var fundamentacion = [];
  $("textarea[name='comentariocompetencia']").each(function(){
    var nrocompetencia = $(this).attr("nrocompetencia");
    var input = $(this).val();
    fundamentacion[nrocompetencia] = [];
    fundamentacion[nrocompetencia].push(input);
  });

  //ESTADO DE OBSERVADO
  var idestado = $("select#idestado").val();
  var errores = 0;
  var i;
  var faltanivel = 0;

  for (i = 2; i <= 9; i++) {
    // FALTA MARCAR NIVEL
    if(!$("input[name='radionivel"+i+"']").is(":checked")){
      faltanivel++;
      var alertaNivel = "Falta elegir el nivel de alguna competencia.";
    }
  }

    // NO APROBADO SI ES INEXPERTO
    inexpertoNoAprueba = 0;
    alertaInexperto = "";
  for (i = 2; i <= 9; i++) {
    $("input[name='radionivel"+i+"']:checked").each(function(){
      // valor = $("input[name='radionivel"+i+"']:checked").val();
      if ($("input[name='radionivel"+i+"']:checked").val() == "Inexperto" && idestado == 2) {
        inexpertoNoAprueba++;
        alertaInexperto = "El alumno no puede aprobar si tiene una competencia calificada como Inexperto.";
      }
    });
  }

  // FUNDAMENTACION ESTÁ Puesta.
  var contareas = 0;
  for(i = 2; i <= 9; i++){
    if($("textarea[id='comentariocompetencia"+i+"']").val() == ""){
      contareas++;
      var alertaFundamentacion = "Falta fundamentar alguna Competencia";
    }
  }

  //Observacion finali
  observacionfinal = $("#observacionfinal").val();

  errores = 0;
  //SUMA ERRORES
  if (faltanivel > 0) {
    alert(alertaNivel);
    errores++;
  }
  if (inexpertoNoAprueba > 0) {
    alert(alertaInexperto);
    errores++;
  }
  if (contareas > 0) {
    alert(alertaFundamentacion);
    errores++;
  }
  if (observacionfinal == "") {
    errores++;
  }

  if (errores == 0) {
    swal({
     title: "¿Confirma Corrección?",
     text: "No podrá volver atras.",
     html:true,
     type: "warning",
     showSpinner: true,
     showCancelButton: true,
     confirmButtonColor: "#DD6B55",
     confirmButtonText: "Send",
     showLoaderOnConfirm: true,
     closeOnConfirm: false
   }, function () {
    $.ajax({
      type: 'post',
      url: '/aca/b/observado_revision.handler.php',
      data: {
        opcion: "guardaMatriz",
        calificacion: calificacion,
        nivelSeleccionado: nivelSeleccionado,
        idestado: idestado,
        idobservado: idobservado,
        fundamentacion: fundamentacion,
        observacionfinal: observacionfinal
        },
      success: function (datos) {
        swal("Observado Corregido");
        window.location.replace("http://www.axonplataforma.com.ar/aca/index.php?page=/b/observados_profesores");
      }
    });
  });
} else {
  alert("Observación final debe ser completada.");
}
});

</script>
<!-- HANDLER DE LA MATRIZ -->

<?php

case "guardaMatriz":

// System::print_pre($_POST);
$date = date('Y-m-d');
$observacionfinal = $_POST["observacionfinal"];
$marcadores_se = $_POST["calificacion"];
$competencias = $_POST["nivelSeleccionado"];
$idestado = $_POST["idestado"];
$idobservado = $_POST["idobservado"];
$fundamentacion = $_POST["fundamentacion"];

$c = new Sql('matriz_observados_niveles');
$niveles = $c->fetch();

$marcaores = [];
foreach ($marcadores_se as $marcador) {
  $m = explode("||", $marcador);
  $marcadores[$m[0]][] = array($m[1], $m[2]);
}
ob_start();
?>
<div class="container">
  <div class="row">
    <div class="col-lg-10 col-lg-offset-1">
      <table class="table table-striped table-dark" >
        <tbody>
          <?php
          foreach ($competencias as $nrocompetencia => $value) {
            if (is_array($value)) {
              ?>
              <tr>
                <th scope="row" class="text-center info" colspan="<?php echo count($niveles); ?>">
                  <b>C<?php echo $nrocompetencia; ?>:
                  <?php
                  $c = new Sql('matriz_observados_nombre_competencias');
                  $c->where('id', $nrocompetencia);
                  $nombrecomp = $c->fetch('field:nombre');
                  echo $nombrecomp[0];
                  ?></b>
                  <h5 class="text-center">Nivel Observado en esta competencia:<b><?php echo $value[0]; ?></b></h5>
                </th>
              </tr>
              <tr>
                  <td class="text-center">
                    <?php foreach ($niveles as $nivel_nombre) { ?>
                    <!-- NIVELES -->
                    <?php // echo "<b>" . $nivel_nombre->nivel_nombre . "</b><br>"; ?>
                      <?php
                      // MARCADORES
                      foreach ($marcadores[$nrocompetencia] as $marcador) {
                        if($marcador[0] == $nivel_nombre->id) {
                          $c = new Sql('matriz_observados');
                          $c->where('id', $marcador[1]);
                          $nombremarcador = $c->fetch('field:marcador');
                          echo  $nombremarcador[0];
                        }
                      }
                      ?>
                    <?php } ?>
                  </td>
              </tr>
              <tr>
                <td colspan="<?php echo count($niveles); ?>">
                  <h5> <b> Comentario del profesor:</b></h5>
                  <p><?php echo utf8_decode($fundamentacion[$nrocompetencia][0]); ?></p>
                </td>
              </tr>
              <?php
            }
          }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <td class="success" colspan="<?php echo count($niveles); ?>">
              <h5 class="commentbox text-center"><b>Observación Final: </b></h5>
            </td>
          </tr>
          <tr>
            <td>
              <p name="observacionfinal" class="commentbox text-center" ><?php echo utf8_decode($observacionfinal);?></p>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="row text-center">
  <div class="col-md-8 col-md-offset-2 observacionfinal">
      <b>Revisado por: </b><?php echo $_SESSION['nombre']." ".$_SESSION['apellido'];?><br>
      <b>Email: </b><?php echo $_SESSION['email'];?><br>
      <b>Fecha: </b><?php echo date("d/m/Y H:i");?><br>
      <hr>
  </div>
</div>
<?php
$matrizobsevados = ob_get_clean();
// echo $matrizobsevados;
$sql = "UPDATE ca_requisitos_certificacion
        SET fbtexto ='$matrizobsevados', fbfecha = '$date', estado = $idestado
        WHERE id = $idobservado";
$guardar = Sql::doSql($sql);

$estado = "";
if ($estadoobservado=recuperar_estado_requisito_certificacion($idestado)) {
    $estado = strtoupper($estadoobservado->estado);
}

$c = new Sql('ca_requisitos_certificacion');
$c->select('idalumno');
$c->where('id', $idobservado); //CAMBIAR POR ID ALUMNO
$idalumnoobservado = $c->fetch(1);

$c = new Sql('jos_users');
$c->where('id', $idalumnoobservado->idalumno); //CAMBIAR POR ID ALUMNO
$usuario = $c->fetch(1);

// Enviar email al alumno
$de         = "no_responder@axont.com";
$password   = "I*2016nueva";
$denombre   = "Secretaria Academica";
$enviara    = $usuario->email;
$cc         = "coordinador.academico@axont.com";
$cc1        = "";
$cco        = "larry.ojeda@axont.com";
$asunto     = "Coaching Observado Revisado";
if($_POST["idestado"] == 3){
    $cuerpo     = "Estimado Alumno ".$usuario->name."<br>"
        ."Antes que nada aprovechamos la oportunidad para felicitarte por enviar tu OBSERVADO<br>"
        ."y estar comprometido con tu propia certificacion como COACH ONTOLÓGICO PROFESIONAL<br>"
        ."En esta oportunidad tu COACHING OBSERVADO fue revisado y por el momento<br>"
        ."no cumple con los estándares requeridos por Axon e ICF, por lo tanto su estado es <b>".$estado."</b>.<br><br>"
        ."Podés ver el feedback (desde la plataforma, en tu panel, donde figura tu nombre y apellido, en <i>mi certificacion</i>,<br>"
        ."Un boton azul al lado de <i>Observado: Volver a presentar</i>) para sacar provecho en esta etapa de tu <br>"
        ."aprendizaje y seguir practicando! Confiamos en que todos tenemos la capacidad para realizarlo, VOS PODES! VAMOS!<br><br>"
        ."En el caso que tengas dudas respecto a el feedback y/o necesitas asistencia podes contactarte con nosotros<br>"
        ."A certificacion@axont.com para que te acompañemos en este proceso.<br><br>"
        ."Ademas te invitamos a los talleres de práctica para poder sumar herramientas.<br>"
        ."Los días y horarios los podrás encontrar en el muro Publico<br><br>"
        ."Saludos<br>"
        ."Secretaría Académica";
}else{
    $cuerpo     = "Estimado Alumno ".$usuario->name."<br>"
            ."Antes que nada aprovechamos la oportunidad para felicitarte por enviar tu OBSERVADO<br>"
            ."y estar comprometido con tu propia certificacion como COACH ONTOLÓGICO PROFESIONAL<br>"
            ."Te informamos que tu Coaching Observado ha sido revisado y su estado es <b>".$estado."</b>. <br>"
            ."Puedes ver el feedback desde la plataforma, tu panel, donde figura tu nombre y apellido - Status certificación.<br>"
            ."Ademas te invitamos a los talleres de práctica para poder sumar herramientas.<br>"
            ."Los días y horarios los podrás encontrar en el muro Publico<br><br>"
            ."Saludos<br>"
            ."Secretaría Académica";
}

$respondera = "secretaria.academica@axont.com";
$res=enviarEmailSMTP($de,$password,$denombre,$enviara,$cc,$cc1,$cco,$asunto,$cuerpo,$respondera,$aAdjuntos);

// Si el estado es Aprobado,
// Genera un mensaje en el muro del alumno o en el muro de 20 lecciones!
if($idestado == 2){
  $errorstatus = "";

  // Verifica si existe el status certificacion para este alumno
  if ($statuscert = recuperar_status_certificacion($usuario->id)) {

      // Actualiza la tabla status_certificacion
      if(Usuario::get_id_formacion_actual($usuario->id) >= 286){
          $campos = array("observado", "his_observado","tesina");
          $valores = array("Aprobado", $matrizobsevados,"Aprobado");

      }else{
          $campos = array("observado", "his_observado");
          $valores = array("Aprobado", $matrizobsevados);

      }

      $condicion = "id=".$statuscert->id;
      if (!Actualizar_Registros($jcxn, "status_certificacion", $campos, $valores, $condicion, 0)) {
          $errorstatus = "<p>ERROR 1: NO se pudo actualizar el Status de Certificación</p>";
      }
  }
  else {
      // Agrega el status_certificacion para este alumno
      $campos = array("user_id", "coloquio", "tesina", "observado", "his_observado");
      $valores = array($usuario->id,"Pendiente", "Pendiente", "Aprobado", $matrizobsevados);
      if (!Insertar_Un_Registro($jcxn, "status_certificacion", $campos, $valores, 0)) {
          $errorstatus = "<p>ERROR 2: NO se pudo crear el Status de Certificación</p>";
      }
  }

  if ($errorstatus=="") {
      // Establece el comentario para el post y los correos a los companeros
      $nombrealumno= $usuario->name;
      $comentario = "Felicitamos al Alumno ".$nombrealumno." porque su Coaching Observado ha sido Aprobado en el dia de hoy. <br> "
                   ."Informamos que el Coaching Observado ha sido revisado por un COACH y su estado es: <b>".$estado."</b> <br> "
                   ."Felicitaciones ".$nombrealumno;

      // Publica post en el muro 20L
      include_once('/var/www/scripts/classes/muro.class.php');
      Post::inserta_post($comentario, 17, 964);

      // Enviar Post a Twitter
      //$twit  = "El Alumno ".$nombrealumno." ha aprobado su Coaching Observado. Felicitaciones!";
      //postear_un_twit($twit);

      //Envia email a Agencia Social Media
      $userid= $usuario->id;
      $asunto= "Observado Aprobado";
      $comentario= "El Alumno ".$nombrealumno." ha aprobado su Coaching Observado. Felicitaciones!";
      Social::enviar_post($userid, $asunto, $comentario);

      // Verifica si completo los requisitos de Certificacion. Si Certifico => Envia mensaje al alumno felicitandolo
      // y a los companeros y publica post en los muros de los cursos donde estuvo
      $resultado = completo_requisitos_certificacion($usuario->id);
      if ($resultado<>"") {
          echo $resultado;
          exit();
      }
  }
  else {
      echo $errorstatus."<br>AVISE a Secretaria Academica de este error. Gracias.";
      exit();
  }
} elseif ($idestado==3) {
  agregar_alumno_lote($usuario->id, 17, "20L", 26090, date('Y-m-d', strtotime("+2 days")), "admin");
}

// Vuelve a tesinas_profesores
echo "<script language='javascript' type='text/javascript'>window.location.href ='?page=/b/observados_profesores';</script> ";

break;
}
