const btn = document.getElementById("btn-back-to-top");
btn.style.display = "none";
const footerOffset = 300;
window.addEventListener("scroll", () => {
    const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    // Mostrar si se bajó más de 200px y no estamos en los últimos 500px
    if (scrollTop > 200 && scrollTop + windowHeight < documentHeight - footerOffset) {
        btn.classList.add("btn-back-to-top")
    } else {
        btn.style.display = "none";
    }
});

// Volver arriba suave
btn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
});