var admin = {
  service : null,

  init : function() {
    this.service = new Service(3);
    this.bind();
  },

  bind : function() {
    var self = this;
    
    $("#minify-button").on('click', function(e) {
      e.preventDefault();
      $('li[data-minify-img]').each(function() {
        var imgid = $(this).data('minify-img');
        var $this = $(this);
        $this.addClass("start");
        admin.minifyImg(imgid, function() {
          $this.removeClass("start").addClass("done");
        });
      })
    });

    $("#clear-button").on('click', function(e) {
      e.preventDefault();
      self.service.request({
          url : window.location.pathname,
          method : 'POST',
          data : {ajax: true, action: 'clear_minified'},
          success : function(request) {
          },
          error : function() {

          }
        });
    });

    $(".manual-trigger[data-minify-img]").on('click', function(e) {
        e.preventDefault();
        var imgid = $(this).data('minify-img');
        var $this = $(this);
        $this.closest('li').addClass("start");
        admin.minifyImg(imgid, function() {
          $this.closest('li').removeClass("start").addClass("done");
        });
    });
  },

  minifyImg : function(imgid, success, error) {
    var self = this;
    self.service.request({
      url : window.location.pathname,
      method : 'POST',
      data : {imageid: imgid, ajax: true, action: 'minify_image'},
      success : function(request) {
        success(request);
      },
      error : function() {
        error(request);
      }
    });
  },

};

$(document).ready(function() {
  admin.init();
});