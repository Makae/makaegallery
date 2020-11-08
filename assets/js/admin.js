var admin = {
    service: null,

    init: function () {
        this.service = new Service(3);
        this.bind();
    },

    bind: function () {
        var self = this;

        $("#minify-button").on('click', function (e) {
            e.preventDefault();
            $('li[data-minify-img]').each(function () {
                var imgid = $(this).data('minify-img');
                var $this = $(this);
                $this.addClass("start");
                admin.minifyImg(imgid, function () {
                    $this.removeClass("start").addClass("done");
                });
            })
        });

        $(".minify-gallery-button").on('click', function (e) {
            e.preventDefault();
            $(this).closest('.gallery-wrapper').find('li[data-minify-img]').each(function () {
                var imgid = $(this).data('minify-img');
                var $this = $(this);
                $this.addClass("start");
                admin.minifyImg(imgid, function () {
                    $this.removeClass("start").addClass("done");
                });
            })
        });

        $("#clear-button").on('click', function (e) {
            e.preventDefault();
            self.service.request({
                url: window.location.pathname,
                method: 'GET',
                data: {ajax: true, action: 'clear_minified'},
                success: function (request) {
                    $('.processing-progress li.done').removeClass('done');
                },
                error: function () {

                }
            });
        });

        $(".upload-image-button").on('click', function (e) {
            e.preventDefault();

            const fileInput = $(this).closest("form").find("input[type='file']")[0];
            const $errorContainer = $(fileInput).closest(".upload-wrapper").find(".error-wrapper");
            if (fileInput.files.length === 0) {
                return;
            }
            const nonceToken = $(this).data("nonce");
            const galleryid = $(this).data('gallery-id');

            self.service.request({
                url: window.location.pathname,
                method: 'POST',
                query: {
                    ajax: 1,
                    nonce: nonceToken,
                    action: 'upload_images',
                    galleryid: galleryid
                },
                data: {
                    images: fileInput.files
                },
                success: function (request) {
                    $errorContainer.empty();
                    $errorContainer.removeClass("visible");

                },
                error: function (request) {
                    var data = JSON.parse(request.result.data);
                    $errorContainer.empty();
                    var errors = "";
                    for (var key in data.result.results) {
                        if (!data.result.hasOwnProperty(key) || data.result[key]['success']) {
                            continue;
                        }
                        let image = data.result.results[key];
                        errors += '<p class="error">The image `<strong>' + image.name + '</strong>` could not be uploaded.<br/>ERROR: <em>`' + image.msg + '`</em></p>'
                    }
                    $errorContainer.html(errors)
                    $errorContainer.addClass("visible");
                }
            });
        });


        $(".clear-gallery-button").on('click', function (e) {
            e.preventDefault();
            var galleryid = $(this).data('gallery-id');
            var _this = this;
            self.service.request({
                url: window.location.pathname,
                method: 'GET',
                data: {ajax: true, action: 'clear_minified', galleryid: galleryid},
                success: function (request) {
                    $(_this).closest('.gallery-wrapper').find('li.done').removeClass('done');
                },
                error: function () {

                }
            });
        });

        $(".manual-trigger[data-minify-img]").on('click', function (e) {
            e.preventDefault();
            var imgid = $(this).data('minify-img');
            var $this = $(this);
            $this.closest('li').addClass("start");
            admin.minifyImg(imgid, function () {
                $this.closest('li').removeClass("start").addClass("done");
            });
        });
    },

    minifyImg: function (imgid, success, error) {
        var self = this;
        self.service.request({
            url: window.location.pathname,
            method: 'GET',
            data: {imageid: imgid, ajax: true, action: 'minify_image'},
            success: function (request) {
                success(request);
            },
            error: function () {
                error(request);
            }
        });
    },

};

$(document).ready(function () {
    admin.init();
});
