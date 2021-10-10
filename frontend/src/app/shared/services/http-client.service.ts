import {Injectable} from '@angular/core';
import {HttpClient, HttpParams} from '@angular/common/http';
import {ConfigService} from './config.service';
import {Observable} from 'rxjs';
import {PropertyModel} from '../models/property-model';

@Injectable({
  providedIn: 'root'
})
export class HttpClientService {
  private readonly basePath: string;

  public constructor(
    private readonly httpClient: HttpClient,
    private readonly configService: ConfigService
  ) {
    this.basePath = configService.config.backendUrl;
    if (!this.basePath.endsWith('/')) {
      this.basePath += '/';
    }
  }

  public httpGet<T>(path: string, params?: HttpParams): Observable<T> {
    return this.httpClient.get<T>(this.basePath + path, {
      params: params
    });
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public httpPost<T>(path: string, body: any, params?: HttpParams, responseType?: string): Observable<T> {
    return this.httpClient.post<T>(this.basePath + path, body, {
      params: params,
      responseType
    } as PropertyModel);
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public httpPut<T>(path: string, body: any, params?: HttpParams): Observable<T> {
    return this.httpClient.put<T>(this.basePath + path, body, {
      params: params
    });
  }

  public httpDelete<T>(path: string, params?: HttpParams): Observable<T> {
    return this.httpClient.delete<T>(this.basePath + path, {
      params: params
    });
  }
}
