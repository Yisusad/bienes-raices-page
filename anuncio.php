<?php 

    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id){
        header('Location: /');
    }

    //Importar BD
    require 'includes/config/database.php';
    $db = conectarDB();
    //Consultar
    $query = "SELECT * FROM propiedades WHERE id = ${id}";
    
    //Obtener Resultados
    $resultado = mysqli_query($db, $query);

    //Redirecciona si el id no es el correcto
    if($resultado->num_rows === 0){
        header('Location: /');
    }

    $propiedad = mysqli_fetch_assoc($resultado);

    require 'includes/funciones.php';   
    incluirTemplate('header');
?>

    <main class="contenedor seccion contenido-centrado">
        <h1><?php echo $propiedad['titulo']; ?></h1>

         <img src="/imagenes/<?php echo $propiedad['imagen']; ?>" alt="">
  

        <div class="resumen-propiedad">
            <p class="precio">$<?php echo $propiedad['precio']; ?></p>
            <ul class="iconos-caracteristicas">
                <li>
                    <img loading="lazy" src="build/img/icono_wc.svg" alt="icono wc">
                    <p><?php echo $propiedad['wc']; ?></p>
                </li>
                <li>
                    <img loading="lazy" src="build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                    <p><?php echo $propiedad['estacionamiento']; ?></p>
                </li>
                <li>
                    <img loading="lazy" src="build/img/icono_dormitorio.svg" alt="icono dormitorio">
                    <p><?php echo $propiedad['habitaciones']; ?></p>
                </li>
            </ul>
            <?php echo $propiedad['descripcion']; ?>
        </div>
    </main>

    <?php 
        mysqli_close($db);
        incluirTemplate('footer');
    ?>