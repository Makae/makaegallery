import {Injectable} from '@angular/core';
import {HttpClientService} from "./http-client.service";
import {Observable} from "rxjs";
import {HttpParams} from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  public constructor(private httpClientService: HttpClientService) {

  }

  public login(name: string, password: string): Observable<boolean> {
    return this.httpClientService.httpGet<boolean>(`login`, new HttpParams({
      fromObject: {
        name,
        password
      }
    }));
  }

  private checkLogin(): Observable<boolean> {
    return this.httpClientService.httpGet<boolean>('login-status');
  }
}
