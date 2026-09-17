import { Component, OnInit } from '@angular/core';

import { Aviso } from '../modelos/aviso';
import { AvisosService } from '../servicios/avisos.service';

@Component({
  selector: 'app-avisos-lista',
  templateUrl: './avisos-lista.component.html',
  styleUrls: ['./avisos-lista.component.css']
})
export class AvisosListaComponent implements OnInit {
  avisos: Aviso[] = [];
  cargando = true;
  error = '';

  constructor(private avisosService: AvisosService) { }

  ngOnInit(): void {
    this.cargar();
  }

  cargar(): void {
    this.cargando = true;
    this.avisosService.listar().subscribe({
      next: avisos => {
        this.avisos = avisos;
        this.cargando = false;
      },
      error: () => {
        this.error = 'No pude hablar con tu API. Revisa que composer run dev siga corriendo.';
        this.cargando = false;
      }
    });
  }
}