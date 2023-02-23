/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// start the Stimulus application
import './bootstrap';

import * as Bs from 'bootstrap';


function toggleActive(toggledBox) {
  const card = toggledBox.querySelector('.card');
  const button = toggledBox.querySelector('.btn');
  const cardHeader = toggledBox.querySelector('.card-header');

  card.classList.toggle('border-primary');

  cardHeader.classList.toggle('border-primary');
  cardHeader.classList.toggle('text-bg-primary');

  button.classList.toggle('btn-primary');
  button.classList.toggle('btn-outline-primary');

  toggledBox.classList.toggle('active');
}

/*
document.querySelectorAll('.pricingBox').forEach((box) => {
  box.addEventListener('click', function(event) {
    const targetBox = event.currentTarget;
    const currentBox = document.querySelectorAll('.pricingBox.active')
    if (currentBox.length) {
      currentBox.forEach(toggleActive);
    }
    toggleActive(targetBox);
  });
})
 */

