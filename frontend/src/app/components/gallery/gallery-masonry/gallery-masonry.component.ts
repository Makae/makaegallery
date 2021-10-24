import {Component, OnInit} from '@angular/core';
import {GalleryService} from '../../../shared/services/gallery.service';
import {ActivatedRoute} from '@angular/router';
import {filter, map, switchMap} from 'rxjs/operators';
import {Image} from '../../../shared/models/gallery-model';
import {CapabilityService} from '../../../shared/services/capability.service';

interface DisplayedImage extends Image {
  cssClass: string;
}

interface DisplayedColumn {
  images: DisplayedImage[];
  imageCount: number;
  heightSum: number;
}

@Component({
  selector: 'app-gallery-masonry',
  templateUrl: './gallery-masonry.component.html',
  styleUrls: ['./gallery-masonry.component.scss']
})
export class GalleryMasonryComponent implements OnInit {
  private static readonly NUM_COLUMNS = 3;
  public containerWidth?: number;
  public width = 250;
  public columns: DisplayedColumn[] = [];

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
    this.columns = [];
    for (let i = 0; i < GalleryMasonryComponent.NUM_COLUMNS; i++) {
      this.columns.push({imageCount: 0, heightSum: 0, images: []})
    }

    this.activatedRoute.params.pipe(
      map(params => params['galleryId'] as string),
      filter(galleryId => !!galleryId),
      switchMap(galleryId => this.galleryService.getGallery(galleryId)),
      map(gallery => gallery.images as Image[]),
      map(images => images.map(GalleryMasonryComponent.mapImageToDisplayedImage))
    ).subscribe(images => {
      this.assignImagesToColumns(images);
    });
  }

  private assignImagesToColumns(images: DisplayedImage[]) {
    for (let image of images) {
      const columnIndex = GalleryMasonryComponent.findNextColumnIndex(this.columns);
      const column = this.columns[columnIndex];
      column.images.push(image);
      column.heightSum += image.dimensions.height
    }
  }

  private static findNextColumnIndex(columns: DisplayedColumn[]): number {
    let index = -1;
    for (let i = 0; i < columns.length; i++) {
      if(index === -1 || columns[i].heightSum < columns[index].heightSum) {
        index = i;
      }
    }
    return index;
  }
}
