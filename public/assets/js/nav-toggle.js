function navToggle() {
  let icon = document.querySelector(".nav-toggle-icon");
  let nav = document.querySelector(".nav");

  icon.className =
    icon.className === "bi bi-list nav-toggle-icon"
      ? "bi bi-x-square nav-toggle-icon"
      : "bi bi-list nav-toggle-icon";

  nav.style.height = nav.style.height == "100vh" ? "64px" : "100vh";
}

function dropToggleNav(id) {
  let subMenuNav = document.getElementById(`sub-menu-${id}`);
  
  subMenuNav.classList.toggle("active");
}
