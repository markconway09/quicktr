<div class="container border border-secondary my-4 p-4 text-bg-dark rounded">
    <div id="cards-container">
        <div class="spinner mx-auto" id="cards-container">
            <div class="spinner1"></div>
        </div>
    </div>
</div>


<script>
    const cards = [];
    const pasos = ["Diagnóstico", "Aprobación", "Reparación", "Terminado", "Entregado"];
    const pasosLargo = ["Espera del diagnóstico", "Espera aprobación del cliente", "En Reparación", "Reparación terminada", "Entregado al cliente"];
    const colores = ["rgba(245, 66, 84, 0.65)", "rgba(232, 163, 26, 0.65)", "rgba(47, 133, 44, 0.65)", "rgba(68, 114, 196, 0.65)", "rgba(173, 173, 173, 0.65)"];
    $(document).ready(function() {
        // Function to make an AJAX call and fetch data
        $.ajax({
            url: 'controller/ajax_query.php', // The PHP file that returns data
            type: 'GET', // Method of the request
            data: { call: 2 },
            dataType: 'json', // Expected data type (JSON)
            success: function(data){
                displayCards(data);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
        });

        function displayCards(data) {
            const container = document.getElementById('cards-container');
            container.innerHTML = ""; // Clear loading text
            
            let row; // Wrapper div for rows

            data.forEach((item, index) => {
                // Create a new row every 3 cards
                if (index % 3 === 0) {
                    row = document.createElement('div');
                    row.className = "row mb-3"; // Adds spacing between rows
                    container.appendChild(row);
                }

                // Create individual card
                const card = document.createElement('div');
                card.className = "listCard col-lg-4 col-12"; // Adjusted to fit 3 cards per row on large screens
                card.style = "background-color:" + colores[item.estado];
                var cardhtml = `
                    <div class="text">
                        <span class="text-center">` + item.servicio.split(":")[0] + ` # ` + item.id + `</span>
                        <span class="subtitle text-center">` + item.fecha + ` · ` + item.local + `</span>
                        <hr>
                        <div class="subtitle">
                            <table class="w-100 table-sm mb-0">
                                <tr>
                                    <td><b>Nombre</b></td>
                                    <td>` + item.nombre + `</td>
                                </tr>
                                <tr>
                                    <td><b>Dispositivo</b></td>
                                    <td>` + item.nombre_dispositivo + `</td>
                                </tr>
                                <tr>
                                    <td><b>Descripción</b></td>
                                    <td>` + truncateString(item.desc, 40) + `</td>
                                </tr>
                                <tr>
                                    <td><b>Precio</b></td>
                                    <td>` + item.precio + `€ (+ IVA ` + item.iva + `%) = ` + item["precio-final"] + `€</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <span class="subtitle text-center fw-bold">` + pasosLargo[item.estado] + ` (` + (item.estado + 1) + `/5)</span>
                    <div class="card__progress">
                        <progress max="5" value="` + (item.estado + 1) + `"></progress>
                    </div>
                    
                    <div class="icons">
                        <a href="controller/execute.php?estado=` + (item.estado - 1) + `&id=` + item.id + `&pag=0" class="btns text-dark" type="button">
                            <span><i class="bi bi-arrow-left"></i></span>
                        </a>
                        <a href="controller/execute.php?estado=` + (item.estado + 1) + `&id=` + item.id + `&pag=0" class="btns text-dark" type="button">
                            <span><i class="bi bi-arrow-right"></i></span>
                        </a>
                        <a href="?pag=list&id=` + item.id + `" class="btns text-dark" type="button">
                            <span><i class="bi bi-info-circle"></i></span>
                        </a>
                    </div>
                `;
                cardhtml = cardhtml.replace("null", "No especificado");
                card.innerHTML = cardhtml;
                row.appendChild(card);
            });
        }

        
    });

    function truncateString(string, limit) {
        if (string.length > limit) {
            return string.substring(0, limit) + "..."
        } else {
            return string
        }
    }
</script>