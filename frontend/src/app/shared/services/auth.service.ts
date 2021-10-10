import {Injectable} from '@angular/core';
import {HttpClientService} from "./http-client.service";
import {BehaviorSubject, Observable} from "rxjs";
import {distinctUntilChanged, tap} from 'rxjs/operators';
import {HttpStatusCode} from '@angular/common/http';

export interface AuthStatus {
  loggedIn: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private authStatusSubject = new BehaviorSubject<AuthStatus>({loggedIn: false})

  public constructor(private httpClientService: HttpClientService) {
  }

  public login(name: string, password: string): Observable<AuthStatus> {
    this.httpClientService.httpPost<boolean>(
      `auth/login`,
      {name, password}
    ).subscribe({
      next: () => {
        this.authStatusSubject.next({loggedIn: true});
      },
      error: (response) => {
        if (response.status === HttpStatusCode.Unauthorized) {
          this.authStatusSubject.next({loggedIn: true});
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
