// ******************************************* Toggle light and dark mode ********************************************************************* //
(function() {
  let onpageLoad = localStorage.getItem("theme") || "light";
  let element = document.body;
  element.classList.add(onpageLoad);
  document.getElementById("theme").textContent =
    localStorage.getItem("theme") || "light";
})()

function myFunction() {
  var element = document.body;
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
function openNav() {
  document.getElementById("mySidepanel").style.width = "250px";
}

/* Set the width of the sidebar to 0 (hide it) */
function closeNav() {
  document.getElementById("mySidepanel").style.width = "0";
}

// ********************************************* Like icons *****************************************************************//
// const likeIcon = document.querySelector(".like");

// let heartfull = false

// likeIcon.addEventListener("click", () => {

//   if (heartfull === false) {
//     likeIcon.innerHTML = `<i class="fa-solid fa-heart"></i>`;

//     heartfull = true;
    
//   }else{
//     likeIcon.innerHTML = `<i class="fa-regular fa-heart"></i>`;

//     heartfull = false;
//   }
// })



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
    break; 
    
    // if the comment has been successfully deleted then remove the comment from the comment list
    case 'COMMENT_DELETED_SUCCESSFULLY':

      // Get the comment element by its ID or any other unique identifier
      var commentId = response.commentId;
      var commentElement = document.getElementById(commentId);
      if (commentElement) {
        commentElement.remove();
      }
    
    break; 
  }
}

