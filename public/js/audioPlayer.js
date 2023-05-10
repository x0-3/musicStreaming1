const playBtn = document.querySelector("#mainPlayBtn");
const audio = document.querySelector("#audio");
const btnPrev = document.querySelector("#btnPrev");
const btnNext = document.querySelector("#btnNext");
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

let trackId = 0; 

//FIXME: fetch element from database
const tracks = [];

// ************************************************ toggle play ************************************************//

function togglePlay() {
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

// FIXME: the trak is undefined
const trackScr = "uploads/music/" + tracks[trackId];
// console.log(trackScr);

function loadTrack(){

  // set the audio track source
  audio.scr = trackScr;

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

// ************************************************ switch prev ************************************************//
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

// ************************************************ switch next ************************************************//
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

// ************************************************ music time ************************************************//
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

slider.addEventListener("input", customSlider);


// Initialize the slider position

slider.addEventListener("input", () => {

  // Calculate the time based on the percentage value of the slider position
  const time = (slider.value / 100) * audio.duration;

  // Set the current time of the audio
  audio.currentTime = time;

  // Update the slider position
  customSlider();
});


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
  audio.currentTime = rangeSlider.value;
});

// update the progress bar as the song plays
audio.addEventListener('timeupdate', () => {

  const progressPercent = (audio.currentTime / audio.duration) * 100;
  setTime(time, currentAudioTime);
  progress.style.width = `${progressPercent}%`;
  sliderThumb.style.left = `${progressPercent}%`;
});



// ************************************************ volume control ************************************************//
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











// // ************************************************ volume control ************************************************//
// // set the initial volume to 50%
// audio.volume = 0.5;

// // add an event listener to the volume slider input element
// volumeSlider.addEventListener('input', () => {
//   // get the current value of the volume slider
//   const volume = volumeSlider.value;

//   // convert the volume to a decimal value between 0 and 1
//   const decimalVolume = volume / 100;

//   // set the volume of the audio element
//   audio.volume = decimalVolume;
// });


// // ************************************************ music slider ************************************************//

// function skipForward(url) {
//   let audio = document.getElementById('audio');
//   let slider = document.querySelector('.slider');
//   let progress = document.querySelector('.progress');
//   let playBtn = document.querySelector('#mainPlayBtn');

//   fetch(url)
//     .then(response => response.json())
//     .then(data => {
//       audio.src = data.link;
//       audio.play();
//       playBtn.innerHTML = '<span class="fa-solid fa-pause"></span>';

//       slider.value = 0;
//       progress.style.width = '0%';
//     })
//     .catch(error => console.error(error));
// }


// ************************************************ keybinds ************************************************//









  


  
  
  
  