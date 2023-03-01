import noUiSlider from 'nouislider';

let form;
let resultZone;

/*
function onRangeChange(range){
  const wrapper = range.closest('.rangeWrapper');

  if (!wrapper) {
    return;
  }

  const min = wrapper.querySelector('.values .min');
  const max = wrapper.querySelector('.values .max');
  const value = wrapper.querySelector('.range .value');

  min.textContent = range.min;
  max.textContent = range.max;

  if (range.value === range.min || range.value === range.max){
    value.textContent = '';
  } else {
    value.textContent = range.value;
    value.style.left = calculateThumbPosition(range) + 'px'
  }

}
 */

function onRangeChange(range) {
  const wrapper = range.closest('.rangeWrapper');
  const rangeSlider = range;

  if (!wrapper) {
    return;
  }
  const rangeBullet = wrapper.querySelector('.range .value');

  rangeBullet.innerHTML = rangeSlider.value;

  var bulletPosition = (rangeSlider.value /rangeSlider.max);
  let thumbPos = calculateThumbPosition(rangeSlider);
  console.group('Thumb Calculation')
  console.debug(bulletPosition)
  console.debug(thumbPos);
  console.groupEnd();
  //rangeBullet.style.left = (bulletPosition * (608-20)) + "px";
  rangeBullet.style.left = thumbPos+"px";
}

function calculateThumbPosition(range) {
  const value = range.value;
  const totalInputWidth = range.clientWidth;

  const thumbHalfWidth = 5
  const left = (((value - range.min) / (range.max - range.min)) * (totalInputWidth-(2*thumbHalfWidth)));

  return left;
}

function calculate(form) {
  const nbMonths = form.querySelector('[name="params[nbMois]"]')?.value
  const financement = form.querySelector('[name="params[financement]"]:checked')?.value
  const formData = new FormData(form);
  if(!formData.has('option_dac')){
    formData.append('option_dac', 'off');
  }

  const datas = Object.fromEntries(formData);

  fetch(form.action,{
    method:'post',
    body: JSON.stringify({
      'nbMois' : nbMonths,
      'financement': financement,
      'optionDAC' : datas.option_dac,
    })
  }).then(resp => resp.json())
    .then(json => {
      resultZone.innerHTML = json.html;
    })
}

function onDomLoaded(){

  form = document.querySelector('form#calculator');
  resultZone = document.querySelector('#planFinancement')

  const range = form.querySelector('input[type="range"]');
  if (range) {
    range.addEventListener('change', function (event) {
      onRangeChange(event.target);
    });
  }

  form.addEventListener('change', function(event){
    calculate(form);
  });

  document.querySelectorAll(`input[form="${form.id}"]`).forEach((input) => {
    input.addEventListener('change', function(event) {
      calculate(form);
    })
  })

  calculate(form);

  if (range) {
    onRangeChange(range);
  }

}

document.addEventListener('DOMContentLoaded', onDomLoaded)