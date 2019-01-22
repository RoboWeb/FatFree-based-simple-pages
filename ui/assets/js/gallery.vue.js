(function() {

  "use strict";

  var gallery = new Vue({
    el: '#gallery',
    data: {
      imgList: '',
      activeImgIndx: 0,
      firstVisibleImgIndx: 0,
      lastVisibleImgIndx: 5,
      imageMain: ''
    },
    computed: {
      src: function() {
        return this.imgList[this.activeImgIndx].lastElementChild.lastElementChild.src;
      }
    },
    watch: {
      'activeImgIndx': function(nv, ov) {
        this.imageMain.src = this.src;
      }
    },
    ready: function() {
      this.imgList = this.$el.getElementsByClassName('gallery-img');
      this.imageMain = document.getElementById('gallery-main-img');
    },
    methods: {
      showNextImg: function(e) {
        if(this.activeImgIndx + 1 === this.imgList.length) this.activeImgIndx = 0;
        else this.activeImgIndx++;
        if(this.activeImgIndx > this.lastVisibleImgIndx) {
          if(this.lastVisibleImgIndx + 6 >= this.imgList.length) this.lastVisibleImgIndx = this.imgList.length - 1;
          else this.lastVisibleImgIndx += 6;
          this.firstVisibleImgIndx = this.lastVisibleImgIndx - 5;
        } else if (this.activeImgIndx === 0) {
          this.firstVisibleImgIndx = 0;
          this.lastVisibleImgIndx = 5;
        }

      },
      showPrevImg: function(e) {
        if(this.activeImgIndx - 1 < 0) this.activeImgIndx = this.imgList.length - 1;
        else this.activeImgIndx--;
        if(this.activeImgIndx < this.firstVisibleImgIndx) {
          if(this.firstVisibleImgIndx - 6 < 0) this.firstVisibleImgIndx = 0;
          else this.firstVisibleImgIndx -= 6;
          this.lastVisibleImgIndx = this.firstVisibleImgIndx + 5;
        } else if (this.activeImgIndx === this.imgList.length - 1) {
          this.lastVisibleImgIndx = this.imgList.length - 1;
          this.firstVisibleImgIndx = this.lastVisibleImgIndx - 5;
        }
        // this.galleryMain.src = this.imgList[this.activeImgIndx].lastElementChild.lastElementChild.src;
      },
      setActiveImg: function(i) {
        this.activeImgIndx = i;
      }
    }
  });
})();
