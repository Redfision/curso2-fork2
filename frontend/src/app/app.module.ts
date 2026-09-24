import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { AuthInterceptor } from './interceptores/auth.interceptor';

import { AppComponent } from './app.component';
import { AvisosListaComponent } from './avisos-lista/avisos-lista.component';
import { FormsModule } from '@angular/forms';

import { EntrarComponent } from './entrar/entrar.component';
import { AvisoNuevoComponent } from './aviso-nuevo/aviso-nuevo.component';


@NgModule({
  declarations: [
    AppComponent,
    AvisosListaComponent,
    EntrarComponent,
    AvisoNuevoComponent
  ],
  imports: [
    BrowserModule,
     HttpClientModule,
     FormsModule
  ],
    providers: [
    { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
