var admin = {
    container: null,
    service: null,
    backend_api_url: null,
    init: function () {
        this.service = new Service(3);
        this.bind();
    },

    bind: function () {
        let self = this;
        this.container = $('.admin');
        this.backend_api_url = this.container.data('apiurl');

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
            admin.clearAllGalleries();
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
            self.uploadImage(galleryId, fileInput, nonceToken, (request) => {
                const data = JSON.parse(request.result.data);
                self.updateContainers($uploadWrapper, data.result.results);
                $(fileInput).val("");
            }, (request) => {
                const data = JSON.parse(request.result.data);
                self.updateContainers($uploadWrapper, data.result.results);
            });
        });


        $(".clear-gallery-button").on('click', function (e) {
            e.preventDefault();
            var galleryid = $(this).data('gallery-id');
            var _this = this;

            self.clearGallery(galleryid, (request) => {
                $(_this).closest('.gallery-wrapper').find('li.done').removeClass('done');
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

    clearGallery: function (galleryId, successCbk) {
        var self = this;
        self.service.request({
            url: this.backend_api_url + '/gallery/' + galleryId + '/clear',
            method: 'GET',
            success: successCbk,
            error: function () {
                error(request);
            }
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
        if (messages.length === 0) {
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
            url: this.backend_api_url + '/image/' + imgid + '/minify',
            method: 'GET',
            success: function (request) {
                success(request);
            },
            error: function () {
                error(request);
            }
        });
    },

    clearAllGalleries: function () {
        var self = this;
        self.service.request({
            url: this.backend_api_url + '/gallery/clear',
            method: 'GET',
            success: function (request) {
                success(request);
            },
            error: function () {
                error(request);
            }
        });
    },

    uploadImage: function (galleryId, fileInput, nonceToken, successCbk, errorCbk) {
        var self = this;
        self.service.request({
            url: this.backend_api_url + '/gallery/' + galleryId + '/image/',
            method: 'POST',
            query: {
                nonce: nonceToken,
            },
            data: {
                images: fileInput.files
            },
            success: successCbk,
            error: errorCbk
        });
    }
};

$(document).ready(function () {
    admin.init();
});
