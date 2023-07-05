// we want to export the class Like 
export default class Like {

    // list of the like in the current page
    constructor(likeElements){
  
        this.likeElements = likeElements

        if(this.likeElements){

            // call a method
            this.init();
        }
    }

    init(){

        // for each like element we map the element 
        this.likeElements.map(element => {

            // when the element is clicked we call a onClick method
            element.addEventListener('click', this.onClick);
        })
    }

    onClick(e){

        e.preventDefault(); // doesn't sent the info to data (remove the logic behind the element)
        const url = this.href; // get the url of the link

        // axios to get the url of a controller route
        axios.get(url).then(response => {

            // get the buttons
            const heartFilled = this.querySelector('.filled');
            const emptyHeart = this.querySelector('.unfilled');

            // toggle the display of the filled and empty buttons
            heartFilled.classList.toggle('d-none');
            emptyHeart.classList.toggle('d-none');
        })
    }
}