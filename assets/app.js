// any CSS you import will output into a single css file (app.css in this case)
import './styles/sass/app.css';

// start the Stimulus application
import './bootstrap.js';


import Like from "./js/Like.js";
import Favorite from "./js/Favorite.js";

// ********************************************* Like functionnality *****************************************************************//
// execute function when the DOM has finished loading
document.addEventListener('DOMContentLoaded', () => {

  // Like

  // get the array of likes 
  // []. slice.call to get the functionnality of an array
  const likeElements = []. slice.call(document.querySelectorAll('a[data-action="like"]'));

  // check if it was successful fetched
  if (likeElements) {

    // then make a new like element imported from the javascript script
    new Like(likeElements);
  }


  const favoriteElements = []. slice.call(document.querySelectorAll('a[data-action="favorite"]'));
    
  if (favoriteElements) {
    
    new Favorite(favoriteElements);
  }
});


// ******************************************* Toggle light and dark mode ********************************************************************* //
(function() {
  let onpageLoad = localStorage.getItem("theme") || "light";
  let element = document.body;
  element.classList.add(onpageLoad);
  document.getElementById("theme").textContent =
    localStorage.getItem("theme") || "light";
})()


// we user window. because the onClick attribute it's defined in the global scope 
// we need it to be defined in the global scope in order for the function to be found and called
window.toggleTheme = function() {
  let element = document.body;
  element.classList.toggle("dark");

  let theme = localStorage.getItem("theme");
  if (theme && theme === "dark") {
    localStorage.setItem("theme", "light");
  } else {
    localStorage.setItem("theme", "dark");
  }

  document.getElementById("theme").textContent = localStorage.getItem("theme");
}


// ********************************************** Hamburger menu ****************************************************************** * // 
/* Set the width of the sidebar to 250px (show it) */
window.openNav = function() {
  document.getElementById("mySidepanel").style.width = "250px";
}

/* Set the width of the sidebar to 0 (hide it) */
window.closeNav = function() {
  document.getElementById("mySidepanel").style.width = "0";
}

// ********************************************** Modal ****************************************************************** * // 

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on close btn, close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
