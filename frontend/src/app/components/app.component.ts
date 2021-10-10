import { Component } from '@angular/core';
import {BehaviorSubject, Observable} from "rxjs";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  public $loggedIn = new BehaviorSubject<boolean>(false);

  public onLoginClick(): void {

  }

  public onLogoutClick(): void {

  }
}
