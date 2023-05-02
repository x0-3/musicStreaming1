// ******************************************* Toggle light and dark mode ********************************************************************* //
function myFunction() {
  var element = document.body;
  element.classList.toggle("dark");
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

// ******************************************* audio player **************************************************************** * //

const playBtn = document.querySelector("#mainPlayBtn");
const audio = document.querySelector("#audio");
const btnPrev = document.querySelector("#btnPrev");
const btnNext = document.querySelector("#btnNext");
// const trackTitle = document.querySelector(".trackTitle");
// const ArtistName = document.querySelector(".ArtistName");
// const cover = document.querySelector(".cover");
const slider = document.querySelector(".slider");
const thumb = document.querySelector(".slider-thumb");
const progress = document.querySelector(".progress");
const time = document.querySelector(".time");
const fulltime = document.querySelector(".fulltime");
const volumeSlider = document.querySelector(".volume-slider .slider");
const volumeProgress = document.querySelector(".volume-slider .progress");
const volumeIcon = document.querySelector(".volume-icon");


let trackPlaying = false;

// let volumeMuted = false;

let trackId = 0; 

// TODO: make it dinamic with AJAX
const tracks = [
  "Aaliyah - Giving You More",
  "Ella Mai - Good Bad",
  "Aaliyah - Giving You More"
];

// play /pause 
playBtn.addEventListener("click", playTrack);

function playTrack(){

  // if the track is not playing
  if(trackPlaying === false){

    // play the audio
    audio.play();

    // change the button with a pause icon
    playBtn.innerHTML = `
    <span class="fa-solid fa-pause"></span>
    `;

    // set it to true
    trackPlaying = true;
  }else{ //else if the track is already playing then

    // stop the audio
    audio.pause();

    // change the button with a play icon
    playBtn.innerHTML = ` <span class="fa-solid fa-circle-play"></span> `;

    // set it to false
    trackPlaying = false;

  }
}

// switch songs
function switchtrack(){

  // if the audio is playing 
  if(trackPlaying === true){

    // then keep it playing
    audio.play();

  }
}

// get track source
const trackScr = "assets/music/" + tracks[trackId] + ".mp3";

function loadTrack(){

  // set the audio track source
  audio.scr = "assets/music/" + tracks[trackId] + ".mp3";

  // console.log(audio);

  // reload the track
  audio.load();


  progress.style.width = 0;
  thumb.style.left = 0;

  // wait for the song to load
  audio.addEventListener('loadedData', () => {

    // display the duration of the song
    setTime(fulltime, audio.duration);

    // set the max value to the slider
    slider.setAttribute("max", audio.duration);
  });
}

loadTrack();

// play previous track
btnPrev.addEventListener('click', () => {

  trackId--;

  // if the track id is negative  
  if (trackId < 0) {

    // go back to the last song
    trackId = tracks.length - 1;
  }

  loadTrack();

  switchtrack();

});

// switch to next song
btnNext.addEventListener("click", nextTrack);

function nextTrack(){

  trackId++;

  if (trackId > tracks.length - 1) {

    // set the id to 0
    trackId = 0;
  }
    
  loadTrack();

  switchtrack();

}


audio.addEventListener('ended', nextTrack);

// music time 
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


// music slider
function customSlider(){

  // calculate the % 
  const val = (slider.value / audio.duration) * 100 + "%";

  // console.log(audio.duration);

  // set the progress of the song
  progress.style.width = val;
  thumb.style.left = val;

  // show the cuurent time of the song
  setTime(time, slider.value);

  // set it to the slider 
  audio.currentTime = slider.value;
}

customSlider();

slider.addEventListener("input", customSlider);


// volume control
let volume = document.getElementById('volume-slider');
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