@extends('layouts.publico')

@section('titulo', 'Portada · Blog de Avisos')

@section('contenido')
<div class="max-w-lg mx-auto p-8 bg-white rounded-lg shadow mt-8">

    <label class="block text-sm font-medium
               text-gray-700 mb-1">Nombre</label>
 
     <input class="w-full rounded-lg border
                @error('titulo') 
                    border-red-500 focus:ring-red-200 
                @else
                     border-marca
                @enderror
                 px-3 py-2
                 focus:border-blue-500 focus:ring-2
                 focus:ring-blue-200 outline-none my-4 ">
 
     <button class="w-full bg-marca text-white
                 font-semibold rounded-lg py-2
                 hover:bg-blue-800 transition">
         Enviar
     </button>
</div>
@endsection
