import {Component, OnInit} from '@angular/core';
import {GalleryService} from '../../../shared/services/gallery.service';
import {Gallery} from '../../../shared/models/gallery-model';
import {AuthService} from '../../../shared/services/auth.service';
import {distinctUntilChanged, filter, map} from 'rxjs/operators';

@Component({
  selector: 'app-gallery-listing',
  templateUrl: './gallery-listing.component.html',
  styleUrls: ['./gallery-listing.component.scss']
})
export class GalleryListingComponent implements OnInit {

  public galleries: Gallery[] = [];

  public constructor(
    public authService: AuthService,
    public galleryService: GalleryService
  ) {
  }

  public ngOnInit(): void {
    this.authService.authStatusChange().pipe(
      map(status => status.loggedIn),
      filter(loggedIn => loggedIn === undefined || loggedIn === true),
      distinctUntilChanged()
    ).subscribe(() => {
      this.galleryService.getGalleries().subscribe((galleries) => {
          this.galleries = galleries;
        }
      )
    })

  }

}
