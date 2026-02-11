<div class="col-md-6 col-lg-6 col-xl-6 mt-3">
    <div class="single-price-box">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="d-flex flex-column gap-4 justify-content-center align-items-center">
                <div class="d-flex flex-column justify-content-start align-items-start w-100">
                    <img style="filter: brightness(10.5);" src="{{ $icon }}" alt="{{ $name }}">
                </div>
                <div class="d-flex flex-column justify-content-start align-items-start w-100">
                    <h5 class="text-light">{{ $name }}</h5>
                    <p>{{ $description }}</p>
                </div>
            </div>
            <div class="d-flex flex-column gap-2 justify-content-center align-items-center">
                <div class="d-flex flex-column justify-content-end align-items-end w-100">
                    <h5 class="text-light">{{ $price }} USD</h5>
                    <p>{{ $billingPeriod }}</p>
                </div>
                <div class="d-flex flex-column justify-content-end align-items-end w-100">
                    <div class="btn-group me-2" style="height: 30px;" role="group" aria-label="Second group">
                        <button type="button" style="display: flex; justify-content: center; align-items: center; width: 20px; background: linear-gradient(-90deg, #0061ae 1.05%, #17255FFF 100%); " class="btn text-light " onclick="decreaseValue('{{ $id }}')">-</button>
                        <input type="number" class="border text-center" style="width: 50px;" id="quantityInput{{ $id }}" value="0" min="0" />
                        <button type="button" style="display: flex; justify-content: center; align-items: center; width: 20px; background: linear-gradient(-90deg, #0061ae 1.05%, #17255FFF 100%); " class="btn text-light" onclick="increaseValue('{{ $id }}')">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function increaseValue(id) {
        var input = document.getElementById('quantityInput' + id);
        var value = parseInt(input.value, 10);
        value = isNaN(value) ? 0 : value;
        input.value = value + 1;
    }

    function decreaseValue(id) {
        var input = document.getElementById('quantityInput' + id);
        var value = parseInt(input.value, 10);
        value = isNaN(value) ? 0 : value;
        if (value > 0) {
            input.value = value - 1;
        }
    }
</script>