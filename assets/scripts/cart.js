import {Alert} from "bootstrap";

document.addEventListener('DOMContentLoaded', onDomLoaded);

function showCartFlash(jsonReturn) {
  const flashesContainer = document.querySelector('.flashes');
  const status = jsonReturn.status;
  const message = jsonReturn.message;

  if (!flashesContainer) {
    alert(message);
  }

  const randomId = `alert${Math.floor(Math.random()*1000)}`;

  const htmlTemplate = `
    <div id="${randomId}" class="alert alert-${status} alert-dismissible fade show" role="alert">
        ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  `

  flashesContainer.insertAdjacentHTML('beforeend', htmlTemplate);
  setTimeout(() => {
    const alert = Alert.getOrCreateInstance(`#${randomId}`)
    alert.close();
  },3000);
}

function updateCartItems(jsonReturn) {
  const nbItems = jsonReturn.nbItems;
  const remainings = jsonReturn.remainings;
  document.querySelectorAll('.cart-count')
    .forEach((countSpan) => {
      countSpan.innerHTML = nbItems;
    });
  document.querySelectorAll('.cart-remainings').forEach(remainingSpan => {
    remainingSpan.innerHTML = remainings;
  })
}

function onDomLoaded() {
  document.querySelectorAll("[add-to-cart]").forEach(
    (addToCartButton) => {
      addToCartButton.addEventListener('click', async (event) => {
          event.preventDefault();
          const url = addToCartButton.getAttribute('href');
          const response = await fetch(url);
          const jsonReturn = await response.json();
          showCartFlash(jsonReturn);
          if (response.ok) {
            updateCartItems(jsonReturn);
          }
        }
      );
    }
  )
}