import {Injectable} from '@angular/core';
import {HttpClientService} from "./http-client.service";
import {BehaviorSubject, EMPTY, Observable} from "rxjs";
import {distinctUntilChanged} from 'rxjs/operators';
import {HttpErrorResponse, HttpStatusCode} from '@angular/common/http';
import {AuthStatus} from '../models/auth-status-model';


@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private authStatusSubject = new BehaviorSubject<AuthStatus>({})

  public constructor(private httpClientService: HttpClientService) {
    this.httpClientService.httpGet<boolean>(`auth/status`).subscribe({
      complete: () => {
        this.authStatusSubject.next({loggedIn: true});
      },
      error: (error: HttpErrorResponse) => {
        if (error.status === HttpStatusCode.Unauthorized ||error.status === HttpStatusCode.Forbidden) {
          this.authStatusSubject.next({loggedIn: false});
        }
      }
    });
  }

  public login(name: string, password: string): Observable<AuthStatus> {
    this.httpClientService.setBasicAuthHeaders(name, password);
    this.httpClientService.httpGet(
      `auth/status`
    ).subscribe({
      complete: () => {
        this.authStatusSubject.next({loggedIn: true});
      },
      error: (response) => {
        if (response.status === HttpStatusCode.Unauthorized) {
          this.authStatusSubject.next({loggedIn: false});
        }
      }
    });
    return this.authStatusChange();
  }

  public authStatusChange(): Observable<AuthStatus> {
    return this.authStatusSubject.asObservable().pipe(
      distinctUntilChanged()
    );
  }

  public logout(): Observable<void> {
    this.httpClientService.clearBasicAuthHeaders();
    return EMPTY;
  }
}