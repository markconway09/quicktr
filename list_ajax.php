<div class="container border border-secondary my-4 p-4 text-bg-dark rounded">
    <div class="row mb-2">
        <div class="col-12">
            <div class="row">
                <div class="col-12 my-2">
                    <div class="searchBox mb-1">
                        <input onkeyup="search(this.value)" onkeydown="search(this.value)" class="searchInput w-100" type="text" name="search" placeholder="Buscar... (Dispositivo, Id, Servicio...)">
                        <button class="searchButton" type="submit">
                            <i class="bi bi-search" style="font-size: large;"></i>
                        </button>
                    </div>
                    <?php if (isset($_POST["search"])) { ?>
                        <a href="list" style="text-decoration: none; border-radius: 50px;" class="bg-secondary mx-auto logout noselect">
                            <span class="text">Quitar Filtro</span>
                            <span class="icon" style="border-color: white;">
                                <i class="text-light bi bi-x-circle"></i>
                            </span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div id="list-container">
        <div class="spinner mx-auto" id="list-container">
            <div class="spinner1"></div>
        </div>
    </div>
</div>

<script>
    var parameters = "";
    const iconos = ['search', 'person-raised-hand', 'tools', 'check-lg', 'person-fill-check'];
    const colores = ["#cb4351", "#ce9c3b", "#529651", "#4c6ca4", "#4a5467"];
    const pasos = ["Diagnóstico", "Aprobación", "Reparación", "Terminado", "Entregado"];
    const pasosLargo = ["Espera del diagnóstico", "Espera aprobación del cliente", "En Reparación", "Reparación terminada", "Entregado al cliente"];

    function search(param) {
        if(param == ""||param==" "){
            parameters = "";
        }else{
            parameters = param;
        }
        query();
    }

    function query() {
        // Function to make an AJAX call and fetch data
        $.ajax({
            url: 'controller/ajax_query.php', // The PHP file that returns data
            type: 'GET', // Method of the request
            data: {
                call: 2,
                search: parameters
            },
            dataType: 'json', // Expected data type (JSON)
            success: function(data) {
                displayList(data);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
        });
    }

    function displayList(data) {
        const container = document.getElementById('list-container');
        container.innerHTML = ""; // Clear loading spinner

        data.forEach((item) => {
            let pastDate = new Date(item.date);
            let now = new Date();
            let timeDiff = now - pastDate;
            let daysPassed = Math.floor(timeDiff / (1000 * 3600 * 24));
            if(daysPassed == 0){
                daysPassed = "hoy";
            } else if(daysPassed == 1){
                daysPassed = "ayer";
            } else daysPassed = `hace ${daysPassed} día(s)`;
            

            // Create individual list item
            const listItem = document.createElement('div');
            listItem.className = "list-item mb-2 p-2 border-none rounded";
            listItem.style = "background-color: " + colores[item.estado];

            var listItemHtml = `
                    <div class="d-flex justify-content-between" onclick="location.href='list&id=${item.id}'" style="cursor: pointer;">
                            <div class="p-1" style="border-right: 1px solid #fff">
                                <i style="padding: 0 15px 0 5px; font-size: 30px" class="bi bi-${iconos[item.estado]}"></i>
                            </div>
                        <div style="width:100%; padding-left:20px;">
                            <div>${item.nombre_dispositivo} - ${item.servicio.split(":"[0])}</div>
                            <div>${item.id} - ${item.nombre}</div>
                        </div>
                        <div style="width:50%; text-align: right; padding-right:10px;">
                            ${item.fecha}
                            <br>
                            ${daysPassed}
                        </div>
                    </div>
                `;

            listItem.innerHTML = listItemHtml;
            container.appendChild(listItem);
        });
    }
    $(document).ready(function() {
        query();
    });
</script>