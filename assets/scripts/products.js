function onDomLoaded() {
  const divProduct = document.querySelector('#products');
  const url = divProduct.dataset['fetchUrl']
  if (!url) {
    return;
  }

  function fetchProducts(nextUrl) {
    fetch(url,{
      method: 'post',
      headers: {
        'Content-Type': 'application/json;charset=utf-8'
      },
      body: JSON.stringify({nextUrl})
    })
      .then((response) => response.json())
      .then((json) => {
        divProduct.insertAdjacentHTML('beforeend', json.html);
        if (json.next !== null) {
          fetchProducts(json.next);
        }
      })
  }


  fetch(url)
    .then((response) => response.json())
    .then((json) => {
      divProduct.innerHTML = json.html
      if (json.next !== null) {
        fetchProducts(json.next);
      }
    })
}


document.addEventListener('DOMContentLoaded', onDomLoaded);