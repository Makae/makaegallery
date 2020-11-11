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
                    const data = JSON.parse(request.result.data);
                    self.updateContainers($uploadWrapper, data.result.results);

                    $(fileInput).val("");

                },
                error: function (request) {
                    const data = JSON.parse(request.result.data);
                    self.updateContainers($uploadWrapper, data.result.results);
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

    updateContainers: function ($context, results) {
        this.clearContainers($context);

        const successMessages = [];
        const errorMessages = [];

        for (const key in results) {
            if (!results.hasOwnProperty(key)) {
                continue;
            }
            let image = results[key];

            if (results[key]['success']) {
                successMessages.push('<strong>' + image.name + '</strong> has been uploaded');
            } else {
                errorMessages.push('The image <strong>' + image.name + '</strong> could not be uploaded.<br/>ERROR: <em>`' + image.msg + '`</em>');
            }
        }

        this.setErrors($context, errorMessages);
        this.setNotifications($context, successMessages);
    },

    setMessages: function ($container, clazz, messages) {
        if(messages.length === 0) {
            return;
        }
        let html = '<span class="close-button">X</span>';
        for (let key in messages) {
            if (!messages.hasOwnProperty(key)) {
                continue;
            }
            html += '<p class="' + clazz + '">' + messages[key] + '</p>';
        }
        const $html = $(html);

        var self = this;
        $($html.get(0)).on('click', () => {
            self.clearContainer($container.parent(), clazz);
        })
        $container.append($html);
        $container.addClass("visible");
    },

    setErrors: function ($context, messages) {
        this.setMessages($context.find(".message-wrapper.error"), "error", messages);
    },

    setNotifications: function ($context, messages) {
        this.setMessages($context.find(".message-wrapper.notification"), "notification", messages)
    },

    clearContainers: function ($context) {
        this.clearContainer($context, "error");
        this.clearContainer($context, "notification");
    },

    clearContainer: function ($context, clazz) {
        const $container = $context.find(".message-wrapper." + clazz);

        $container.empty();
        $container.removeClass("visible");
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
