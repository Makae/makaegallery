import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {GalleryListingComponent} from './components/gallery/gallery-listing/gallery-listing.component';

const routes: Routes = [
  {path: '', component: GalleryListingComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
