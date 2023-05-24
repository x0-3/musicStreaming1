import Like from "./Like.js";
import Favorite from "./Favorite.js";

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

// ********************************************* add comment form *****************************************************************//
const formComment = document.querySelector('#commentForm'); 
const commentList = document.querySelector('.comments');

// listen for comment form submission
formComment.addEventListener('submit', function(e){

  e.preventDefault(); // doesn't send form to server

  // fetch the comment from server
  fetch(this.action, {
    body: new FormData(e.target), // get form data
    method: 'POST', // get method 

  })
  // the response is returned as a JSON object
  .then(response => response.json())
  
  // then the json is redirected to the the handleResponse function
  .then(json => {

    handleResponse(json);
  })

})


const handleResponse = function(response) {

  switch (response.code){

    // if the comment is successful then place the comment in the comment list
    case 'COMMENT_ADDED_SUCCESSFULLY':

      // insertAdjacentHTML allows you to insert the new comment to a specific position
      // afterbegin insert the new comment at the top of the comment list 
      commentList.insertAdjacentHTML('afterbegin', response.html); 

      // function to delete the comment from the comment list
      handleAddEventListenerOnCommentDeleteButton(document.querySelector(`#comment-${response.idComment} .deletebtn`));
    break; 
    
  }
}

// TODO: comment the code
function handleAddEventListenerOnCommentDeleteButton(deleteButtonElement) {
  deleteButtonElement.addEventListener('click', function (e) {
  
    e.preventDefault(); // doens't send data 

    let url = deleteButtonElement.getAttribute('delete-url');
    deleteComment(url);

  });
}

function deleteComment(url) {

  $.ajax({

    type: "POST",
    url: url
  })

  .done(function (data) {

    let id = data.id;


    $('#comment-' + id).remove();
  })

  .fail(function () {

    alert('Could not be deleted');
  });
}


$(document).ready(function () {

  let deleteButtonElements = document.querySelectorAll(".deletebtn");

  deleteButtonElements.forEach(function(elementComment) {
    handleAddEventListenerOnCommentDeleteButton(elementComment);
  });

});
