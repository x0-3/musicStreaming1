import Like from "./like.js";

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

      console.log("handleResponse - response = ", response);
      console.log("handleResponse - response.idComment = ", response.idComment);

      handleAddEventListenerOnCommentDeleteButton(document.getElementById(`comment-${response.idComment}`));
    break; 
    
    // FIXME: make it not redirect to the json endpoint
    // if the comment has been successfully deleted then remove the comment from the comment list
    // case 'COMMENT_DELETED_SUCCESSFULLY':

    //   // Get the comment element by its ID or any other unique identifier
    //   let commentId = response.commentId;
    //   let commentElement = document.getElementById(commentId);
    //   if (commentElement) {
    //     commentElement.remove();
    //   }
    
    // break;  
  }
}


function handleAddEventListenerOnCommentDeleteButton(elementComment) {
  elementComment.addEventListener('click', function (e) {
  
    e.preventDefault(); // doens't send data 

    let url = elementComment.getAttribute('delete-url');
    deleteComment(url);

    console.log('url = ', url);
  })
}


function deleteComment(url) {

  $.ajax({

    type: "POST",
    url: url
  })

  .done(function (data) {

    console.log('data = ', data);

    // let id = JSON.parse(data).id;
    let id = data.id;

    console.log('id = ', id);

    $('#comment-' + id).remove();
    // document.getElementById(`comment-${id}`).remove();
  })

  .fail(function () {

    alert('Could not be deleted');
  });
}



// FIXME: make it not redirect to the json endpoint
$(document).ready(function () {

  // // console.log('$(".deletebtn") = ', $('.deletebtn'));

  // // $('.deletebtn').on('click', function (e) {

  // let elementsComments = document.querySelectorAll(".deletebtn");

  // console.log("elementsComments = ", elementsComments);

  // // $('.deletebtn').forEach(elementComment => {
  // elementsComments.forEach(elementComment => {
  //   // elementComment.on('click', function (e) {
  //   elementComment.addEventListener('click', function (e) {
    
  //     e.preventDefault(); // doens't send data 

  //     // let url = $(this).attr('delete-url');
  //     let url = elementComment.getAttribute('delete-url');
  //     deleteComment(url);

  //     console.log('url = ', url);
  //   // });
  //   })
  // });

  let elementsComments = document.querySelectorAll(".deletebtn");

  elementsComments.forEach(function(elementComment) {
    handleAddEventListenerOnCommentDeleteButton(elementComment);
  });

});
