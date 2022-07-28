let currentTypeForm = document.getElementById($('#productType').val());
currentTypeForm.querySelectorAll('input').forEach(input => input.required = true);
currentTypeForm.classList.remove('hidden');

$('#productType').on('change', e => {
    const newTypeForm = document.getElementById(e.target.value);

    currentTypeForm.classList.add('hidden');
    newTypeForm.classList.remove('hidden');

    currentTypeForm.querySelectorAll('input').forEach(input => input.required = false);
    newTypeForm.querySelectorAll('input').forEach(input => input.required = true);

    currentTypeForm = newTypeForm;
});

$('#product_form').on('submit', e=> {
    e.preventDefault();

    $.ajax(
        'product',
        {
            method: 'POST',
            data: $('#product_form').serializeArray(),
            success: _ => window.location.href = '/',
            error: jqXHR => alert(jqXHR.responseText),
        }
    );
});
