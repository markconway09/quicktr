<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="favicon.ico" />
        <title>Orden de reparación</title>
        <!-- GFONTS -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <!-- BOOTSTRAP -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- JQUERY -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <!-- SIGNATURE JS -->
        <script src="jquery.signature.js"></script>
        <!-- SIGNATURE CSS -->
        <link href="jquery.signature.css" rel="stylesheet">
        <style>
        .kbw-signature { width: 300px; height: 200px; }
        </style>
        <style>
            body {
                font-family: "raleway";
                background-color: gray;
            }
        </style>
    </head>
    <body>
        <div class="container my-4">
            <form action="execute.php" target="_blank" method="POST" class="form-control p-4 text-bg-light">
                <div class="row mb-3 rounded p-3" style="background-color:rgb(43,45,46);">
                    <img src="LOGO.png" alt="logo" class="img-fluid mx-auto w-25">
                </div>
                <div class="row">
                    <h1 class="display-5 text-center mb-4">DATOS CLIENTE</h1>
                </div>
                <div class="row">
                    <div class="col-12 col-md-7 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Nombre" type="text" name="nombre" id="nombre">
                            <label for="nombre">Nombre</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="DNI/NIF/NIE" type="text" name="doc" id="doc">
                            <label for="doc">DNI/NIF/NIE</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-5 mb-3">
                        <div class="input-group">
                            <div class="form-floating">
                                <?php
                                include 'countrycodes.php';
                                ?>
                            </div>

                            <div class="form-floating">
                                <input class="form-control" placeholder="Teléfono" type="tel" name="tel" id="tel">
                                <label for="tel">Teléfono</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 mb-3">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Email" type="email" name="email" id="email">
                            <label for="email">Email</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php
                    if (!is_null($_SESSION["local"])) {
                    ?>
                        <input type="hidden" name="local" id="local" value="<?php echo $_SESSION["local"]; ?>" />
                    <?php
                    }
                    ?>
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <select class="form-control form-select" name="razon" id="razon">
                                <option value="Sin especificar" selected>No especificar</option>
                                <option value="Marketing/RSS">Marketing/Redes Sociales</option>
                                <option value="Maps">Google/Apple Maps</option>
                                <option value="Flyer">Flyer</option>
                                <option value="Retorno">Retorno de cliente</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <label for="razon">Como nos encontró</label>
                        </div>
                    </div>
                </div>

                <h1 class="display-5 text-center mb-4">DATOS DISPOSITIVO</h1>

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-floating">
                            <input class="form-control" placeholder="Dispositivo" type="text" name="dispositivo" id="dispositivo">
                            <label for="dispositivo">Dispositivo</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <textarea rows="14" style="height:100%;" class="form-control" placeholder="Descripción" name="motivo" id="motivo"></textarea>
                            <label for="motivo">Descripción</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-control">
                            <p>Firma</p>
                            <div id="sig"></div>
                            <p style="clear: both;">
                                <button class="btn btn-secondary btn-sm" id="clear">
                                    <span class="material-symbols-outlined pt-1">delete</span>
                                </button>
                                <span class="d-none fw-bold" id="borrar">Borrar</span>
                            </p>
                            <textarea name="sign" id="sign" style="display: none"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" name="guardar-servicio" class="btn btn-success btn-lg col-5 mx-auto" value="Enviar">
                </div>
            </form>
        </div>
    </body>
</html>
<script>
    var sig = $('#sig').signature({syncField: '#sign', syncFormat: 'PNG'});
    $('#clear').click(function(e) {
        e.preventDefault();
        sig.signature('clear');
        $("#sign").val('');
    });
    $('#clear').hover(
        function() {
            $('#clear').removeClass('btn-secondary').addClass('btn-danger');
            $('#borrar').removeClass('d-none');
        },
        function() {
            $('#clear').removeClass('btn-danger').addClass('btn-secondary');
            $('#borrar').addClass('d-none');
        }
    );
    // Initialize Signature Pad
    const canvas = document.getElementById('signature-canvas');
    const signaturePad = new SignaturePad(canvas);

    // Resize canvas for proper drawing
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // Clear the canvas to redraw
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    // Clear button functionality
    document.getElementById('clear').addEventListener('click', function() {
        signaturePad.clear();
    });

    // Save button functionality
    document.getElementById('save').addEventListener('click', function() {
        if (!signaturePad.isEmpty()) {
            const dataURL = signaturePad.toDataURL(); // Get the signature data URL
            document.getElementById('signature-data').value = dataURL; // Store it in a hidden field
            alert("Firma guardada! Puedes enviar los datos ahora.");
            // Log the data URL for demonstration
            console.log(dataURL);
        } else {
            alert("Por favor, proporciona una firma primero.");
        }
    });
</script>