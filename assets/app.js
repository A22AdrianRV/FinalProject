/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

let select = document.getElementById("form_types_Type1");
let select2 = document.getElementById("form_types_Type2");
select.options.selectedIndex = 0;
select2.options.selectedIndex = 0;
select2.parentElement.style.display = "none";

select.parentElement.addEventListener("input", (e) => {
  if (e.target.options.selectedIndex != 0) {
    select2.parentElement.style.display = "";
  } else {
    select2.parentElement.style.display = "none";
    select2.options.selectedIndex = 0;
  }
});


