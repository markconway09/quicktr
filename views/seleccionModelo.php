<div class="col-12">
    <div class="row mb-3">
        <div class="col-lg-6 col-12">
            <div class="form-group">
                <div class="form-floating">
                    <select class="form-control form-select" id="deviceType" name="deviceType">
                        <option selected disabled>Seleccione un tipo de dispositivo</option>
                        <option value="Reparación Móvil">Móvil</option>
                        <option value="Reparación Ordenador">Ordenador</option>
                        <option value="Reparación Tablet">Tablet</option>
                        <option value="Reparación Consola">Consola</option>
                        <option value="Mantenimiento Otros">Mantenimiento Otros</option>
                    </select>
                    <label for="deviceType">Tipo de Dispositivo</label>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="form-group mb-2">
                <div class="form-floating">
                    <select class="form-control form-select" id="deviceBrand" name="deviceBrand">
                        <option selected disabled>Seleccione una marca</option>
                        <option value="Apple">Apple</option>
                        <option value="Huawei">Huawei</option>
                        <option value="Samsung">Samsung</option>
                        <option value="Xiaomi">Xiaomi</option>
                        <option value="Oppo">Oppo</option>
                        <option value="Other">Otro</option>
                    </select>
                    <label for="deviceBrand">Marca de Dispositivo</label>
                </div>
            </div>
            <div class="form-floating" id="otherDeviceInput" style="display: none;">
                <input type="text" class="form-control" id="dispositivo" name="otroDispositivo" placeholder="Dispositivo">
                <label for="dispositivo">Dispositivo</label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-6 col-12">
            <div class="form-group">
                <div class="form-floating">
                    <select class="form-control form-select" id="deviceModel" name="deviceModel">
                        <option value="">Seleccione un modelo</option>
                    </select>
                    <label for="deviceModel">Modelo de Dispositivo</label>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="form-group">
                <div class="form-floating">
                    <select class="form-control form-select" id="deviceSubModel" name="deviceSubModel">
                        <option value="">Seleccione un submodelo</option>
                    </select>
                    <label for="deviceSubModel">Submodelo de Dispositivo</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 text-light">
            <div class="form-floating" id="partsCheckboxes">
                <!-- Parts checkboxes will be appended here -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deviceTypeSelect = document.getElementById('deviceType');
        const deviceBrandSelect = document.getElementById('deviceBrand');
        const deviceModelSelect = document.getElementById('deviceModel');
        const deviceSubModelSelect = document.getElementById('deviceSubModel');
        const checkboxesContainer = document.getElementById('partsCheckboxes');
        const otherDeviceInput = document.getElementById('otherDeviceInput');

        // Initially hide all selects except deviceType and hide checkboxes
        deviceBrandSelect.style.display = 'none';
        deviceModelSelect.style.display = 'none';
        deviceSubModelSelect.style.display = 'none';
        checkboxesContainer.style.display = 'none';
        deviceBrandSelect.nextElementSibling.style.display = 'none';
        deviceModelSelect.nextElementSibling.style.display = 'none';
        deviceSubModelSelect.nextElementSibling.style.display = 'none';

        deviceTypeSelect.addEventListener('change', function() {
            const selectedType = deviceTypeSelect.value;

            if (selectedType === 'Reparación Móvil') {
            deviceBrandSelect.style.display = 'block';
            deviceModelSelect.style.display = 'block';
            deviceSubModelSelect.style.display = 'block';
            otherDeviceInput.style.display = 'none';
            deviceBrandSelect.nextElementSibling.style.display = 'block';
            deviceModelSelect.nextElementSibling.style.display = 'block';
            deviceSubModelSelect.nextElementSibling.style.display = 'block';
            } else {
            deviceBrandSelect.style.display = 'none';
            deviceModelSelect.style.display = 'none';
            deviceSubModelSelect.style.display = 'none';
            checkboxesContainer.style.display = 'none';
            otherDeviceInput.style.display = 'block';
            deviceBrandSelect.nextElementSibling.style.display = 'none';
            deviceModelSelect.nextElementSibling.style.display = 'none';
            deviceSubModelSelect.nextElementSibling.style.display = 'none';
            }
        });

        deviceBrandSelect.addEventListener('change', function() {
            const selectedBrand = deviceBrandSelect.value;

            if (selectedBrand === 'Other') {
                otherDeviceInput.style.display = 'block';
                deviceModelSelect.style.display = 'none';
                deviceSubModelSelect.style.display = 'none';
                checkboxesContainer.style.display = 'none';
                deviceModelSelect.nextElementSibling.style.display = 'none';
                deviceSubModelSelect.nextElementSibling.style.display = 'none';
            } else {
                otherDeviceInput.style.display = 'none';
                deviceModelSelect.style.display = 'block';
                deviceSubModelSelect.style.display = 'block';
                deviceModelSelect.disabled = false;
                deviceSubModelSelect.disabled = true;
                checkboxesContainer.style.display = 'none';
                deviceModelSelect.innerHTML = '<option selected disabled>Seleccione una marca</option>';

                if (selectedBrand) {
                    $.ajax({
                        url: `controller/fetch_modelos.php?brand_name=${selectedBrand}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            data.forEach(function(model) {
                                const option = document.createElement('option');
                                option.value = model.modelo;
                                option.textContent = model.modelo;
                                deviceModelSelect.appendChild(option);
                            });
                        }
                    });
                }
            }
        });

        deviceModelSelect.addEventListener('change', function() {
            const selectedModel = deviceModelSelect.value;

            // Enable deviceSubModelSelect if a model is selected
            deviceSubModelSelect.disabled = false;
            checkboxesContainer.style.display = 'none';
            deviceSubModelSelect.innerHTML = "<option value=''>Seleccione un modelo</option>";

            if (selectedModel) {
                $.ajax({
                    url: `controller/fetch_modelos.php?model_name=${selectedModel}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        data.forEach(function(subModel) {
                            const option = document.createElement('option');
                            option.value = subModel.id;
                            option.textContent = subModel.submodelo;
                            deviceSubModelSelect.appendChild(option);
                        });
                    }
                });
            }
        });

        deviceSubModelSelect.addEventListener('change', function() {
            const selectedSubModel = deviceSubModelSelect.value;
            const selectedBrand = deviceBrandSelect.value;
            const selectedModel = deviceModelSelect.value;

            // Show checkboxes if a submodel is selected
            checkboxesContainer.style.display = selectedSubModel ? 'block' : 'none';

            // Create or update hidden input for deviceName
            let deviceNameInput = document.getElementById('deviceName');
            if (!deviceNameInput) {
                deviceNameInput = document.createElement('input');
                deviceNameInput.type = 'hidden';
                deviceNameInput.id = 'deviceName';
                deviceNameInput.name = 'deviceName';
                document.querySelector('form').appendChild(deviceNameInput);
            }
            deviceNameInput.value = `${selectedBrand} ${selectedModel} ${deviceSubModelSelect.options[deviceSubModelSelect.selectedIndex].text}`;

            if (selectedSubModel) {
                $.ajax({
                    url: `controller/fetch_modelos.php?submodel_id=${selectedSubModel}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Clear existing checkboxes
                        checkboxesContainer.innerHTML = '';

                        data.forEach(function(part) {
                            if (part.original) {
                                const checkboxDiv = document.createElement('div');
                                checkboxDiv.className = 'form-check form-check-inline';

                                const checkbox = document.createElement('input');
                                checkbox.className = 'form-check-input';
                                checkbox.type = 'checkbox';
                                checkbox.id = part.id;
                                checkbox.name = 'deviceServices[]';
                                checkbox.value = part.id;
                                checkbox.dataset.cost = part.original; // Store cost in data attribute

                                const label = document.createElement('label');
                                label.className = 'form-check-label';
                                label.textContent = part.nombre + ' (Original)' + ' - ' + part.original;

                                checkboxDiv.appendChild(checkbox);
                                checkboxDiv.appendChild(label);
                                checkboxesContainer.appendChild(checkboxDiv);

                                checkbox.addEventListener('change', function() {
                                    const cost = parseFloat(this.dataset.cost);
                                    if (this.checked) {
                                        updatePriceFromCheckbox(cost);
                                        addHiddenCostInput(this.id, cost);
                                    } else {
                                        updatePriceFromCheckbox(-cost);
                                        removeHiddenCostInput(this.id);
                                    }
                                });
                            }
                            if (part.compatible) {
                                const checkboxDiv = document.createElement('div');
                                checkboxDiv.className = 'form-check form-check-inline';

                                const checkbox = document.createElement('input');
                                checkbox.className = 'form-check-input';
                                checkbox.type = 'checkbox';
                                checkbox.id = part.id;
                                checkbox.name = 'deviceServices[]';
                                checkbox.value = part.id;
                                checkbox.dataset.cost = part.compatible; // Store cost in data attribute

                                const label = document.createElement('label');
                                label.className = 'form-check-label';
                                label.htmlFor = part.id;
                                label.textContent = part.nombre + ' (Compatible)' + ' - ' + part.compatible;

                                checkboxDiv.appendChild(checkbox);
                                checkboxDiv.appendChild(label);
                                checkboxesContainer.appendChild(checkboxDiv);

                                checkbox.addEventListener('change', function() {
                                    const cost = parseFloat(this.dataset.cost);
                                    if (this.checked) {
                                        updatePriceFromCheckbox(cost);
                                        addHiddenCostInput(this.id, cost);
                                    } else {
                                        updatePriceFromCheckbox(-cost);
                                        removeHiddenCostInput(this.id);
                                    }
                                });
                            }
                            if (part.incel) {
                                const checkboxDiv = document.createElement('div');
                                checkboxDiv.className = 'form-check form-check-inline';

                                const checkbox = document.createElement('input');
                                checkbox.className = 'form-check-input';
                                checkbox.type = 'checkbox';
                                checkbox.id = part.id;
                                checkbox.name = 'deviceServices[]';
                                checkbox.value = part.id;
                                checkbox.dataset.cost = part.incel; // Store cost in data attribute

                                const label = document.createElement('label');
                                label.className = 'form-check-label';
                                label.htmlFor = part.id;
                                label.textContent = part.nombre + ' (Incel)' + ' - ' + part.incel;

                                checkboxDiv.appendChild(checkbox);
                                checkboxDiv.appendChild(label);
                                checkboxesContainer.appendChild(checkboxDiv);

                                checkbox.addEventListener('change', function() {
                                    const cost = parseFloat(this.dataset.cost);
                                    if (this.checked) {
                                        updatePriceFromCheckbox(cost);
                                        addHiddenCostInput(this.id, cost);
                                    } else {
                                        updatePriceFromCheckbox(-cost);
                                        removeHiddenCostInput(this.id);
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    });

    function updatePriceFromCheckbox(cost) {
        window.opener.updatePriceFromCheckbox(cost);
    }

    function addHiddenCostInput(id, cost) {
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'deviceServiceCosts[]';
        hiddenInput.id = 'cost_' + id;
        hiddenInput.value = cost;
        document.querySelector('form').appendChild(hiddenInput);
    }

    function removeHiddenCostInput(id) {
        let hiddenInput = document.getElementById('cost_' + id);
        if (hiddenInput) {
            hiddenInput.remove();
        }
    }
</script>