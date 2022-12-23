// sidenav transition-burger

var sidenav = document.querySelector("aside");
var main = document.querySelector("main");
var sidenav_trigger = document.querySelector("[sidenav-trigger]");
var sidenav_close_button = document.querySelector("[sidenav-close]");
var burger = sidenav_trigger.firstElementChild;
var top_bread = burger.firstElementChild;
var bottom_bread = burger.lastElementChild;

sidenav_trigger.addEventListener("click", function () {
  console.log('s');
  if (page == "virtual-reality") {
    sidenav.classList.toggle("xl:left-[18%]");
  }
  sidenav_close_button.classList.toggle("hidden");

  main.classList.toggle("xl:ml-68.5");

  if (sidenav.classList.contains('xl:translate-x-0')) {
    sidenav.classList.toggle("xl:translate-x-0");
  } else {
    sidenav.classList.toggle("translate-x-0");
    sidenav.classList.toggle("shadow-soft-xl");
  }
  if (page == "rtl") {
    top_bread.classList.toggle("-translate-x-[5px]");
    bottom_bread.classList.toggle("-translate-x-[5px]");
  } else {
    top_bread.classList.toggle("translate-x-[5px]");
    bottom_bread.classList.toggle("translate-x-[5px]");
  }
});
sidenav_close_button.addEventListener("click", function () {
  sidenav_trigger.click();
});

window.addEventListener("click", function (e) {
  if (!sidenav.contains(e.target) && !sidenav_trigger.contains(e.target)) {
    if (sidenav.classList.contains("translate-x-0")) {
      sidenav_trigger.click();
    }
  }
});
