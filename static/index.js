const loadItems = () => {
    $.ajax(
        'product',
        {
            success: data => {
                const boxes = data.map(product =>
                    `<div class="product">
                        <input type="checkbox" class="delete-checkbox" value="${product.id}">
                        <p>
                            ${product.sku}<br>
                            ${product.name}<br>
                            ${product.price} $<br>
                            ${product.attribute}
                        </p>
                     </div>`
                );

                $('#products').html(boxes.join('\n'));
            },
            error: jqXHR => alert(jqXHR.responseText),
        }
    )
}

loadItems();

$('#delete-product-btn').on('click', () => {
    let values = [];
    const checkboxes = document.querySelectorAll('input[class="delete-checkbox"]:checked');
    checkboxes.forEach(checkbox => values.push(checkbox.value));

    $.ajax(
        `product?id=${values.join(',')}`,
        {
            method: 'DELETE',
            success: loadItems,
            error: jqXHR => alert(jqXHR.responseText),
        }
    )
});
