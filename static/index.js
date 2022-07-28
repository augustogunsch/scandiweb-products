const productsList = document.getElementById('products');

const loadItems = () => {
	const xhttp = new XMLHttpRequest();

	xhttp.onload = function() {
		const products = JSON.parse(this.responseText);

		const boxes = products.map(product =>
			`<div class="product">
				<input type="checkbox" class="delete-checkbox" value="${product.id}">
				<p>
					${product.sku}<br>
					${product.name}<br>
					${product.price} $<br>
					${product.attribute}
				</p>
			 </div>`
		)

		productsList.innerHTML = boxes.join('\n');
	}

	xhttp.open('GET', 'products', true);
	xhttp.send();
}
loadItems();

const deleteSelected = () => {
	const checkboxes = document.querySelectorAll('input[class="delete-checkbox"]:checked');
	let values = [];
	checkboxes.forEach(checkbox => values.push(checkbox.value));

	const xhttp = new XMLHttpRequest();

	xhttp.onload = function() {
		loadItems();
	}

	xhttp.open('DELETE', `products?id=${values.join(',')}`, true);
	xhttp.send();
}

const deleteButton = document.getElementById('delete-product-btn');
deleteButton.addEventListener('click', deleteSelected);
