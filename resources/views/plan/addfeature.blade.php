<tr>
    <td class="text-start">
        <span class="fw-bold">{{ __($name) }}</span><br>
        <span><small>{{ $per ?? '' }}</small></span>
    </td>
    <td class="text-center">
        <div class="btn-group" style="height: 30px;" role="group">
            <button type="button" class="btn btn-primary" style="display: flex; justify-content: center; align-items: center; width: 20px;" onclick="decreaseValue('{{ $id }}', {{ $price }})">-</button>
            <input type="text" name="{{ $name }}"  class="border text-center" style="width: 50px;" id="quantityInput{{ $id }}" value="0" min="0" onchange="calculateTotalPrice('{{ $id }}', {{ $price }})" readonly />
            <button type="button" class="btn btn-primary" style="display: flex; justify-content: center; align-items: center; width: 20px;" onclick="increaseValue('{{ $id }}', {{ $price }})">+</button>
        </div>
    </td>
    <td class="text-end">
        <h5><span id="price_{{ $id }}">{{ $price }}</span> <small class="text-muted">USD</small></h5>

    </td>
</tr>

<script>
    function increaseValue(id, price) {
        var input = document.getElementById('quantityInput' + id);
        var totalInput = document.getElementById('total_price_input');

        var value = parseInt(input.value, 10);
        value = isNaN(value) ? 0 : value;
        input.value = value + 1;

        // تحديث السعر الإجمالي
        totalInput.value = (parseFloat(totalInput.value) || 0) + price;
        updateTotalPrice();
    }

    function decreaseValue(id, price) {
        var input = document.getElementById('quantityInput' + id);
        var totalInput = document.getElementById('total_price_input');

        var value = parseInt(input.value, 10);
        value = isNaN(value) ? 0 : value;
        if (value > 0) {
            input.value = value - 1;
            totalInput.value = (parseFloat(totalInput.value) || 0) - price;
            updateTotalPrice();
        }
    }

    function calculateTotalPrice(id, price) {
        var input = document.getElementById('quantityInput' + id);
        var totalInput = document.getElementById('total_price_input');

        var quantity = parseInt(input.value, 10);
        quantity = isNaN(quantity) ? 0 : quantity;

        totalInput.value = quantity * price;
        updateTotalPrice();
    }

    function updateTotalPrice() {
        var totalPriceElement = document.getElementById('total_price');
        var totalInput = document.getElementById('total_price_input');
        var paytab_total_price = document.getElementById('paytab_total_price');

        paytab_total_price.value = totalInput.value;

        totalPriceElement.innerHTML = totalInput.value;
    }
</script>
