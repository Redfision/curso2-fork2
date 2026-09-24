import { Component, EventEmitter, Output } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';

import { Aviso, NuevoAviso } from '../modelos/aviso';
import { AvisosService } from '../servicios/avisos.service';
import { SesionService } from '../servicios/sesion.service';

@Component({
  selector: 'app-aviso-nuevo',
  templateUrl: './aviso-nuevo.component.html',
  styleUrls: ['./aviso-nuevo.component.css']
})
export class AvisoNuevoComponent {
  @Output() creado = new EventEmitter<Aviso>();

  aviso: NuevoAviso = { titulo: '', contenido: '', categoria_id: 1 };
  errores: Record<string, string[]> = {};
  mensaje = '';
  enviando = false;

  constructor(private avisosService: AvisosService, public sesion: SesionService) { }

  guardar(): void {
    this.errores = {};
    this.mensaje = '';
    this.enviando = true;
    this.avisosService.crear(this.aviso).subscribe({
      next: creado => {
        this.enviando = false;
        this.mensaje = `201 · se creó "${creado.titulo}"`;
        this.aviso = { titulo: '', contenido: '', categoria_id: 1 };
        this.creado.emit(creado);
      },
      error: (e: HttpErrorResponse) => {
        this.enviando = false;
        if (e.status === 422) {
          this.errores = e.error.errors;
        } else {
          this.mensaje = `${e.status} · tu API no lo creó`;
        }
      }
    });
  }
}