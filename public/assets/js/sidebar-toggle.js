let sidebar = document.querySelector(".sidebar");
let sidebarOpenBtn = document.querySelector(".sidebar-open-button");

function sidebarToggle() {
  sidebar.classList.toggle("active");
  sidebarOpenBtn.style.visibility = "hidden";
}

function sidebarClose() {
  sidebar.classList.toggle("active");
  sidebarOpenBtn.style.visibility = "visible";
}

function subMenuToggle(id) {
  let subMenu = document.getElementById(`sub-menu-${id}`);
  let dropArrow = document.getElementById(`droparrow-${id}`);

  subMenu.classList.toggle("active");
  dropArrow.classList.toggle("rotate");
}
