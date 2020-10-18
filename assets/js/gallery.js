var gallery = {
  item_template : 
        '<div class="image-holder loading" style="padding-bottom: %padding%%;" data-imgidx="%imgidx%">' +
            '<span class="loader loader-black"></span>' +
            '<img src="%imgsrc%" data-modalimage="%modalimage%" data-bigimage="%bigimage%" alt="%alttext%"  />' +
        '</div>',

  gallery : null,
  modal : null,
  modal_image: null,
  modal_caption: null,
  modal_close: null,
  load_more_requested: false,
  hash : {},
  perload : {},
  columns : {},
  col_heights : {1:0, 2:0, 3:0},
  images : {},
  pointer : 0,
  enqueue_pointer : -1,
  currently_loading: 0,
  max_parallel_load: 9,
  num_load_more: 15,
  num_per_page: 66,
  total_pages: -1,
  tobe_loaded : [],
  imgidx: -1,
  interval_time: 8000,
  interval: null,
  page_iamges_loaded: 0,

  init : function() {
    this.bind();
    this.loadHash();

    this.total_pages = Math.ceil(this.images.length / this.num_per_page);

    this.setCurrentPage(this.hash.page);

  },

  loadHash: function() {
    this.hash = {};
    var sets = window.location.hash.substr(1).split("+");
    for(var i in sets) {
      var set = sets[i].split("=");
      this.hash[set[0]] = set[1];
    }

    if(!this.hash['page']) {
      this.hash['page'] = 1;
    }
    this.hash.page = parseInt(this.hash.page);
  },

  bind : function() {
    var self = this;
    $('<div class="modal"></div>').appendTo('body');
    this.container = $('.gallery');
    this.modal = $('.modal');
    this.perload = this.container.data('perload');
    this.columns = this.container.data('columns');
    this.images = JSON.parse(decodeURIComponent(this.container.data('images')));
    
    this.prepareModal();
    this.bindImages();
    this.endlessScroll();

    $('.loadmore').on('click', function() {
      self.loadMore();
    });

    $(document).on('keyup', function(e) {
      if(e.keyCode == 27) {
        self.modal.hide();
        window.clearInterval(self.interval);
      }

      if(e.keyCode == 37) {
        self.prevImage();
      }

      if(e.keyCode == 39) {
        self.nextImage();
      }
    });

    $(".load-prev-page").on('click', function() {
      self.prevPage();
    });

    $(".load-next-page").on('click', function() {
      self.nextPage();
    });

  },

  bindImages : function() {
    var self = this;
    var $imgs = $('img[data-modalimage]:not([data-loaded])');
    $imgs.on('click', function(e) {
      self.showModal($(this).parent().data('imgidx'));
    }).data('loaded', '1');

    $imgs.tilt({
      maxTilt:        40,
      perspective:    1200,   // Transform perspective, the lower the more extreme the tilt gets.
      
      scale:          1.02,      // 2 = 200%, 1.5 = 150%, etc..
      speed:          800,    // Speed of the enter/exit transition.
      transition:     true,   // Set a transition on enter/exit.
      axis:           null,   // What axis should be disabled. Can be X or Y.
      reset:          true,   // If the tilt effect has to be reset on exit.
      glare:          true,  // Enables glare effect
      maxGlare:       0.5       // From 0 - 1.    
    });

  },

  prepareModal : function() {
    var self = this;
    var html =
    '<div id="the-modal" class="modal">' +
      '<div class="loader"> </div>' +
      '<span class="close">&times;</span>' +
      '<div class="modal-infos"><span class="modal-x">x</span> von <span class="modal-y">y</span></div>' +
      '<img class="modal-content" id="modal_image">' +
      '<div class="modal-caption"></div>' +
      '<div class="modal-control modal-control__prev">&#8249;</div>' +
      '<div class="modal-control modal-control__next">&#8250;</div>' +
      '<div class="modal-control modal-control__play play"></div>' +
      '<a href="#" target="_blank" class="modal-control modal-control__magnify">&#65291;</a>' +
    '</div>';
    $(html).appendTo('body');
    this.modal = $('#the-modal');
    this.modal_image = $('#the-modal img');
    this.modal_caption = $('#the-modal caption');
    this.modal_close = $('#the-modal .close');

    this.modal_close.on('click', function() {
      self.modal.hide();
    });

    this.modal.find('.modal-control__prev').on('click', function() {
      self.prevImage();
    });

    this.modal.find('.modal-control__next').on('click', function() {
      self.nextImage();
    });

    this.modal.find('.modal-control__play').on('click', function() {
      if(self.interval) {
        window.clearInterval(self.interval);
        $(this).addClass('play').html("");
        self.interval = null;
        return;
      }

      var callback = function() {
        if(self.modal.hasClass('loading')) {
          window.clearInterval(self.interval);
          self.interval = window.setInterval(callback);
        }
        self.nextImage();
      };

      $(this).removeClass('play').html("&#10074;&#10074;");
      self.interval = window.setInterval(callback, self.interval_time);
    });

     this.modal.on('swiperight', function(e) {
      self.prevImage();
     });

     this.modal.on('swipeleft', function(e) {
      self.nextImage();
     });

  },

  showModal : function(imgidx) {
    this.modal.show();
    this.updateModalImage(imgidx);
  },

  updateModalImage : function(imgidx) {
    if(imgidx < 0 || imgidx >= this.images.length)
      return;

    this.imgidx = imgidx;

    var self = this;
    var image = this.images[this.imgidx];
    var imgsrc = image.optimized_url;
    var bigsrc = image.original_url;
    var caption = $(".gallery").data('title');

    this.modal.addClass("loading");
    this.modal_image.on('load', function() {
      self.modal.removeClass("loading");
      self.modal_image.loaded = null;
    });
    this.modal_image.attr('src', imgsrc);
    this.modal_caption.innerHTML = caption;

    if(this.imgidx === 0) {
      this.modal.addClass('at-start');
    } else {
      this.modal.removeClass('at-start');
    }

    if(this.images.length == this.imgidx + 1) {
      this.modal.addClass('at-end');
    } else {
      this.modal.removeClass('at-end');
    }


    this.modal.find('.modal-control__magnify').attr('href', bigsrc);
    this.modal.find('.modal-x').text(this.imgidx + 1);
    this.modal.find('.modal-y').text(this.images.length);

  },

  prevImage : function() {
    this.updateModalImage(this.imgidx - 1);
  },

  nextImage : function() {
    this.updateModalImage(this.imgidx + 1);
  },

  endlessScroll : function() {
    var self = this;
    
    $(window).on('scroll', evt_buffer(function(e) {
      var cheight = $('body').innerHeight();
      var top = $(window).scrollTop() + window.innerHeight;
      var threshold = window.innerHeight / 2;
      if(cheight - top  < threshold) {
        self.loadMore(true);
      }
    }, 200, true));
  },

  loadMore : function(only_if_not_enqueued) {
    if(only_if_not_enqueued && this.enqueue_pointer + 1 < this.tobe_loaded.length) {
      return;
    }

    var load_images = [];
    var pointer_start = this.pointer;
    for(var ptr = 0; ptr < this.num_load_more; ptr++) {
      this.pointer =  pointer_start + ptr;

      if(this.pointer > this.max_pointer) {
        this.allPageLoaded();
        break;
      }

      if(this.pointer >= this.images.length) {
        this.allLoaded();
        break;
      }
      // console.log(this.pointer, this.num_per_page);
      this.images[this.pointer].loaded = true;
      
      load_images.push(this.images[this.pointer])
    }
    this.pointer++;
    // console.log("--" + load_images.length);
    this.loadImages(load_images);
  },

  clearImages : function() {
    $(".row.gallery>div").empty();
    this.col_heights = {1:0, 2:0, 3:0};
    for(var i = 0; i < this.images.length; i++) {
      this.images[i].loaded = false;
    }
  },

  loadImages : function(images) {
    var self = this;
    if(images && images.length) {
      this.enqueueImages(images);
    }
    
    if(this.max_parallel_load <= this.currently_loading) {
      return;
    }

    this.currently_loading++;
    
    var img = this.getNextEnqueued();
    if(!img) {
      this.currently_loading--;
      return;
    }

    var html = '';
    var caption = $($('[data-title]')[0]).attr('data-title');

    var imgsrc   = img.thumbnail_url;
    var modalsrc = img.optimized_url;
    var bigsrc   = img.original_url;
    var padding  = (img['dimensions']['height'] / img['dimensions']['width']) * 100;

    html = this.item_template.replace('%imgsrc%', imgsrc);
    html = html.replace('%bigimage%', bigsrc);
    html = html.replace('%modalimage%', modalsrc);
    html = html.replace('%imgidx%', img.imgidx);
    html = html.replace('%alttext%', caption);
    html = html.replace('%padding%', padding);
    
    $html = $(html);
    $html.find('img').on('load', function() {
      self.currently_loading--;
      $(this).closest('.image-holder').removeClass('loading').addClass('loaded');
      self.loadImages();
      
      self.page_images_loaded++;
      self.updatePageProgress();
    }).on('error', function() {
      self.currently_loading--;
      self.loadImages();
      
      self.page_images_loaded++;
      self.updatePageProgress();
    });

    var column = this.getSmallestColumn();

    this.addColumnHeight(column, img.dimensions.height);

    $html.appendTo($(".column-" + column));

    this.bindImages();

    if(this.enqueue_pointer + 1 == this.images.length) 
{      this.allLoaded();
    }

    if(this.max_parallel_load > this.currently_loading) {
      this.loadImages();
    }
  },

  enqueueImages : function(images) {
      this.tobe_loaded = this.tobe_loaded.concat(images);
  },

  getNextEnqueued : function() {
    if(this.tobe_loaded.length <= this.enqueue_pointer)
      return null;

    var elm = this.tobe_loaded[++this.enqueue_pointer];
    if(elm) {
      return elm;
    }
    this.enqueue_pointer--;
    return null;
  },

  updatePageProgress : function() {
    // console.log("loaded:" + this.page_images_loaded);
    var progress = 100 * Math.min(1, (this.page_images_loaded / this.num_per_page)) + '%';
    $('.progressbar').css('width', progress);
  },

  getSmallestColumn : function() {
    var c1 = parseInt(this.getColHeight(1));
    var c2 = parseInt(this.getColHeight(2));
    var c3 = parseInt(this.getColHeight(3));
    
    if(c1 <= c2 && c1 <= c3) {
      return 1;
    } else if(c2 <= c3 && c2 <= c1) {
      return 2;
    }
    return 3;
  },

  getColHeight : function(col_idx) {
      return parseInt(this.col_heights[col_idx]);
    },

  addColumnHeight : function(c_idx, height) {
    this.col_heights[c_idx] = this.getColHeight(c_idx) + height;
  },

  prevPage : function() {
    window.location.hash = '#page=' + parseInt(this.current_page - 1);
    this.setCurrentPage(parseInt(this.current_page - 1));
  },

  nextPage : function() {
    window.location.hash = '#page=' + parseInt(this.current_page + 1);
    this.setCurrentPage(parseInt(this.current_page + 1));
  },

  setCurrentPage : function(page) {
    this.current_page = page;
    this.pointer =  (this.current_page - 1) * this.num_per_page;
    this.max_pointer = Math.min(this.images.length - 1, this.current_page * this.num_per_page - 1);
    // console.log(this.max_pointer - this.pointer);
    this.page_images_loaded = 0;
    this.currently_loading = 0;

    this.clearImages();
    this.loadMore();
    
    if(this.current_page >= this.total_pages) {
      $(".load-next-page").css('display', 'none');
    } else {
      $(".load-next-page").css('display', 'inline-block');
    }

    if(this.current_page == 1) {
      $(".load-prev-page").css('display', 'none');
    } else {
      $(".load-prev-page").css('display', 'inline-block');
    }

    $(".pagex").html(page);
    $(".ofy").html(this.total_pages);

  },

  allLoaded : function() {
    if($('.alloaded').length)
      return;

    $("<div class='alloaded alert alert-success'>Keine weiteren Bilder</div>").appendTo('body');
    $('.loadmore,.load-next-page').css('display', 'none');
  },

  allPageLoaded : function() {
    $('.loadmore').css('display', 'none');
  }

};

$(document).ready(function() {
  gallery.init();
});
