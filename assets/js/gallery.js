var gallery = {
  item_template : 
        '<div class="image-holder">' +
            '<img src="%imgsrc%" data-modalimage="%modalimage%" alt="%alttext%" />' +
        '</div>',

  gallery : null,
  modal : null,
  modal_image: null,
  modal_caption: null,
  modal_close: null,
  perload : {},
  columns : {},
  images : {},
  pointer : 0,

  init : function() {
    this.bind();

    for(var i = 0; i < this.images.length; i++) {
      if(!this.images[i].loaded) {
        this.pointer = i;
        break;
      }
    }
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
  },

  bindImages : function() {
    var self = this;
    $('img[data-modalimage]:not([data-loaded])').on('click', function(e) {
      self.showModal($(this).data('modalimage'), $(this).data('alt'));
    }).data('loaded', '1');
  },

  prepareModal : function() {
    var self = this;
    var html =
    '<div id="the-modal" class="modal">' +
      '<span class="close">&times;</span>' +
      '<img class="modal-content" id="modal_image">' +
      '<div class="modal-caption"></div>' +
      '<div class="modal-prev">Zurück</div>' +
      '<div class="modal-next">Weiter</div>' +
    '</div>';
    $(html).appendTo('body');
    this.modal = $('#the-modal');
    this.modal_image = $('#the-modal img');
    this.modal_caption = $('#the-modal caption');
    this.modal_close = $('#the-modal .close');
    this.modal_close.on('click', function() {
      self.modal.hide();
    });
  },

  showModal : function(imgsrc, caption) {
    this.modal.show();
    this.modal_image.attr('src', imgsrc);
    this.modal_caption.innerHTML = caption;
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
    var html = '';
    var caption = $($('[data-modalimage]')[0]).attr('alt');

    startcolumnidx = this.getSmallestColumn();
    for(var i = 0; i < images.length; i++) {
      var imgsrc = images[i].thumbnail_url;
      var modalsrc = images[i].optimized_url;
     
      html = this.item_template.replace('%imgsrc%', imgsrc);
      html = html.replace('%modalimage%', modalsrc);
      html = html.replace('%alttext%', caption);
      
      columnidx = (startcolumnidx + i) % this.columns + 1;
      $(html).appendTo($(".column-" + columnidx));
      
      this.pointer++;
    }

    this.bindImages();
    if(this.pointer + 1 == this.images.length) {
      this.allLoaded();
    }
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
  }

};

$(document).ready(function() {
  gallery.init();
});