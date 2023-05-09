var audio = document.getElementById("audio");
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

// ************************************************ volume control ************************************************//


// get the volume slider input element
const volumeSlider = document.getElementById('volume-slider');

// set the initial volume to 50%
audio.volume = 0.5;

// add an event listener to the volume slider input element
volumeSlider.addEventListener('input', () => {
  // get the current value of the volume slider
  const volume = volumeSlider.value;

  // convert the volume to a decimal value between 0 and 1
  const decimalVolume = volume / 100;

  // set the volume of the audio element
  audio.volume = decimalVolume;
});


// ************************************************ music slider ************************************************//

// get the range slider input element
const rangeSlider = document.querySelector('.slider');

// set the initial value and max value of the range slider
rangeSlider.value = 0;
rangeSlider.max = audio.duration;

// add an event listener to the audio element to update the range slider value as the song plays
audio.addEventListener('timeupdate', () => {
  rangeSlider.value = audio.currentTime;
});

// add an event listener to the range slider input element to seek to a specific time in the song
rangeSlider.addEventListener('input', () => {
  audio.currentTime = rangeSlider.value;
});

// update the progress bar as the song plays
const progress = document.querySelector('.progress');
const sliderThumb = document.querySelector('.slider-thumb');

audio.addEventListener('timeupdate', () => {
  const progressPercent = (audio.currentTime / audio.duration) * 100;
  progress.style.width = `${progressPercent}%`;
  sliderThumb.style.left = `${progressPercent}%`;
});


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









  


  
  
  
  