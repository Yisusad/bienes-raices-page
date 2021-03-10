<?php 

    //Base de datos

    require '../../includes/config/database.php';

    $db = conectarDB();

    //Arreglo para mensajes de errores
    $errores = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //echo "<pre>";
        //var_dump($_POST);
        //echo "</pre>";

        $titulo = $_POST['titulo'];
        $precio = $_POST['precio'];
        $descripcion = $_POST['descripcion'];
        $habitaciones = $_POST['habitaciones'];
        $wc = $_POST['wc'];
        $estacionamiento = $_POST['estacionamiento'];
        $vendedorId = $_POST['vendedor'];

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

 
        //Revisar que el arreglo de errores esté vacio para poder insertar en la BD
        if(empty($errores)){
        
        //Insertar a BD
        $query = "INSERT INTO propiedades (titulo, precio, descripcion, habitaciones, wc, estacionamiento, vendedorId) VALUES ( '$titulo',
        '$precio', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$vendedorId' )";

        //echo $query;
        $resultado = mysqli_query($db, $query);

        if($resultado){
            echo "Insertado correctamente";
        }
        }

    }

    require '../../includes/funciones.php';
    
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>

        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form action="/admin/propiedades/crear.php" class="formulario" method="POST">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título de propiedad">

                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" placeholder="Precio de propiedad">

                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png">

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"></textarea>
            </fieldset>

            <fieldset>
                <legend>Información de Propiedad</legend>

                <label for="habitaciones">Habitaciones</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9">

                <label for="wc">Baños</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9">

                <label for="estacionamiento">Estacionamiento</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9">               
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="">-- Seleccione --</option>
                    <option value="1">Juan</option>
                    <option value="2">Karen</option>
                </select>
            </fieldset>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>

    <?php incluirTemplate('footer'); ?>