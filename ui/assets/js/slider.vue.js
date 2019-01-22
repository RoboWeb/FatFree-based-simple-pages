(function(){
  "use strict";

  var slider = new Vue({
    el: "#homeSlider",
    data: {
      activeSlideIndx: -1,
      slidesList: '',
      delay: 10000
    },
    watch: {
      'activeSlideIndx': function(nv, ov) {
        var pIndx = ov < 0 ? this.slidesList.length - 1 : ov;

        this.slidesList[pIndx].classList.remove('active');
        this.slidesList[nv].classList.add('active');
      }
    },
    ready: function() {
      this.slidesList = this.$el.getElementsByClassName('slide');
      this.activeSlideIndx = 0;
      this.startRotating();

    },
    methods: {
      setActiveSlide: function(i){
        clearInterval(this.intervalID); this.startRotating();
        this.activeSlideIndx = i;
      },
      nextSlide: function(e){
        if(e !== 'undefined') { clearInterval(this.intervalID); this.startRotating(); }
        if(this.activeSlideIndx + 1 === this.slidesList.length) this.activeSlideIndx = 0;
        else this.activeSlideIndx++;
      },
      prevSlide: function(e){
        if(e !== 'undefined') { clearInterval(this.intervalID); this.startRotating(); }
        if(this.activeSlideIndx - 1 < 0) this.activeSlideIndx = this.slidesList.length - 1;
        else this.activeSlideIndx--;
      },
      startRotating: function(){
        this.intervalID = setInterval(this.nextSlide, this.delay);
      }
    }
  });
// slider.$mount("#homeSlider");
})();
