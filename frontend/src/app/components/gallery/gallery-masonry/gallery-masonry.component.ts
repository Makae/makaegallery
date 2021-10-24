import {Component, OnInit} from '@angular/core';
import {GalleryService} from '../../../shared/services/gallery.service';
import {ActivatedRoute} from '@angular/router';
import {filter, map, switchMap} from 'rxjs/operators';
import {Dimensions, Image} from '../../../shared/models/gallery-model';
import {CapabilityService} from '../../../shared/services/capability.service';

interface DisplayedImage extends Image {
  cssClass: string;
}

@Component({
  selector: 'app-gallery-masonry',
  templateUrl: './gallery-masonry.component.html',
  styleUrls: ['./gallery-masonry.component.scss']
})
export class GalleryMasonryComponent implements OnInit {
  public images?: DisplayedImage[];
  public containerWidth?: number;
  public width = 250;

  public constructor(
    public galleryService: GalleryService,
    public activatedRoute: ActivatedRoute,
    public capabilityService: CapabilityService
  ) {
    capabilityService.screenDimensionChanged().subscribe((screenDimensions) => {
      //this.containerWidth = Math.ceil(screenDimensions.width / this.width) * this.width;
      //console.log(screenDimensions.width, this.width, this.containerWidth);
    });
  }

  /*  private static mapImageToMasonryImage(image: Image): IMasonryGalleryImage {
      return {
        imageUrl: image.thumbnail_url
      }
    }*/
  private static mapImageToDisplayedImage(image: Image): DisplayedImage {
    return {
      ...image,
      cssClass: GalleryMasonryComponent.getCssClassForAspectRatio(image.dimensions.width / image.dimensions.height)
    }
  }

  private static getCssClassForAspectRatio(aspectRatio: number): string {
    if (aspectRatio <= 0.6) {
      return 'grid-area-1to3'
    } else if (aspectRatio <= 0.8) {
      return 'grid-area-1to2'
    } else {
      return 'grid-area-1to1'
    }
  }

  public ngOnInit(): void {
    this.activatedRoute.params.pipe(
      map(params => params['galleryId'] as string),
      filter(galleryId => !!galleryId),
      switchMap(galleryId => this.galleryService.getGallery(galleryId)),
      map(gallery => gallery.images),
      // map(images => images.map(GalleryMasonryComponent.mapImageToMasonryImage))
      map(images => images?.map(GalleryMasonryComponent.mapImageToDisplayedImage))
    ).subscribe(images => {
      this.images = images;
    });
  }
}
