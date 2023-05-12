// ******************************************* Toggle light and dark mode ********************************************************************* //
(function() {
  let onpageLoad = localStorage.getItem("theme") || "light";
  let element = document.body;
  element.classList.add(onpageLoad);
  document.getElementById("theme").textContent =
    localStorage.getItem("theme") || "";
})()

function myFunction() {
  var element = document.body;
  element.classList.toggle("dark");

  let theme = localStorage.getItem("theme");
  if (theme && theme === "dark") {
    localStorage.setItem("theme", "");
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
const likeIcon = document.querySelector(".like");

let heartfull = false

likeIcon.addEventListener("click", () => {

  if (heartfull === false) {
    likeIcon.innerHTML = `<i class="fa-solid fa-heart"></i>`;

    heartfull = true;
    
  }else{
    likeIcon.innerHTML = `<i class="fa-regular fa-heart"></i>`;

    heartfull = false;
  }
})



// ********************************************* comment form *****************************************************************//
const formComment = document.querySelector('form');
const CommentList = document.querySelector('.comments');

formComment.addEventListener("submit", function(e){

  e.preventDefault(); // doesn't send form to server

  fetch(this.action, {

    body: new FormData(e.target), // get form data
    method: 'POST',

  })

  .then(response => response.json())
  .then(json => {
    console.log(json);

  })

})


// TODO: finish functionality
const handleResponse = function(response) {

  switch (response.code){

    case 'COMMENT_ADDED_SUCCESSFULLY':
      CommentList.innerHTML += response.html;
      break;
    
  }
}