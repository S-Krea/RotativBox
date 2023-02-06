function onRangeChange(range){
  const wrapper = range.closest('.rangeWrapper');

  if (!wrapper) {
    return;
  }

  const min = wrapper.querySelector('.values .min');
  const max = wrapper.querySelector('.values .max');
  const value = wrapper.querySelector('.values .value');

  min.textContent = range.min;
  max.textContent = range.max;

  if (range.value === range.min || range.value === range.max){
    value.textContent = '';
  } else {
    value.textContent = range.value;
    value.style.left = calculateThumbPosition(range) + 'px'
  }

}

function calculateThumbPosition(range) {
  const value = range.value;
  const totalInputWidth = range.clientWidth;

  const thumbHalfWidth = 5
  const left = (((value - range.min) / (range.max - range.min)) * ((totalInputWidth - thumbHalfWidth) - thumbHalfWidth)) + thumbHalfWidth;

  return left;
}

function onDomLoaded(){
  const form = document.querySelector('form#calculator');
  const resultZone = document.querySelector('#planFinancement')

  const range = form.querySelector('input[type="range"]');

  range.addEventListener('change', function(event) {
    onRangeChange(event.target);
  });

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
  onRangeChange(range);
}

document.addEventListener('DOMContentLoaded', onDomLoaded)