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


// ********************************************** Hamburger menu ****************************************************************** * // 
const menu = document.querySelector('.openbtn');
const closeElem =  document.querySelector('.closebtn');

menu.addEventListener('click', () => {

  document.getElementById("mySidepanel").style.width = "250px";
  
} )

closeElem.addEventListener('click', () => {
  document.getElementById("mySidepanel").style.width = "0";
  
} )

// ********************************************** Modal ****************************************************************** * // 

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the element that closes the modal
var span = document.getElementsByClassName("close")[0];

if (modal) {

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
}




//*********************************************** copy link to clipboard ******************************************************//
let copyBtn = document.getElementById("copyBtn");

if (copyBtn) {

  copyBtn.addEventListener('click', () => {
  
    // Get the text field
    var copyLink = document.getElementById("copyLink");
  
    // Select the text field
    copyLink.select();
    copyLink.setSelectionRange(0, 99999); // For mobile devices
  
    // Copy the text inside the text field
    navigator.clipboard.writeText(copyLink.value);
  
  })
}

//*********************************************** parallax **********************************************************//

const parallax = document.querySelector(".parallax");

if(parallax){

  document.addEventListener("scroll", function() {
    let offset = window.pageYOffset;
    parallax.style.transform = `translateY(-${offset * 0.8}px)`;
  });
}

//*********************************************** dynamique user form **********************************************************//

var rolesField = document.getElementById('edit_user_roles');

if(rolesField){

  // Add an event listener to the roles field
  document.addEventListener('DOMContentLoaded', function() {
    rolesField.addEventListener('change', toggleFields);
  });
  
  // Function to toggle the 'poster' and 'bio' fields based on the selected role
  function toggleFields() {
    var rolesField = document.getElementById('edit_user_roles');
    var selectedRoles = Array.from(rolesField.selectedOptions).map(function(option) {
        return option.value;
    });
  
  var artistFormElem = document.getElementById('artistForm');
  
    if (selectedRoles.includes('ROLE_ARTIST')) {
      artistFormElem.style.display = 'block';
    } else {
      artistFormElem.style.display = 'none';
    }
  }
  
  // Call the toggleFields function initially to set the initial state
  toggleFields();
}

//*********************************************** subscription sideNav **********************************************************//

let openSidebar = document.getElementById('btn');
let closeSidebar = document.getElementById('close');
let sidebar = document.getElementById('subscriptions');

if(openSidebar){

  openSidebar.onclick = function() {
  sidebar.classList.toggle('active');
  }
}

if(closeSidebar){

  closeSidebar.onclick = function() {
    sidebar.classList.toggle('active');
  }
}