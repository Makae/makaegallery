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
  perload : {},
  columns : {},
  images : {},
  pointer : 0,
  enqueue_pointer : -1,
  currently_loading: 0,
  max_parallel_load: 5,
  tobe_loaded : [],
  imgidx: -1,
  interval_time: 5000,
  interval: null,

  init : function() {
    this.bind();

    var load_images = [];

    for(var ptr = 0; ptr < this.images.length; ptr++) {
      if(this.images[ptr].doload) {
        this.pointer = ptr;
        this.images[ptr].loaded = true;
       
        load_images.push(this.images[ptr]);
        if(ptr >= this.images.length) {
          this.allLoaded();
          break;
        }
      }
    }

    this.loadImages(load_images);
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
      }

      if(e.keyCode == 37) {
        self.prevImage();
      }

      if(e.keyCode == 39) {
        self.nextImage();
      }
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
      '<div class="modal-control modal-control__play">&#9654;</div>' +
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
        $(this).html("&#9654;");
        self.interval = null;
        return;
      }

      $(this).html("&#10074;&#10074;");
      self.interval = window.setInterval(function() {
        self.nextImage();
      }, self.interval_time);
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
    var $img = $('[data-imgidx="' + this.imgidx +'"] > img');
    var imgsrc = $img.data('modalimage');
    var bigsrc = $img.data('bigimage');
    var caption = $img.data('alt');

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


    var idxloaded = Math.min(this.imgidx + 4, this.images.length - 1);
    if(!$('[data-imgidx="' + idxloaded + '"]').length) {
      this.loadMore();
    }
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
        self.loadMore();
      }
    }, 200, true));
  },

  loadMore : function() {
    var load_images = [];
    for(var i = 0; i < this.perload; i++) {
      var ptr = this.pointer + i;
      if(ptr >= this.images.length) {
        this.allLoaded();
        break;
      }
      this.images[ptr].loaded = true;
      load_images.push(this.images[ptr]);
    }

    this.loadImages(load_images);
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

    var startcolumnidx = this.getSmallestColumn();
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
    
    columnidx = (startcolumnidx + this.enqueue_pointer) % this.columns  + 1;
    
    $html = $(html);
    $html.find('img').on('load', function() {
      self.currently_loading--;
      $(this).closest('.image-holder').removeClass('loading').addClass('loaded');
      self.loadImages();
    }).on('error', function() {
      self.currently_loading--;
      self.loadImages();
    });

    $html.appendTo($(".column-" + columnidx));

    this.bindImages();

    if(this.enqueue_pointer + 1 == this.images.length) {
      this.allLoaded();
    }

    if(this.max_parallel_load > this.currently_loading) {
      this.loadImages();
    }
  },

  enqueueImages : function(images) {
    this.tobe_loaded = this.tobe_loaded.concat(images);
  },

  getNextEnqueued : function() {
    if(this.tobe_loaded.length == this.enqueue_pointer)
      return null;
    return this.tobe_loaded[++this.enqueue_pointer];
  },

  getSmallestColumn : function() {
    var c1 = $(".column-1").innerHeight();
    var c2 = $(".column-2").innerHeight();
    var c3 = $(".column-3").innerHeight();
    if(c1 < c2 && c1 < c3) {
      return 1;
    } else if(c2 < c3 && c2 < c1) {
      return 2;
    }
    return 3;
  },

  allLoaded : function() {
    if($('.alloaded').length)
      return;
    $("<div class='alloaded alert alert-success'>Keine weitern Bilder</div>").appendTo('body');
    $('.loadmore').remove();
  }

};

$(document).ready(function() {
  gallery.init();
});