import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

import { Aviso } from '../modelos/aviso';

@Injectable({
  providedIn: 'root'
})
export class AvisosService {

  constructor(private http: HttpClient) { }

  listar(): Observable<Aviso[]> {
    return this.http.get<{ data: Aviso[] }>('/api/avisos').pipe(
      map(respuesta => respuesta.data)
    );
  }
}