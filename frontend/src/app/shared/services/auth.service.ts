import {Injectable} from '@angular/core';
import {HttpClientService} from "./http-client.service";
import {BehaviorSubject, Observable} from "rxjs";
import {distinctUntilChanged, tap} from 'rxjs/operators';
import {HttpResponse, HttpStatusCode} from '@angular/common/http';
import {AuthStatus} from '../models/auth-status-model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private authStatusSubject = new BehaviorSubject<AuthStatus>({loggedIn: false})

  public constructor(private httpClientService: HttpClientService) {
    this.httpClientService.httpGet<boolean>(`auth/status`).subscribe({
      complete: () => {
        this.authStatusSubject.next({loggedIn: false});
      }
    });
  }

  public login(name: string, password: string): Observable<AuthStatus> {
    this.httpClientService.httpPostResponse(
      `auth/login`,
      {name, password}
    ).subscribe({
      next: (response) => {
        const resp = response as unknown as HttpResponse<any>;
        console.log(resp.headers);
        debugger;
      },
      complete: () => {
        debugger;
        this.authStatusSubject.next({loggedIn: true});
      },
      error: (response) => {
        debugger;
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
    return this.httpClientService.httpGet<void>(`auth/logout`).pipe(
      tap(() => {
        this.authStatusSubject.next({loggedIn: false});
      }));
  }
}
