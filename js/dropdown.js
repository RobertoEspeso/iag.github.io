const dropdown = document.querySelector('.dropdown');
const selected = dropdown.querySelector('.dropdown-selected');
const options = dropdown.querySelectorAll('.dropdown-options li');
const hiddenSelect = dropdown.querySelector('.hidden-select');

selected.addEventListener('click', () => {
  dropdown.classList.toggle('open');
});

options.forEach(option => {
  option.addEventListener('click', () => {
    const value = option.getAttribute('data-value');
    const text = option.textContent;
    selected.textContent = text;
    hiddenSelect.value = value;
    dropdown.classList.remove('open');
  });
});

// Cerrar si se hace clic fuera
document.addEventListener('click', (e) => {
  if (!dropdown.contains(e.target)) {
    dropdown.classList.remove('open');
  }
});