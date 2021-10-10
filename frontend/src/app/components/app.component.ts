import {Component} from '@angular/core';
import {Observable} from "rxjs";
import {AuthService, AuthStatus} from '../shared/services/auth.service';
import {map} from 'rxjs/operators';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  public readonly loggedIn: Observable<boolean>;

  public constructor(
    public authService: AuthService
  ) {
    this.loggedIn = this.authService
      .authStatusChange()
      .pipe(
        map((authStatus) => authStatus.loggedIn)
      );
  }

}
