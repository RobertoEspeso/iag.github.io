
const links = document.querySelectorAll('.nav-link');

links.forEach(link => {
  link.addEventListener('click', function () {
    links.forEach(l => l.classList.remove('active'));
    this.classList.add('active');
  });
});

const activo = localStorage.getItem('activo')
if (activo) {
  const actual = document.querySelector(`.nav-link[href="${activo}"]`);
  if (actual) {
    actual.classList.add('active')
  }
}

links.forEach(link => {
  link.addEventListener('click', function () {
    localStorage.setItem('activo', this.getAttribute('href'))
  })
})
