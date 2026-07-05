const b = document.body;
b.dataset.theme = localStorage.getItem("theme") ?? b.dataset.theme;

function toggleTheme() {
  b.dataset.theme = b.dataset.theme === "light" ? "dark" : "light";
  localStorage.setItem("theme", b.dataset.theme);
}


document.querySelector('[aria-label="Open Nide Bar"]').addEventListener('click', function (e) {
  this.classList.toggle('rotate-button');
  document.querySelector('nav').classList.toggle('open');
  document.querySelector('aside').classList.toggle('open');
})
const logoutLink = document.querySelector('a[data-i18n="logout"]');

logoutLink?.addEventListener("click", async (e) => {
  e.preventDefault();

  try {
    const response = await fetch("/doc/api/logout.php", { method: "POST" });
    const res = await response.text();
    if (response) {
      setTimeout(() => {
        location.reload();
      }, 500);
    }
  } catch (err) {
    alert("Request Not found try agin");
  }
});
