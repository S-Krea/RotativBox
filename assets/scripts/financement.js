function onDomLoaded(){
  const form = document.querySelector('form#calculator');
  const resultZone = document.querySelector('#planFinancement')

  form.addEventListener('change', function(event){
    calculate(form);
  });

  function calculate(form) {
    const nbMonths = form.querySelector('[name="params[nbMois]"]')?.value
    const financement = form.querySelector('[name="params[financement]"]:checked')?.value

    fetch(form.action,{
      method:'post',
      body: JSON.stringify({
        'nbMois' : nbMonths,
        'financement': financement,
      })
    }).then(resp => resp.json())
      .then(json => {
        resultZone.innerHTML = json.html;
      })
  }

  calculate(form);
}

document.addEventListener('DOMContentLoaded', onDomLoaded)