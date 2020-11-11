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
            const $uploadWrapper = $(fileInput).closest(".upload-wrapper");

            if (fileInput.files.length === 0) {
                return;
            }
            const nonceToken = $(this).data("nonce");
            const galleryId = $(this).data('gallery-id');

            self.service.request({
                url: window.location.pathname,
                method: 'POST',
                query: {
                    ajax: 1,
                    nonce: nonceToken,
                    action: 'upload_images',
                    galleryid: galleryId
                },
                data: {
                    images: fileInput.files
                },
                success: function (request) {
                    $(fileInput).val("");

                    const data = JSON.parse(request.result.data);
                    self.clearContainers($uploadWrapper);
                    let list = [];

                    for (const key in data.result.results) {
                        if (!data.result.results.hasOwnProperty(key) || !data.result.results[key]['success']) {
                            continue;
                        }
                        let image = data.result.results[key];
                        list.push('`<strong>' + image.name + '</strong>` has been uploaded');

                    }

                    self.setNotifications($uploadWrapper, list);

                },
                error: function (request) {
                    const data = JSON.parse(request.result.data);
                    self.clearContainers($(fileInput));
                    let list = [];

                    for (const key in data.result.results) {
                        if (!data.result.results.hasOwnProperty(key) || data.result.results[key]['success']) {
                            continue;
                        }
                        let image = data.result.results[key];
                        list.push('The image `<strong>' + image.name + '</strong>` could not be uploaded.<br/>ERROR: <em>`' + image.msg + '`</em>');

                    }

                    self.setErrors($uploadWrapper, list);
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

    setErrors: function ($context, list) {
        let html = '';
        for(let key in list) {
            if(!list.hasOwnProperty(key)) {
                continue;
            }
            html += '<p class="error">' + list[key] + '</p>';
        }
        const $errorContainer = $context.find(".message-wrapper.error");
        $errorContainer.append($(html));
        $errorContainer.addClass("visible");

    },

    setNotifications: function ($context, list) {
        let html = '';
        for(let key in list) {
            if(!list.hasOwnProperty(key)) {
                continue;
            }
            html += '<p class="notification">' + list[key] + '</p>';
        }
        const $errorContainer = $context.find(".message-wrapper.notification");
        $errorContainer.append($(html));
        $errorContainer.addClass("visible");

    },

    clearContainers: function ($context) {
        const $errorContainer = $context.find(".message-wrapper.error");
        const $notificationContainer = $context.find(".message-wrapper.notification");

        $errorContainer.empty();
        $errorContainer.removeClass("visible");
        $notificationContainer.empty();
        $notificationContainer.removeClass("visible");
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
