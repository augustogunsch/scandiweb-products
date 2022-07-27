const productsList = document.getElementById('products');

const loadItems = () => {
	const xhttp = new XMLHttpRequest();

	xhttp.onload = function() {
		console.log(this.responseText);

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

	xhttp.open('GET', 'src/View/Product', true);
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

	xhttp.open('DELETE', 'src/View/Product', true);
	xhttp.send();
}

const deleteButton = document.getElementById('delete-product-btn');
deleteButton.addEventListener('click', deleteSelected);
