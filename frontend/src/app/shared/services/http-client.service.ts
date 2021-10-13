import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders, HttpParams} from '@angular/common/http';
import {ConfigService} from './config.service';
import {Observable} from 'rxjs';
import {PropertyModel} from '../models/property-model';

interface BasicAuth {
  name: string;
  password: string;
}

@Injectable({
  providedIn: 'root'
})
export class HttpClientService {
  private readonly basePath: string;
  private basicAuth?: BasicAuth;


  public constructor(
    private readonly httpClient: HttpClient,
    private readonly configService: ConfigService
  ) {
    this.basePath = configService.config.backendUrl;
    if (!this.basePath.endsWith('/')) {
      this.basePath += '/';
    }
  }

  public setBasicAuthHeaders(name: string, password: string) {
    this.basicAuth = {
      name,
      password
    };
  }

  public clearBasicAuthHeaders() {
    this.basicAuth = undefined;
  }

  public httpGet<T>(path: string, params?: HttpParams): Observable<T> {
    return this.httpClient.get<T>(this.basePath + path, {
      params: params,
      headers: this.addBasicAuthHeader()
    });
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public httpPost<T>(path: string, body: any, params?: HttpParams, responseType?: string): Observable<T> {
    return this.httpClient.post<T>(this.basePath + path, body, {
      params: params,
      headers: this.addBasicAuthHeader(),
      responseType
    } as PropertyModel);
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public httpPostResponse(path: string, body: any, params?: HttpParams): Observable<Object> {
    return this.httpClient.post(this.basePath + path, body, {
      params: params,
      headers: this.addBasicAuthHeader(),
      observe: 'response'
    } as PropertyModel);
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public httpPut<T>(path: string, body: any, params?: HttpParams): Observable<T> {
    return this.httpClient.put<T>(this.basePath + path, body, {
      params: params,
      headers: this.addBasicAuthHeader()
    });
  }

  public httpDelete<T>(path: string, params?: HttpParams): Observable<T> {
    return this.httpClient.delete<T>(this.basePath + path, {
      params: params,
      headers: this.addBasicAuthHeader()
    });
  }

  private addBasicAuthHeader(): HttpHeaders {
    const headers = new HttpHeaders();
    const token = btoa(`${this.basicAuth?.name}:${this.basicAuth?.password}`);
    if (this.basicAuth) {
      headers.append('Authorization', `Basic ${token}`);
    }
    return headers;
  }
}
