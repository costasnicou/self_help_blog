const navbar = document.querySelector(".header-wraper");
// const topHeader = document.querySelector(".top-header");
const trigger = document.querySelector(".nav-trigger");
const main = document.querySelector(".site-main");
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) {
        navbar.classList.add("fixed-header");
        main.style.marginTop = "90px";


      } else {
        navbar.classList.remove("fixed-header");
        main.style.marginTop = "0";
      }
    });
  },
  { threshold: 0 } // triggers as soon as element leaves the viewport
);

observer.observe(trigger);
