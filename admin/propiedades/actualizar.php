<?php 

    //Validar la url por id válido
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id){
        header('location: /admin');
    }
    //Base de datos
    require '../../includes/config/database.php';
    $db = conectarDB();

    //Obtener datos de la Propiedad
    $consulta = "SELECT * FROM propiedades WHERE id = ${id}";
    $resultado = mysqli_query($db, $consulta);
    $propiedad = mysqli_fetch_assoc($resultado);

    //Consultar todos los vendedores
    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

    //Arreglo para mensajes de errores
    $errores = [];

    $titulo = $propiedad['titulo'];
    $precio = $propiedad['precio'];
    $descripcion = $propiedad['descripcion'];
    $habitaciones = $propiedad['habitaciones'];
    $wc = $propiedad['wc'];
    $estacionamiento = $propiedad['estacionamiento'];
    $vendedorId = $propiedad['vendedorId'];
    $imagenPropiedad = $propiedad['imagen'];
    

    if($_SERVER['REQUEST_METHOD'] === 'POST'){


        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";

        // echo "<pre>";
        // var_dump($_FILES);
        // echo "</pre>";

        //se usa mysqli_real*** para sanitizar y evitar injecciones
        $titulo = mysqli_real_escape_string( $db, $_POST['titulo'] );
        $precio = mysqli_real_escape_string( $db, $_POST['precio'] );
        $descripcion = mysqli_real_escape_string( $db, $_POST['descripcion'] );
        $habitaciones = mysqli_real_escape_string( $db, $_POST['habitaciones'] );
        $wc = mysqli_real_escape_string( $db, $_POST['wc'] );
        $estacionamiento = mysqli_real_escape_string( $db, $_POST['estacionamiento'] );
        $vendedorId = mysqli_real_escape_string( $db, $_POST['vendedor'] );
        $creado = date('Y/m/d');

        //asigna FILES a una variable
        $imagen = $_FILES['imagen'];

        if(!$titulo){
            $errores[]= "Tienes que añadir un título";
        }
        if(!$precio){
            $errores[]= "Tienes que agregar un precio";
        }
        if( strlen($descripcion) < 50 ){
            $errores[]= "Tienes que agregar una descripción con al menos 50 caracteres";
        }
        if(!$habitaciones){
            $errores[]= "Tienes que añadir la cantidad de habitaciones";
        }
        if(!$wc){
            $errores[]= "Tienes que añadir la cantidad de baños";
        }
        if(!$estacionamiento){
            $errores[]= "Tienes que añadir la cantidad de estacionamientos";
        }
        if(!$vendedorId){
            $errores[]= "Tienes que seleccionar un vendedor";
        }

        //validar por tamaño (1 mb máximo)
        $medida = 1000 * 1000;

        if($imagen['size'] > $medida) {
            $errores[]= "La imagen es muy pesada";
        }

 
        //Revisar que el arreglo de errores esté vacio para poder insertar en la BD
        if(empty($errores)){

            //Subir Imagenes

            //Crear carpeta
            $carpetaImagenes = '../../imagenes/';

            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes);
            }

            $nombreImagen = '';
            //Verifica si hay imagen
            if($imagen['name']){
                //Si hay imagen se elimina la anterior    
                unlink($carpetaImagenes . $propiedad['imagen']);

                //Generar nombre unico para imagenes
                $nombreImagen = md5(uniqid(rand(), true)). ".jpg";

                //subiendo imagen
                move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
     
            }else {
                //Si no hay imagen previa
                $nombreImagen = $propiedad['imagen'];
            }
            
            //Insertar a BD
            $query = "UPDATE propiedades SET titulo = '${titulo}', precio = '${precio}', imagen = '${nombreImagen}', descripcion = '${descripcion}', habitaciones = ${habitaciones}, 
            wc = ${wc}, estacionamiento = ${estacionamiento}, vendedorId = ${vendedorId} WHERE id = ${id}";
            //echo $query;
            $resultado = mysqli_query($db, $query);

            if($resultado){
                //echo "Insertado correctamente";
                //Se redirecciona para evitar entradas masivas

                header('Location: /admin?resultado=2');
            }
        }

    }

    require '../../includes/funciones.php';
    
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Actualizar Propiedad</h1>

        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título de propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" placeholder="Precio de propiedad" value="<?php echo $precio; ?>">

                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png">
                <img src="/imagenes/<?php echo $imagenPropiedad ?>" class="imagen-small">

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información de Propiedad</legend>

                <label for="habitaciones">Habitaciones</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">

                <label for="wc">Baños</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento; ?>">               
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="">-- Seleccione --</option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                        <option <?php echo $vendedorId == $vendedor['id'] ? 'selected' : ''; ?> 
                         value="<?php echo $vendedor['id']; ?>"><?php echo $vendedor['nombre']. " " .$vendedor['apellido']; ?></option>
                    <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>
    </main>

    <?php incluirTemplate('footer'); ?>