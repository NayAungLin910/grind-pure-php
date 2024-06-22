function dropdownToggle(id) {
    let dropSubMenu = document.querySelector(`#drop-sub-menu-${id}`);
    let dropArrow = document.querySelector(`#drop-arrow-${id}`);


    dropSubMenu.style.display = dropSubMenu.style.display == "block" ? "none" : "block";
    dropArrow.classList.toggle("rotate");
}