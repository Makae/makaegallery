let masonry = {
    $container: null,
    $grid: null,
    $masonry: null,
    service: null,
    backend_api_url: null,

    item_template:
        '<div class="grid-item %class%" data-imgidx="%image_idx%">' +
        '   <div class="image-holder" ' +
        '       style="background-image: url(\'%thumbnail_src%\')" ' +
        '       data-optimized-src="%optimized_src%" ' +
        '       data-original-src="%original_src%" />' +
        '</div>',

    init: function () {
        let self = this;
        this.service = new Service(-1);
        this.$container = $('.masonry');
        this.backend_api_url = this.$container.data('apiurl');
        this.gallery_id = this.$container.data('galleryid');

        this.$grid = $('.grid');
        this.$masonry = this.$grid.masonry({
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer',
            rowHeight: '.grid-sizer'
        });

        this.service.request({
            url: self.backend_api_url + '/gallery/' + this.gallery_id,
            method: 'GET',
            success: function (request) {
                const data = JSON.parse(request.result.data);
                self.images = data.images;
                for (var i = 0; i < self.images.length; i++) {
                    self.images[i].idx = i;
                }
                self.bind();
            },
            error: function () {

            }
        });


    },

    setGridOptions: function(columns, columnHeight) {
        document.documentElement.style.setProperty('--grid-columns', columns);
        document.documentElement.style.setProperty('--grid-column-height', columnHeight + "px");
        this.$grid.masonry();
    },

    bind: function () {
        let self = this;
        let $images = $(this.renderImages(this.images));
        this.$grid.append($images)
            .masonry('appended', $images);
        $(document).on('keyup', (event) => {
            // 191 == "§" on Swiss German keyboard (left of digit 1)
            if(event.keyCode !== 191) {
                return;
            }
            self.uploader.showUploadModal();
        })
    },

    renderImages: function (images) {
        let renderedImages = [];
        for (let i = 0; i < images.length; i++) {
            renderedImages.push(this.renderImage(images[i]));
        }
        return renderedImages;
    },

    renderImage: function (image) {
        let thumbnailUrl = image.thumbnail_url;
        let optimizedUrl = image.optimized_url;
        let originalUrl = image.original_url;
        let aspectRatio = image['dimensions']['width'] / image['dimensions']['height'];

        let html = this.item_template.replace('%thumbnail_src%', thumbnailUrl);
        html = html.replace('%original_src%', originalUrl);
        html = html.replace('%optimized_src%', optimizedUrl);
        html = html.replace('%image_idx%', image.idx);

        let gridArea = '';
        if(aspectRatio <= 0.6) {
            gridArea = 'grid-area-1to3'
        } else if(aspectRatio <= 0.8) {
            gridArea = 'grid-area-1to2'
        } else {
            gridArea = 'grid-area-1to1'
        }
        html = html.replace('%class%', gridArea);

        return $(html)[0];
    },

    uploader: {
        showUploadModal: () => {
            $('#uploadPhotoModal').modal('show');
        }
    }

};

$(document).ready(function () {
    masonry.init();
});
