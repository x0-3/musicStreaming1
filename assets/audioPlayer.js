const playBtn = document.querySelector("#mainPlayBtn");
const audio = document.querySelector("#audio");
const btnPrev = document.querySelector("#btnPrev");
// const btnNext = document.querySelector("#btnNext");
const trackTitle = document.querySelector(".trackTitle");
const ArtistName = document.querySelector(".ArtistName");
const cover = document.querySelector(".cover");
const slider = document.querySelector(".slider");
const thumb = document.querySelector(".slider-thumb");
const progress = document.querySelector(".progress");
const time = document.querySelector(".time");
const fulltime = document.querySelector(".fulltime");
const volumeSlider = document.querySelector(".volume-slider .slider");
const volumeProgress = document.querySelector(".volume-slider .progress");
const volumeIcon = document.querySelector(".volume-icon");

let trackPlaying = true;

let volumeMuted = false;

// let trackId = 0; 

// const tracks = [];

// ************************************************ toggle play ************************************************//

window.togglePlay = function() {
  var playButton = document.getElementById("mainPlayBtn");

  if (audio.paused) {
      audio.play();
      playButton.innerHTML = '<span class="fa-solid fa-pause"></span>';
  } else {
      audio.pause();
      playButton.innerHTML = '<span class="fa-solid fa-circle-play"></span>';
  }
}


// ************************************************ switch song ************************************************//
function switchtrack(){

  // if the audio is playing 
  if(trackPlaying === true){

    // then keep it playing
    audio.play();

  }
}


// ************************************************ track source ************************************************//

// const trackScr = "uploads/music/" + tracks[trackId];
// // console.log(trackScr);

// function loadTrack(){

//   // set the audio track source
//   audio.scr = trackScr;

//   progress.style.width = 0;
//   thumb.style.left = 0;

//   // wait for the song to load
//   audio.addEventListener('loadedData', () => {

//     // display the duration of the song
//     setTime(fulltime, audio.duration);

//     // set the max value to the slider
//     slider.setAttribute("max", audio.duration);
//   });
// }

// loadTrack();

// ************************************************ switch prev ************************************************//
// btnPrev.addEventListener('click', () => {

//   trackId--;

//   // if the track id is negative  
//   if (trackId < 0) {

//     // go back to the last song
//     trackId = tracks.length - 1;
//   }

//   loadTrack();

//   switchtrack();

// });

// ************************************************ switch next ************************************************//
// btnNext.addEventListener("click", nextTrack);

// function nextTrack(){

//   trackId++;

//   if (trackId > tracks.length - 1) {

//     // set the id to 0
//     trackId = 0;
//   }
    
//   loadTrack();

//   switchtrack();

// }

// audio.addEventListener('ended', nextTrack);

// ************************************************ music time ************************************************//
audio.onloadedmetadata = function() {
  
  
  function setTime(output, input){
  
    // calculate the minutes
    const minutes = Math.floor(input / 60);
    
    // calculate the seconds
    const seconds = Math.floor(input % 60);
  
    // if seconds is under 10
    if (seconds < 10) {
      
      // show a zero 
      output.innerHTML = minutes + ":0" + seconds;
  
    }else{
  
      // show the :
      output.innerHTML = minutes + ":" + seconds;
    }
  }
  
  setTime(fulltime, audio.duration);

  // current time on slider 
  audio.addEventListener('timeupdate', () => {
    
    // get the current audio time
    const currentAudioTime = Math.floor(audio.currentTime);


    // get the percentage
    const timePercentage = (currentAudioTime / audio.duration) * 100 + "%";

    // set the current time of the song
    setTime(time, currentAudioTime);

    // set the slider to the time
    progress.style.width = timePercentage;
    thumb.style.left = timePercentage;

  });


  // ************************************************ music slider ************************************************//
  function customSlider(){

    // calculate the % 
    const val = (audio.currentTime / audio.duration) * 100;

    // set the progress of the song
    progress.style.width = val + "%";
    slider.style.left = val + "%";
    
    // show the current time of the song
    setTime(time, slider.value);
    
    // set it to the slider 
    audio.currentTime = slider.value;

    // update the display of the time
    setTime(time, audio.currentTime);
  }

  customSlider();


  // ************************************************ move the slider with music ************************************************//

  // set the initial value and max value of the range slider
  slider.value = 0; //do not uncomment

  slider.max = audio.duration;

  // add an event listener to the audio element to update the range slider value as the song plays
  audio.addEventListener('timeupdate', () => {
    slider.value = audio.currentTime;
  });

  // add an event listener to the range slider input element to seek to a specific time in the song
  slider.addEventListener('input', () => {
    // audio.currentTime = rangeSlider.value;
    audio.currentTime = slider.value;
  });

  // update the progress bar as the song plays
  audio.addEventListener('timeupdate', () => {

    const progressPercent = (audio.currentTime / audio.duration) * 100;
    setTime(time, audio.currentTime);
    progress.style.width = `${progressPercent}%`;
    thumb.style.left = `${progressPercent}%`;
  });

};


// ************************************************ volume control ************************************************//
let volume = document.getElementById('volume-slider');

audio.volume = 0.3; // set the initial volume to 50%

volume.addEventListener("change", function(e) {
  audio.volume = e.currentTarget.value / 100;


  // if the volume is high
  if (audio.volume > 0.5) {
  
    // change the icon 
    volumeIcon.innerHTML = `<i class="fa-solid fa-volume-high"></i>`;

  } else if (audio.volume === 0){

    // mute icon
    volumeIcon.innerHTML = `<i class="fa-solid fa-volume-xmark"></i>`;

  } else{

    // volume low
    volumeIcon.innerHTML = `<i class="fa-solid fa-volume-low"></i>`;

  }
});


function toggleMute(){

  // if the volume is not muted
  if (volumeMuted === false) {
    
    // then change the icon
    volumeIcon.innerHTML = `<i class="fa-solid fa-volume-xmark"></i>`;
    audio.volume = 0; // set the volume to 0

    volumeMuted = true; // set the variable to true

  } else {

    // then change the icon
    volumeIcon.innerHTML = `<i class="fa-solid fa-volume-low"></i>`;
    audio.volume = 0.3; // set the volume

    volumeMuted = false; // set the variable to false
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

// Function to handle the click event on comment delete buttons
function handleAddEventListenerOnCommentDeleteButton(deleteButtonElement) {
  deleteButtonElement.addEventListener('click', function (e) {
  
    e.preventDefault(); // Prevent the default behavior of the click event

    // Get the URL from the delete button
    let url = deleteButtonElement.getAttribute('delete-url');
    deleteComment(url); // Call the deleteComment function with the comment URL

  });
}

function deleteComment(url) {

  $.ajax({

    type: "POST", // method used to delete the comment
    url: url // Specify the URL of the comment to be deleted
  })

  .done(function (data) {

    // Once the comment is successfully deleted

    // Get the ID of the deleted comment
    let id = data.id;

    // Remove the comment from the page by selecting its ID
    $('#comment-' + id).remove();
  })

  .fail(function () {

    alert('Could not be deleted');
  });
}


$(document).ready(function () {
  // When the document is ready

  // Get all the delete button elements from the HTML
  let deleteButtonElements = document.querySelectorAll(".deletebtn");

  // Attach a click event listener to each delete button element
  deleteButtonElements.forEach(function(elementComment) {
    handleAddEventListenerOnCommentDeleteButton(elementComment);
  });

});


// ************************************************ keybinds ************************************************//
// TODO: keybindings for pause button, next button, previous button and mute button

document.addEventListener("keydown", e => {

  e.preventDefault();

  if (e.key.toLowerCase() === "k" && e.shiftKey) {
    togglePlay();
  }
  
  if (e.key.toLowerCase() === "m" && e.shiftKey) {
    toggleMute();
  }

});







  


  
  
  
  